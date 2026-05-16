<?php

require 'db.php';
require 'controllers/UserController.php';
session_start();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$partes = explode('/', $uri);

$id = $partes[2] ?? null;

header('Content-Type: application/json');

//denuncias
if ($uri === '/Denuncias' && $method === 'GET') {

    $response = (new UserController($conn))
        ->getDenuncia($conn);

    echo json_encode($response);
} elseif ($uri === '/DenunciasP' && $method === 'POST') {

    $data = json_decode(
        file_get_contents("php://input"),
        true
    );

    $response = (new UserController($conn))
        ->createDenuncia($conn, $data);

    echo json_encode($response);
} elseif ($uri === '/DenunciasA' && $method === 'PUT') {

    $data = json_decode(
        file_get_contents("php://input"),
        true
    );

    $response = (new UserController($conn))
        ->updateDenuncia($conn, $data);

    echo json_encode($response);
} elseif ($uri === '/Cursos' && $method === 'GET') {

    $response = (new UserController($conn))
        ->getCursos($conn);

    echo json_encode($response);
} elseif ($uri === '/Usuarios' && $method === 'POST') {

    $data = json_decode(
        file_get_contents("php://input"),
        true
    );

    $response = (new UserController($conn))
        ->createUsuarios($conn, $data);

    echo json_encode($response);
} elseif ($uri === '/Todos' && $method === 'GET') {

    $response = (new UserController($conn))
        ->getPessoas($conn);

    echo json_encode($response);
} elseif ($uri === '/Objeto' && $method === 'POST') {

    $data = json_decode(
        file_get_contents("php://input"),
        true
    );

    $response = (new UserController($conn))
        ->createObjeto($conn, $data);

    echo json_encode($response);
} elseif ($uri === '/ObjetoA' && $method === 'PUT') {

    $data = json_decode(
        file_get_contents("php://input"),
        true
    );

    $response = (new UserController($conn))
        ->updateObjeto($conn, $data);

    echo json_encode($response);
} elseif ($uri === '/Objetos' && $method === 'GET') {

    $response = (new UserController($conn))
        ->getObjeto($conn);

    echo json_encode($response);
} elseif ($partes[1] === 'Denuncia' && $method === 'GET') {

    $usuarioLogado =
        $_SESSION['usuario_id'] ?? null;

    $response =
        (new UserController($conn))
        ->getBuscarDenuncia(
            $conn,
            $id,
            $usuarioLogado
        );

    echo json_encode($response);
} elseif ($partes[1] === 'Usuario' && $method === 'GET') {

    $usuarioLogado =
        $_SESSION['usuario_id'] ?? null;

    $response =
        (new UserController($conn))
        ->getBuscarUsuario(
            $conn,
            $id,
            $usuarioLogado
        );

    echo json_encode($response);
} elseif ($partes[1] === 'Funcionario' && $method === 'GET') {

    $usuarioLogado =
        $_SESSION['usuario_id'] ?? null;

    $response =
        (new UserController($conn))
        ->getBuscarFuncionario(
            $conn,
            $id,
            $usuarioLogado
        );

    echo json_encode($response);
} elseif ($partes[1] === 'Objeto' && $method === 'GET') {

    $usuarioLogado =
        $_SESSION['usuario_id'] ?? null;

    $response =
        (new UserController($conn))
        ->getBuscarObjeto(
            $conn,
            $id,
            $usuarioLogado
        );

    echo json_encode($response);
} else {

    http_response_code(404);

    echo json_encode([
        "error" => "Rota não encontrada"
    ]);
}
