<?php
/**
 * Bootstrap smoke tests servers.
 */
declare(strict_types=1);

$testServers = [
    'expressive' => [
        'path' 		                  => __DIR__ . '/server/expressive',
        'host' 		                  => EXPRESSIVE_SERVER_HOST,
        'port' 		                  => EXPRESSIVE_SERVER_PORT,
        'docroot' 	                => 'public',
        'auto_composer_install'    => true
    ]
];

foreach ($testServers as $serverName => $params) {
    $serverPath = $params['path'];
    if ($params['auto_composer_install'] && !is_dir($serverPath . DIRECTORY_SEPARATOR . 'vendor')) {
        exec('composer install-expressive-testapp');
    }

    $publicPath = $serverPath . DIRECTORY_SEPARATOR . $params['docroot'];

    // Command that starts the built-in web server
    $command = sprintf(
        'php -S %s:%d -t %s %s >/dev/null 2>&1 & echo $!',
        $params['host'],
        $params['port'],
        $publicPath,
        $publicPath . DIRECTORY_SEPARATOR . 'index.php'
    );

    // Execute the command and store the process ID
    $output = [];
    exec($command, $output);
    $pid = (int) $output[0];
    echo sprintf(
        '%s - %s web server started on %s:%d with PID %d',
        date('r'),
        $serverName,
        $params['host'],
        $params['port'],
        $pid
        ) . PHP_EOL;

    echo sprintf(
        '%s - Command: %s',
        date('r'),
        $command
        ) . PHP_EOL;

    // Kill the web server when the process ends
    register_shutdown_function(function () use ($pid, $serverName) {
        echo sprintf(
            '%s - Killing %s webserver process with ID %d',
            date('r'),
            $serverName,
            $pid
            ) . PHP_EOL;
        exec('kill ' . $pid);
    });

    // Let the server start
    sleep(1);
}
