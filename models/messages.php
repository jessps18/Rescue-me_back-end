<?php

const ERROR_INVALID = [
    "status"=>400,
    "message"=>"Os dados encaminhados na requisição não são validos"
];
const ERROR_NOT_FOUND = [
    "status"=>404,
    "message"=>"Nenhum item encontrado"
];
const ERROR_INTERNAL_SERVER_DB = [
    "status"=>500,
    "message"=>"Erro de conexão com o banco de dados"
];
const SUCCESS_CREATED_ITEM = [
    "status"=>201,
    "message"=>"Item criado com sucesso!"
];
const SUCCESS_UPDATED_ITEM = [
    "status"=>200,
    "message"=>"Item atualizado com sucesso!"
];

