<?php

namespace BeyondCode\HeloLaravel;

use Illuminate\Contracts\Mail\Mailer as MailerContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class HeloLaravelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if (!$this->app['config']['app.debug']) {
            return;
        }
        $instance = app()->make(Mailer::class);

        Mail::swap($instance);

        app()->instance(MailerContract::class, $instance);
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->singleton(Mailer::class, function ($app) {
            $config = $app->make('config')->get('mail');

            // Once we have create the mailer instance, we will set a container instance
            // on the mailer. This allows us to resolve mailer classes via containers
            // for maximum testability on said classes instead of passing Closures.
            $mailer = new Mailer(
                $app['view'], $app['swift.mailer'], $app['events']
            );

            if ($app->bound('queue')) {
                $mailer->setQueue($app['queue']);
            }

            // Next we will set all of the global addresses on this mailer, which allows
            // for easy unification of all "from" addresses as well as easy debugging
            // of sent messages since they get be sent into a single email address.
            foreach (['from', 'reply_to', 'to'] as $type) {
                $this->setGlobalAddress($mailer, $config, $type);
            }

            return $mailer;
        });
    }

    /**
     * Set a global address on the mailer by type.
     *
     * @param \Illuminate\Mail\Mailer $mailer
     * @param array $config
     * @param string $type
     * @return void
     */
    protected function setGlobalAddress($mailer, array $config, $type)
    {
        $address = Arr::get($config, $type);

        if (is_array($address) && isset($address['address'])) {
            $mailer->{'always' . Str::studly($type)}($address['address'], $address['name']);
        }
    }
}
