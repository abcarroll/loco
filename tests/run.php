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
require __DIR__ . '/../docs/examples/bnf.php';
require __DIR__ . '/../docs/examples/ebnf.php';
require __DIR__ . '/../docs/examples/json.php';
require __DIR__ . '/../docs/examples/left.php';
require __DIR__ . '/../docs/examples/locoNotation.php';
