<?php

declare(strict_types=1);

/** @return array<string, mixed> */

use Dotenv\Dotenv;
use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\PhpFileProvider;
use Laminas\Diactoros;

return (static function (): array {
    // Load Env vars for interpolation into PHP config files
    $env = Dotenv::createImmutable(__DIR__ . '/../');
    $env->load();

    $aggregator = new ConfigAggregator([
        Diactoros\ConfigProvider::class,
        new PhpFileProvider(__DIR__ . '/autoload/{{,*.}global,{,*.}local}.php'),
    ]);

    return $aggregator->getMergedConfig();
})();
