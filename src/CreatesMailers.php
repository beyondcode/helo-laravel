<?php

namespace BeyondCode\HeloLaravel;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Swift_Mailer;

trait CreatesMailers
{
    protected function createLaravel6Mailer($app)
    {
        $config = $this->getConfig();

        // Once we have create the mailer instance, we will set a container instance
        // on the mailer. This allows us to resolve mailer classes via containers
        // for maximum testability on said classes instead of passing Closures.
        $mailer = new Mailer(
            $app['view'],
            $app['swift.mailer'],
            $app['events']
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
        $mailer = new Laravel7Mailer(
            'smtp',
            $app['view'],
            $swiftMailer,
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

    protected function createLaravel8Mailer($app)
    {
        $defaultDriver = $app['mail.manager']->getDefaultDriver();
        $config = $this->getConfig($defaultDriver);

        $swiftMailer = new Swift_Mailer($app['mail.manager']->createTransport($config));

        // Once we have create the mailer instance, we will set a container instance
        // on the mailer. This allows us to resolve mailer classes via containers
        // for maximum testability on said classes instead of passing Closures.
        $mailer = new Laravel8Mailer(
            'smtp',
            $app['view'],
            $swiftMailer,
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

    protected function createLaravel9Mailer($app)
    {
        $defaultDriver = $app['mail.manager']->getDefaultDriver();
        $config = $this->getConfig($defaultDriver);

        // We get Symfony Transport from Laravel 9 mailer
        if (version_compare(app()->version(), '10.0.0', '<')) {
            $symfonyTransport = $app['mail.manager']->getSymfonyTransport();
        } else {
            $symfonyTransport = $app['mailer']->getSymfonyTransport();
        }

        // Once we have create the mailer instance, we will set a container instance
        // on the mailer. This allows us to resolve mailer classes via containers
        // for maximum testability on said classes instead of passing Closures.
        $mailer = new Laravel9Mailer(
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
        if (version_compare(app()->version(), '7.0.0', '<')) {
            $address = Arr::get($config, $type);
        } else {
            $address = Arr::get($config, $type, $this->app['config']['mail.'.$type]);
        }

        if (is_array($address) && isset($address['address'])) {
            $mailer->{'always'.Str::studly($type)}($address['address'], $address['name']);
        }
    }
}
