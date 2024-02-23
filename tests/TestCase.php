<?php

namespace BeyondCode\HeloLaravel\Tests;

use BeyondCode\HeloLaravel\HeloLaravelServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            HeloLaravelServiceProvider::class,
        ];
    }
}
