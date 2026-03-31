<?php

use App\Controllers\UserController;
use App\Core\Router;

$route = new Router();

$route->post("/api/user/sing-up", [UserController::class, "singUp"]);

$route->post("/api/user/sing-in", [UserController::class, "singIn"]);

$route->get("/api/user", [UserController::class, "index"]);

$route->get("/sing-in", function ($request, $response) {
    return $response->view("auth");
});

$route->get("/sing-up", function ($request, $response) {
    return $response->view("register");
});

$route->get("/", function ($request, $response) {
    return $response->view("home");
});

$route->get("/product/{id}", function ($request, $response) {
    return $response->view("home");
});

$route->run();
