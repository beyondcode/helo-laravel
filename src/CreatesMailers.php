<?php

namespace BeyondCode\HeloLaravel;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait CreatesMailers
{
    protected function createGeneralLaravelMailer($app)
    {
        $defaultDriver = $app['mail.manager']->getDefaultDriver();
        $config = $this->getConfig($defaultDriver);

        // We get Symfony Transport from Laravel 10+ mailer
        $symfonyTransport = $app['mailer']->getSymfonyTransport();

        // Once we have create the mailer instance, we will set a container instance
        // on the mailer. This allows us to resolve mailer classes via containers
        // for maximum testability on said classes instead of passing Closures.
        $mailer = new GeneralLaravelMailer(
            'smtp',
            $app['view'],
            $symfonyTransport,
            $app['events']
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
     * @param array                   $config
     * @param string                  $type
     *
     * @return void
     */
    protected function setGlobalAddress($mailer, array $config, $type)
    {
        $address = Arr::get($config, $type, $this->app['config']['mail.'.$type]);

        if (is_array($address) && isset($address['address'])) {
            $mailer->{'always'.Str::studly($type)}($address['address'], $address['name']);
        }
    }
}
