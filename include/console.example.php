<?php
require_once 'console.php';

$main_console = new Console();

$main_console->log('You', 'can', 'output', 'in', 'client DevTools console', 'from', 'this PHP program!');
$main_console->error('It also supports Error');
$main_console->log('And it also supports numbers(', 1, ')');
$main_console->log('and boolean(', true, '),');
$main_console->log('array(', [1, 2, 3, 'hello' => 'world'], '),');
$main_console->log('objects.');
$main_console->info('%cSome browsers can also decorate letters.', 'color: #ffd000;font-size: 4em;font-weight: bold;font-style: italic;font-family:serif;' . 'text-decoration: underline dotted red;text-shadow: 10px 10px 1px #cccccc;' . 'text-transform: uppercase;background-color: #eeeeee;margin-right: 30px;' . 'padding-left: 30px;border: 2px solid #dddddd');
$main_console->log("Let's use it!");
$main_console->info('The download is', 'http://github.com/mk7087/console.php');
?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Console.php Exsample</title>
    </head>
    <body>
        <p>Plese open DevTools Console</p>
        <?php $main_console->output_script(' ', 2, 4); ?>
    </body>
</html>