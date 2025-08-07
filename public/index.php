<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

error_log("INDEX: ");
error_log($_SERVER['HTTP_AUTHORIZATION']);
error_log(print_r($_SERVER));

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
