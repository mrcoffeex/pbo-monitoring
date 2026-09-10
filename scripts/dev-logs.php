<?php

if (function_exists('pcntl_fork')) {
    passthru('php artisan pail --timeout=0', $exitCode);
    exit($exitCode);
}

fwrite(STDERR, 'Pail skipped: pcntl is not available. Tailing storage/logs/laravel.log instead.'.PHP_EOL);

$logFile = dirname(__DIR__).DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'laravel.log';
$position = is_file($logFile) ? filesize($logFile) : 0;

while (true) {
    clearstatcache(true, $logFile);

    if (! is_file($logFile)) {
        usleep(400_000);

        continue;
    }

    $size = filesize($logFile);

    if ($size < $position) {
        $position = 0;
    }

    if ($size > $position) {
        $handle = fopen($logFile, 'rb');

        if ($handle !== false) {
            fseek($handle, $position);
            echo stream_get_contents($handle);
            $position = ftell($handle) ?: $position;
            fclose($handle);
        }
    }

    usleep(400_000);
}
