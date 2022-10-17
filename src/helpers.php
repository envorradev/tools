<?php declare(strict_types=1);

if (!function_exists('compute_time')) {
    function compute_time(callable $callable, array $callableArgs = []): string
    {
        $start = microtime(true);
        $callable(...$callableArgs);
        $end = microtime(true);
        return (($end - $start) * 1000).' ms';
    }
}
