<?php

spl_autoload_extensions('.php');
spl_autoload_register(function ($class) {
    include __DIR__ . '/classes/' . str_replace('\\', '/', $class) . '.php';
});

$crawler = new Crawler\Crawler();

echo $crawler->solution->toJson();
