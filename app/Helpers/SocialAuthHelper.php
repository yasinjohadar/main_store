<?php

if (! function_exists('social_login_enabled')) {
    function social_login_enabled(string $provider): bool
    {
        $config = config("services.{$provider}");

        return ! empty($config['client_id']) && ! empty($config['client_secret']);
    }
}
