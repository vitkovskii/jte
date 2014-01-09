<?php
use Jte\Jte;

include __DIR__ . '/../vendor/autoload.php';

$jte = new Jte(__DIR__ . '/templates', ['useCache' => true, 'dir' => __DIR__ . '/cache/']);

$menu = [
    ['url' => 'http://link1.com', 'name' => 'Main page'],
    ['url' => 'http://link2.com', 'name' => 'Another page'],
];

$i = 100;
while ($i--) {
    echo $jte->render('child.jte', 'main', ['menu' => $menu, 'user_count' => rand(0, 1000)]), PHP_EOL;
}