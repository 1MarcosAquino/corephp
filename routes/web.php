<?php

use Core\Router;

$route = new Router();

$route->get("/costumer/{id}", function ($id) {
    return $id;
});

$route->post("/user", "UserController@store");

$route->get("/user/{id}", "UserController@index");

$route->put("/user/{id}", "UserController@update");

$route->delete("/user/{id}", "UserController@detele");

$route->run();
