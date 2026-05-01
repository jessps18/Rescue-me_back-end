<?php

require_once 'models/User.php';

class UserController {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function index() {
        $users = User::all($this->conn);
        echo json_encode($users);
    }

    public function store() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['nome'])) {
            http_response_code(400);
            echo json_encode(["error" => "Nome obrigatório"]);
            return;
        }

        $user = User::create($this->conn, $data['nome']);

        echo json_encode($user);
    }
}