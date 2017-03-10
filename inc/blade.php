<?php

// configure blade engine
require 'vendor/autoload.php';
use Philo\Blade\Blade;
$views = __DIR__ . '/../views';		// blade.php now sits in /inc folder, so prefix views folder with /../
$cache = __DIR__ . '/../cache';		// so $views and $cache still point to valid filesystem folder

$blade = new Blade($views, $cache);

