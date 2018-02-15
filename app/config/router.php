<?php
/**
 * @var Phalcon\Di\FactoryDefault  $di
 */

/* @var \Phalcon\Mvc\Router $router */
$router = $di->getRouter();

// Define your routes here
$router->add(
    "/:controller/:int",
    [
        "controller" => 1,
        "action"     => "viewedit",
        "id"         => 2
    ]
);

// Define your routes here
$router->add(
    "/users/:int/visits",
    [
        "controller" => "users",
        "action"     => "visits",
        "id"         => 1
    ]
);

$router->add(
    "/locations/:int/avg",
    [
        "controller" => "locations",
        "action"     => "avgmark",
        "id"         => 1
    ]
);

$router->add(
    "/:controller/new",
    [
        "controller" => 1,
        "action"     => "create"
    ]
);

$router->handle();
