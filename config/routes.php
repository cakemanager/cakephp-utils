<?php
use Cake\Routing\Router;

Router::plugin('Utils', function ($routes) {
    $routes->fallbacks();
});
