<?php

namespace BeyondCode\HeloLaravel;

use Illuminate\Contracts\Mail\Mailer as MailerContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Swift_Mailer;

class HeloLaravelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if (!$this->app['config']['helo.is_enabled']) {
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
        if ($this->app->runningInConsole()) {
            $this->commands([
                TestMailCommand::class,
            ]);

            View::addNamespace('helo', __DIR__ . '/../resources/views');

            $this->publishes([
                __DIR__.'/../config/helo.php' => base_path('config/helo.php'),
            ], 'config');
        }

        
        $this->mergeConfigFrom(__DIR__.'/../config/helo.php', 'helo');

        $this->app->singleton(Mailer::class, function ($app) {
            if (version_compare($app->version(), '7.0.0', '<')) {
                return $this->createLaravel6Mailer($app);
            }

            return $this->createLaravel7Mailer($app);
        });
    }

    protected function createLaravel6Mailer($app)
    {
        $config = $this->getConfig();

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
    }

    protected function createLaravel7Mailer($app)
    {
        $defaultDriver = $app['mail.manager']->getDefaultDriver();
        $config = $this->getConfig($defaultDriver);

        // Laravel 7 no longer bindes the swift.mailer:
        $swiftMailer = new Swift_Mailer($app['mail.manager']->createTransport($config));

        // Once we have create the mailer instance, we will set a container instance
        // on the mailer. This allows us to resolve mailer classes via containers
        // for maximum testability on said classes instead of passing Closures.
        $mailer = new Mailer(
            'smtp', $app['view'], $swiftMailer, $app['events']
        );

        if ($app->bound('queue')) {
            $mailer->setQueue($app['queue']);
        }

        // Next we will set all of the global addresses on this mailer, which allows
        // for easy unification of all "from" addresses as well as easy debugging
        // of sent messages since they get be sent into a single email address.
        foreach (['from', 'reply_to', 'to', 'return_path'] as $type) {
            $this->setGlobalAddress($mailer, $config, $type);
        }

        return $mailer;
    }

    protected function getConfig($name = 'smtp')
    {
        return $this->app['config']['mail.driver']
            ? $this->app['config']['mail']
            : $this->app['config']["mail.mailers.{$name}"];
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
        if (version_compare(app()->version(), '7.0.0', '<')) {
            $address = Arr::get($config, $type);
        } else {
            $address = Arr::get($config, $type, $this->app['config']['mail.'.$type]);
        }

        if (is_array($address) && isset($address['address'])) {
            $mailer->{'always' . Str::studly($type)}($address['address'], $address['name']);
        }
    }
}
