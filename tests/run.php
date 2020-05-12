<?php
require __DIR__ . '/../vendor/autoload.php';

$whoops = (function () {
    $whoops = new \Whoops\Run;
    $textHandler = new \Whoops\Handler\PlainTextHandler();
    $textHandler->addTraceFunctionArgsToOutput(true);
    $textHandler->addTraceToOutput(false);
    $textHandler->addPreviousToOutput(true);
    $textHandler->setDumper('dump');

    $whoops->pushHandler($textHandler);
    $whoops->register();

    $whoops->allowQuit(false);
    return $whoops;
})();

$test = function ($closure) use($whoops) {
    try {
        $closure();
    } catch (\AssertionError $e) {
        $whoops->handleException($e);
    }
};

require __DIR__ . '/Classic/unit.php';
