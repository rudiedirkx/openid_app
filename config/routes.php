<?php

use row\http\Router;

$router = new Router;

$router->add('/$', 'pages/page/Home');

$router->add('/scaffolding', array('controller' => 'row\applets\scaffolding\Controller'));


