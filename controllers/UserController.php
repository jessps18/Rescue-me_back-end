<?php

require_once("banco.php");
require_once("messages.php");

//controller objeto
function getBuscarObjeto($conn, $id){

    $idObjeto=$id;

    $objetoJSON=[];

    if(
        $idObjeto == "" || $idObjeto==null || !is_numeric($idObjeto)){

        return ERROR_INVALID;

    }else{

        $dadosObjeto= Objeto::get($conn, $id);

        if($dadosObjeto){

            $objetoJSON["objeto"]= $dadosObjeto;

            $objetoJSON["status"]= 200;

            return $objetoJSON;

        }else{

            return ERROR_NOT_FOUND;

        }

    }
}

function getObjeto($conn){

    $dadosObjeto = Objeto::all($conn);

    if(!$dadosObjeto){

        return ERROR_INTERNAL_SERVER_DB;
    }

    if(count($dadosObjeto)==0){

        return ERROR_NOT_FOUND;
    }

    return [
        "objetos"=>$dadosObjeto,
        "status"=>200
    ];
}

function updateObjeto($conn, $data){

    if (empty($data['id_objeto'])) {
        return ERROR_INVALID;
    }

    $dataObjeto = Objeto::recuperar($conn, $data);
    if ($dataObjeto) {
        return SUCCESS_UPDATED_ITEM;
    }

    return ERROR_INTERNAL_SERVER_DB;

}

function createObjeto($conn, $data){
    if (empty($data['nome_objeto']) || empty($data['descricao']) || empty($data['local_encontrado']) || empty($data['onde_deixou']) || empty($data['encontrado_por'])) {
        return ERROR_INVALID;
    }

    $dataObjeto = Objeto::create($conn, $data);
    if ($dataObjeto) {
        return SUCCESS_CREATED_ITEM;
    }

    return ERROR_INTERNAL_SERVER_DB;
}

//controller pessoas
function getPessoas($conn){

    $dadosPessoas = Pessoa::all($conn);

    if(!$dadosPessoas){

        return ERROR_INTERNAL_SERVER_DB;
    }

    if(count($dadosPessoas)==0){

        return ERROR_NOT_FOUND;
    }

    return SUCCESS_CREATED_ITEM;
}

//controller Funcionario
function getBuscarFuncionario($conn, $id){

    $idFuncionario=$id;

    $objetoJSON=[];

    if(
        $idFuncionario == "" || $idFuncionario==null || !is_numeric($idFuncionario)){

        return ERROR_INVALID;

    }else{

        $dadosFuncionario= Funcionario::get($conn, $id);

        if($dadosFuncionario){
            return $objetoJSON;

        }else{

            return ERROR_NOT_FOUND;

        }

    }
}

//controller usuarios(alunos)
function createUsuarios($conn, $data){
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

    $idPessoa = Pessoa::create(
        $conn,
        [

            "nome" => $data['nome'],
            "email" => $data['email'],
            "senha" => $data['senha'],
            "numero_contato" =>$data['numero_contato'],
            "tipo" => "USUARIO"

        ]
    );

    if (!$idPessoa) {

        return ERROR_INTERNAL_SERVER_DB;
    }

    $usuario = Usuario::create(
        $conn,
        [
            "pessoa_id" => $idPessoa,
            "registro_academico" => $data['registro_academico'],
            "curso_id" => $data['curso_id']

        ]
    );

    if ($usuario) {

        return SUCCESS_CREATED_ITEM;
    }

    return ERROR_INTERNAL_SERVER_DB;
}

function getBuscarUsuario($conn, $id){

    $idUsuario=$id;

    $objetoJSON=[];

    if(
        $idUsuario == "" || $idUsuario==null || !is_numeric($idUsuario)){

        return ERROR_INVALID;

    }else{

        $dadosUsuario= Usuario::get($conn, $id);

        if($dadosUsuario){
            return $objetoJSON;

        }else{

            return ERROR_NOT_FOUND;

        }

    }
}

//cursos
function getCursos($conn){

    $dados = Materias::all($conn);

    if(!$dados){

        return ERROR_INTERNAL_SERVER_DB;
    }

    if(count($dados)==0){

        return ERROR_NOT_FOUND;
    }

    return [
        "objetos"=>$dados,
        "status"=>200
    ];
}

//denuncias
function getBuscarDenuncia($conn, $id){

    $idDenuncia=$id;

    $objetoJSON=[];

    if(
        $idDenuncia == "" || $idDenuncia==null || !is_numeric($idDenuncia)){

        return ERROR_INVALID;

    }else{

        $dadosDenuncia= Denuncias::get($conn, $id);

        if($dadosDenuncia){

            return $objetoJSON;

        }else{

            return ERROR_NOT_FOUND;

        }

    }
}

function getDenuncia($conn){

    $dadosDenuncia = Denuncias::all($conn);

    if(!$dadosDenuncia){

        return ERROR_INTERNAL_SERVER_DB;
    }

    if(count($dadosDenuncia)==0){

        return ERROR_NOT_FOUND;
    }

    return [
        "objetos"=>$dadosDenuncia,
        "status"=>200
    ];
}

function updateDenuncia($conn, $data){
    if(
        empty($data['id_denuncia']) || empty($data['status_denuncia']) || empty($data['analisado_por'])
    ){
        return ERROR_INVALID;
    }

    $dadosDenuncia =
    Denuncias::resolver(
        $conn,
        [
            "id_denuncia" => $data['id_denuncia'],
            "status_denuncia" =>$data['status_denuncia'],
            "justificativa" => $data['justificativa'] ?? null,
            "analisado_por" => $data['analisado_por']
        ]
    );

    if($dadosDenuncia){
        return SUCCESS_UPDATED_ITEM;
    }
    return ERROR_INTERNAL_SERVER_DB;
}

function createDenuncia($conn, $data){
    if (empty($data['titulo']) || empty($data['descricao']) || empty($data['objeto_id']) || empty($data['denunciante']) || empty($data['denunciado'])) {
        return ERROR_INVALID;
    }

    $dataDenuncia = Denuncias::create($conn, $data);
    if ($dataDenuncia) {
        return SUCCESS_CREATED_ITEM;
    }

    return ERROR_INTERNAL_SERVER_DB;
}