<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Cli\Console as ConsoleApp;
use Phalcon\Loader;

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

$di = new FactoryDefault();

/**
 * Handle routes
 */
include APP_PATH . '/config/router.php';

/**
 * Read services
 */
include APP_PATH . '/config/services.php';

// Использование стандартного CLI контейнера для сервисов
$cliDi = new CliDI();

$config = $di->getConfig();

$cliDi->set('config', $config);

$cliDi->set('db', $di->get('db'));

/**
 * Регистрируем автозагрузчик и сообщаем ему директорию
 * для регистрации каталога задач
 */
$loader = new Loader();

$loader->registerDirs(
    [
        __DIR__ . '/tasks',
    ]
);

$loader->registerNamespaces([
    'App'            => $config->application->appDir,
    'App\Models'     => $config->application->modelsDir,
    'App\Controller' => $config->application->controllersDir,
]);

$loader->register();

// Создание консольного приложения
$console = new ConsoleApp();

$console->setDI($cliDi);

/**
 * Обработка аргументов консоли
 */
$arguments = [];

foreach ($argv as $k => $arg) {
    if ($k === 1) {
        $arguments['task'] = $arg;
    } elseif ($k === 2) {
        $arguments['action'] = $arg;
    } elseif ($k >= 3) {
        $arguments['params'][] = $arg;
    }
}

try {
    // Обработка входящих аргументов
    $console->handle($arguments);
} catch (\Phalcon\Exception $e) {
    // Связанные с Phalcon вещи указываем здесь
    // ..
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
} catch (\Throwable $throwable) {
    fwrite(STDERR, $throwable->getMessage() . PHP_EOL);
    exit(1);
} catch (\Exception $exception) {
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);
    file_put_contents(
        '../var/logs/cli_error.log',
        date(DATE_W3C, time()) . ' ' . $exception->getMessage() . "\n" . $exception->getTraceAsString(),
        FILE_APPEND
    );

    exit(1);
}