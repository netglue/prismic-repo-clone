<?php
/** @noinspection ALL */
// phpcs:ignoreFile

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Laminas\ServiceManager\ServiceManager;

return (static function (): ServiceManager {
    $config = require __DIR__ . '/config.php';
    $dependencies = $config['dependencies'] ?? [];
    $dependencies['services']['config'] = $config;

    return new ServiceManager($dependencies);
})();
