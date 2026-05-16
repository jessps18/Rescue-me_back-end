<?php

require_once("./models/banco.php");
require_once("./models/messages.php");

class UserController
{
    // OBJETO
    function getBuscarObjeto($conn, $id, $usuarioLogado)
    {
        $idObjeto = $id;

        if ($idObjeto == "" || $idObjeto == null || !is_numeric($idObjeto)) {
            return ERROR_INVALID;
        }

        $dadosObjeto = Objeto::get($conn, $idObjeto);

        if (!$dadosObjeto) {
            return ERROR_NOT_FOUND;
        }

        if ($dadosObjeto['encontrado_por'] != $usuarioLogado) {
            return ERROR_NOT_FOUND;
        }

        return [
            "objeto" => $dadosObjeto,
            "status" => 200
        ];
    }

    function getObjeto($conn)
    {
        $dadosObjeto = Objeto::all($conn);

        if (!$dadosObjeto) {
            return ERROR_INTERNAL_SERVER_DB;
        }

        if (count($dadosObjeto) == 0) {
            return ERROR_NOT_FOUND;
        }

        return [
            "objetos" => $dadosObjeto,
            "status" => 200
        ];
    }

    function updateObjeto($conn, $data)
    {
        if (empty($data['id_objeto'])) {
            return ERROR_INVALID;
        }

        $dataObjeto = Objeto::recuperar($conn, $data);

        if ($dataObjeto) {
            return SUCCESS_UPDATED_ITEM;
        }

        return ERROR_INTERNAL_SERVER_DB;
    }

    function createObjeto($conn, $data)
    {
        if (
            empty($data['nome_objeto']) ||
            empty($data['descricao']) ||
            empty($data['local_encontrado']) ||
            empty($data['onde_deixou']) ||
            empty($data['encontrado_por'])
        ) {
            return ERROR_INVALID;
        }

        $dataObjeto = Objeto::create($conn, $data);

        if ($dataObjeto) {
            return SUCCESS_CREATED_ITEM;
        }

        return ERROR_INTERNAL_SERVER_DB;
    }

    // PESSOAS
    function getPessoas($conn)
    {
        $dadosPessoas = Pessoa::all($conn);

        if (!$dadosPessoas) {
            return ERROR_INTERNAL_SERVER_DB;
        }

        if (count($dadosPessoas) == 0) {
            return ERROR_NOT_FOUND;
        }

        return [
            "pessoas" => $dadosPessoas,
            "status" => 200
        ];
    }

    // FUNCIONARIO
    function getBuscarFuncionario($conn, $id, $usuarioLogado)
    {
        $idFuncionario = $id;

        if ($idFuncionario == "" || $idFuncionario == null || !is_numeric($idFuncionario)) {
            return ERROR_INVALID;
        }

        $dadosFuncionario = Funcionario::get($conn, $idFuncionario);

        if (!$dadosFuncionario) {
            return ERROR_NOT_FOUND;
        }

        if ($dadosFuncionario['pessoa_id'] != $usuarioLogado) {
            return ERROR_NOT_FOUND;
        }

        return [
            "funcionario" => $dadosFuncionario,
            "status" => 200
        ];
    }

    // USUARIOS
    function getBuscarUsuario($conn, $id, $usuarioLogado)
    {
        $idUsuario = $id;

        if ($idUsuario == "" || $idUsuario == null || !is_numeric($idUsuario)) {
            return ERROR_INVALID;
        }

        $dadosUsuario = Usuario::get($conn, $idUsuario);

        if (!$dadosUsuario) {
            return ERROR_NOT_FOUND;
        }

        if ($dadosUsuario['pessoa_id'] != $usuarioLogado) {
            return ERROR_NOT_FOUND;
        }

        return [
            "usuario" => $dadosUsuario,
            "status" => 200
        ];
    }

    function createUsuarios($conn, $data)
    {
        if (
            empty($data['nome']) ||
            empty($data['email']) ||
            empty($data['senha']) ||
            empty($data['numero_contato']) ||
            empty($data['registro_academico']) ||
            empty($data['curso_id'])
        ) {
            return ERROR_INVALID;
        }

        $result = Pessoa::create($conn, [
            "nome" => $data['nome'],
            "email" => $data['email'],
            "senha" => $data['senha'],
            "numero_contato" => $data['numero_contato'],
            "tipo" => "USUARIO"
        ]);

        $idPessoa = $result['id_pessoa'];

        if (!$idPessoa) {
            return ERROR_INTERNAL_SERVER_DB;
        }

        $usuario = Usuario::create($conn, [
            "pessoa_id" => $idPessoa,
            "registro_academico" => $data['registro_academico'],
            "curso_id" => $data['curso_id']
        ]);

        if ($usuario) {
            return SUCCESS_CREATED_ITEM;
        }

        return ERROR_INTERNAL_SERVER_DB;
    }

    // CURSOS
    function getCursos($conn)
    {
        $dados = Materias::all($conn);

        if (!$dados) {
            return ERROR_INTERNAL_SERVER_DB;
        }

        if (count($dados) == 0) {
            return ERROR_NOT_FOUND;
        }

        return [
            "cursos" => $dados,
            "status" => 200
        ];
    }

    // DENUNCIA
    function getBuscarDenuncia($conn, $id, $usuarioLogado)
{
    if ($id == "" || $id == null || !is_numeric($id)) {
        return ERROR_INVALID;
    }

    $dadosDenuncia = Denuncias::get($conn, $id);

    if (!$dadosDenuncia) {
        return ERROR_NOT_FOUND;
    }

    if ($dadosDenuncia['denunciante'] != $usuarioLogado) {
        return ERROR_NOT_FOUND;
    }

    return [
        "denuncia" => $dadosDenuncia,
        "status" => 200
    ];
}

    function getDenuncia($conn)
    {
        $dadosDenuncia = Denuncias::all($conn);

        if (!$dadosDenuncia) {
            return ERROR_INTERNAL_SERVER_DB;
        }

        if (!$dadosDenuncia || count($dadosDenuncia) === 0) {
            return ERROR_NOT_FOUND;
        }

        return [
            "denuncias" => $dadosDenuncia,
            "status" => 200
        ];
    }

    function updateDenuncia($conn, $data)
    {
        if (
            empty($data['id_denuncia']) ||
            empty($data['status_denuncia']) ||
            empty($data['justificativa']) ||
            empty($data['analisado_por'])
        ) {
            return ERROR_INVALID;
        }

        $dadosDenuncia = Denuncias::resolver($conn, [
            "id_denuncia" => $data['id_denuncia'],
            "status_denuncia" => $data['status_denuncia'],
            "justificativa" => $data['justificativa'] ?? null,
            "analisado_por" => $data['analisado_por']
        ]);

        if ($dadosDenuncia) {
            return SUCCESS_UPDATED_ITEM;
        }

        return ERROR_INTERNAL_SERVER_DB;
    }

    function createDenuncia($conn, $data)
    {
        if (
            empty($data['titulo']) ||
            empty($data['descricao']) ||
            empty($data['objeto_id']) ||
            empty($data['denunciante']) ||
            empty($data['denunciado'])
        ) {
            return ERROR_INVALID;
        }

        $dataDenuncia = Denuncias::create($conn, $data);

        if ($dataDenuncia) {
            return SUCCESS_CREATED_ITEM;
        }

        return ERROR_INTERNAL_SERVER_DB;
    }
}
