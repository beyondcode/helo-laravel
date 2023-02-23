<?php

namespace BeyondCode\HeloLaravel;

use Illuminate\Contracts\Mail\Mailer as MailerContract;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Swift_Mailer;

class HeloLaravelServiceProvider extends ServiceProvider
{
    use CreatesMailers;

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningUnitTests() || !$this->app['config']['helo.is_enabled']) {
            return;
        }


        $this->bootMailable();

        if ($this->app->runningInConsole()) {
            View::addNamespace('helo', __DIR__ . '/../resources/views');
        }
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

            $this->publishes([
                __DIR__.'/../config/helo.php' => base_path('config/helo.php'),
            ], 'config');
        }

        $this->mergeConfigFrom(__DIR__.'/../config/helo.php', 'helo');

        $this->app->singleton(Mailer::class, function ($app) {
            $version = $this->version($app);

            if ($version < 7) {
                return $this->createLaravel6Mailer($app);
            }
            if ($version < 9) {
                return $this->createLaravel7Mailer($app);
            }

            return $this->createLaravel9Mailer($app);
        });
    }

    protected function bootMailable()
    {
        if ($this->version() < 10) {
            $instance = app()->make(Mailer::class);

            Mail::swap($instance);

            $this->app->instance(MailerContract::class, $instance);
        } else {
            Mail::swap(new MailManager($this->app));
        }
    }

    private function version($app = null): int
    {
        if (! $app) {
            $app = $this->app;
        }

        return (int) collect(explode('.', $app->version()))->first();
    }
}
