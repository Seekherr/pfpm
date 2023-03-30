<?php

declare(strict_types=1);

use Composer\Autoload\ClassLoader;

require_once __DIR__ . '/../composer/vendor/autoload.php';

$classLoader = new ClassLoader();
$classLoader->add('Seeker\pfpm', __DIR__, true);
$classLoader->register();