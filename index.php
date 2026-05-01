<?php

require 'db.php';
require 'controllers/UserController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

header('Content-Type: application/json');

if ($uri === '/users' && $method === 'GET') {
    (new UserController($conn))->index();
}

elseif ($uri === '/users' && $method === 'POST') {
    (new UserController($conn))->store();
}

else {
    http_response_code(404);
    echo json_encode(["error" => "Rota não encontrada"]);
}