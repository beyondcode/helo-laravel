<?php

return [
    /**
     * This flag determines if you want to send additional SMTP headers that will contain the debug output that HELO
     * makes use of.
     * By default, this is only the case when your application is in debug mode.
     */
    'is_enabled' => env('HELO_ENABLED', env('APP_DEBUG')),
];
