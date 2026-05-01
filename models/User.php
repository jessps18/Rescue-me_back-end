<?php

// ✅ CRUD - usuario
class User
{
    //Crud / Create - post usuarios
    public static function create($conn, $data)
    {
        $senhaHash = password_hash($data['senha'], PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            INSERT INTO usuarios 
            (nome_usuario, email, senha, curso, registro_academico, numero_contato) 
            VALUES (?,?,?,?,?,?)
        ");

        $stmt->bind_param(
            "ssssis",
            $data['nome'],
            $data['email'],
            $senhaHash,
            $data['curso'],
            $data['registro_academico'],
            $data['numero_contato']
        );

        if (!$stmt->execute()) {
            return ["error" => $stmt->error];
        }

        return ["message" => "Usuário criado"];
    }

    //cRud / Read - get usuario
    public static function all($conn)
    {
        return $conn->query("SELECT * FROM usuarios")->fetch_all(MYSQLI_ASSOC);
    }

    //crUd / update - put usuario
    public static function update($conn, $data)
    {
        $stmt = $conn->prepare("
        UPDATE usuarios
        SET nome_usuario = ?, curso = ?, registro_academico = ?, numero_contato = ?
        WHERE email =?
        ");

        $stmt->bind_param(
            "ssiss",
            $data['nome'],
            $data['curso'],
            $data['registro_academico'],
            $data['numero_contato'],
            $data['email'],
        );

        if (!$stmt->execute()) {
            return ["error" => $stmt->error];
        }

        return ["message" => "Usuário atualizado"];
    }

    //cruD / delete - delete usuarios
    public static function delete($conn, $data)
    {
        if (empty($data['email'])) {
            return ["error" => "Email obrigatório"];
        }

        $stmt = $conn->prepare("
        DELETE FROM usuarios
        WHERE email = ?
    ");

        $stmt->bind_param("s", $data['email']);

        if (!$stmt->execute()) {
            return ["error" => $stmt->error];
        }

        return ["message" => "Usuário deletado"];
    }
}


// ✅ CRUD - funcionario
class Funcionario
{
    //Crud / Create - post funcionario
    public static function create($conn, $data)
    {
        $senhaHash = password_hash($data['senha'], PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            INSERT INTO funcionarios 
            (nome_funcionario, email, senha, numero_contato) 
            VALUES (?,?,?,?)
        ");

        $stmt->bind_param(
            "ssss",
            $data['nome'],
            $data['email'],
            $senhaHash,
            $data['numero_contato']
        );

        if (!$stmt->execute()) {
            return ["error" => $stmt->error];
        }

        return ["message" => "Funcionário criado"];
    }

    //cRud / Read - get funcionario
    public static function all($conn)
    {
        return $conn->query("SELECT * FROM funcionarios")->fetch_all(MYSQLI_ASSOC);
    }

    //crUd / update - put funcionario
    public static function update($conn, $data)
    {
        $stmt = $conn->prepare("
        UPDATE funcionarios
        SET nome_funcionario = ?, numero_contato = ?
        WHERE email =?
        ");

        $stmt->bind_param(
            "ssiss",
            $data['nome'],
            $data['numero_contato'],
            $data['email'],
        );

        if (!$stmt->execute()) {
            return ["error" => $stmt->error];
        }

        return ["message" => "Funcionario atualizado"];
    }

    //cruD / delete - delete funcionarios
    public static function delete($conn, $data)
    {
        if (empty($data['email'])) {
            return ["error" => "Email obrigatório"];
        }

        $stmt = $conn->prepare("
        DELETE FROM funcionarios
        WHERE email = ?
    ");

        $stmt->bind_param("s", $data['email']);

        if (!$stmt->execute()) {
            return ["error" => $stmt->error];
        }

        return ["message" => "Funcionario deletado"];
    }
}


// ✅ CRUD - objeto
class Objeto
{
    //Crud / Create - post objeto
    public static function create($conn, $data)
    {
        $codigo = "OBJ-" . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

        // inicia tudo como null
        $email_usuario_achado = null;
        $email_funcionario_achado = null;

        // define quem achou
        if ($data['tipo_encontrado'] === 'usuario') {
            $email_usuario_achado = $data['email_encontrado'];
        } elseif ($data['tipo_encontrado'] === 'funcionario') {
            $email_funcionario_achado = $data['email_encontrado'];
        } else {
            return ["error" => "Tipo inválido"];
        }

        // recuperado (opcional)
        $email_usuario_recuperou = null;
        $email_funcionario_recuperou = null;

        if (!empty($data['tipo_recuperado'])) {
            if ($data['tipo_recuperado'] === 'usuario') {
                $email_usuario_recuperou = $data['email_recuperado'];
            } elseif ($data['tipo_recuperado'] === 'funcionario') {
                $email_funcionario_recuperou = $data['email_recuperado'];
            }
        }

        $ja_recuperado = 0;

        $stmt = $conn->prepare("
            INSERT INTO objetos 
            (codigo_objeto, nome_objeto, descricao, local_encontrado, onde_deixou,
             email_usuario_achado, email_funcionario_achado,
             email_usuario_recuperou, email_funcionario_recuperou,
             ja_recuperado) 
            VALUES (?,?,?,?,?,?,?,?,?,?)
        ");

        $stmt->bind_param(
            "sssssssssi",
            $codigo,
            $data['nome_objeto'],
            $data['descricao'],
            $data['local_encontrado'],
            $data['onde_deixou'],
            $email_usuario_achado,
            $email_funcionario_achado,
            $email_usuario_recuperou,
            $email_funcionario_recuperou,
            $ja_recuperado
        );

        if (!$stmt->execute()) {
            return ["error" => $stmt->error];
        }

        return ["message" => "Objeto criado", "codigo" => $codigo];
    }

    //cRud / Read - get objeto
    public static function all($conn)
    {
        return $conn->query("SELECT * FROM objetos")->fetch_all(MYSQLI_ASSOC);
    }

    //crUd / update - put usuario
    public static function update($conn, $data)
    {
        $email_usuario_recuperou = null;
        $email_funcionario_recuperou = null;

        if ($data['tipo_recuperado'] === "usuario") {
            $email_usuario_recuperou = $data['email_recuperado'];
        } elseif ($data['tipo_recuperado'] === "funcionario") {
            $email_funcionario_recuperou = $data['email_recuperado'];
        } else {
            return ["error" => "tipo invalido"];
        }

        $ja_recuperado = 1;

        $stmt = $conn->prepare("
            UPDATE objetos
            SET
                email_usuario_recuperou = ?,
                email_funcionario_recuperou = ?,
                ja_recuperado = ?

            WHERE codigo_objeto=?
        ");

        $stmt->bind_param(
            "ssis",
            $email_usuario_recuperou,
            $email_funcionario_recuperou,
            $ja_recuperado,
            $data['codigo_objeto']
        );

        if (!$stmt->execute()) {
            return ["error" => $stmt->error];
        }

        if ($stmt->affected_rows === 0) {
            return ["error" => "Objeto não encontrado"];
        }

        return ["message" => "Objeto atualizado"];
    }

    //cruD / delete - delete usuario
    public static function delete($conn, $data)
    {
        if (empty($data['codigo_objeto'])) {
            return ["error" => "Código do objeto obrigatório"];
        }

        $stmt = $conn->prepare("
        DELETE FROM objetos
        WHERE codigo_objeto = ?
    ");

        $stmt->bind_param(
            "s",
            $data['codigo_objeto']
        );

        if (!$stmt->execute()) {
            return ["error" => $stmt->error];
        }

        if ($stmt->affected_rows === 0) {
            return ["error" => "Objeto não encontrado"];
        }

        return ["message" => "Objeto deletado"];
    }
}



// ✅ CRUD - denuncia
class Denuncia
{
    //Crud / Create - post denuncia
    public static function create($conn, $data)
    {
        // inicia tudo como null
        $email_denunciante_usuario = null;
        $email_denunciante_funcionario = null;

        $email_denunciado_usuario = null;
        $email_denunciado_funcionario = null;

        // denunciante
        if ($data['tipo_denunciante'] === 'usuario') {
            $email_denunciante_usuario = $data['email_denunciante'];
        } elseif ($data['tipo_denunciante'] === 'funcionario') {
            $email_denunciante_funcionario = $data['email_denunciante'];
        } else {
            return ["error" => "Tipo denunciante inválido"];
        }

        // denunciado
        if ($data['tipo_denunciado'] === 'usuario') {
            $email_denunciado_usuario = $data['email_denunciado'];
        } elseif ($data['tipo_denunciado'] === 'funcionario') {
            $email_denunciado_funcionario = $data['email_denunciado'];
        } else {
            return ["error" => "Tipo denunciado inválido"];
        }

        $stmt = $conn->prepare("
            INSERT INTO denuncias 
            (descricao, objeto_codigo,
             email_denunciante_usuario, email_denunciante_funcionario,
             email_denunciado_usuario, email_denunciado_funcionario,
             status) 
            VALUES (?,?,?,?,?,?,?)
        ");

        $stmt->bind_param(
            "sssssss",
            $data['descricao'],
            $data['objeto_codigo'],
            $email_denunciante_usuario,
            $email_denunciante_funcionario,
            $email_denunciado_usuario,
            $email_denunciado_funcionario,
            $data['status'] ?? 'PENDENTE'
        );

        if (!$stmt->execute()) {
            return ["error" => $stmt->error];
        }

        return ["message" => "Denúncia criada"];
    }

    //cRud / Read - get denuncia
    public static function all($conn)
    {
        return $conn->query("SELECT * FROM denuncias")->fetch_all(MYSQLI_ASSOC);
    }

    //crUd / update - put denuncia 
    public static function update($conn, $data)
    {
        if (empty($data['id_denuncia'])) {
            return ["error" => "ID da denúncia obrigatório"];
        }

        // inicia como null
        $email_denunciado_usuario = null;
        $email_denunciado_funcionario = null;

        // define quem foi denunciado
        if ($data['tipo_denunciado'] === 'usuario') {
            $email_denunciado_usuario = $data['email_denunciado'];
        } elseif ($data['tipo_denunciado'] === 'funcionario') {
            $email_denunciado_funcionario = $data['email_denunciado'];
        } else {
            return ["error" => "Tipo inválido"];
        }

        $stmt = $conn->prepare("
        UPDATE denuncias
        SET 
            email_denunciado_usuario = ?,
            email_denunciado_funcionario = ?,
            status = ?
        WHERE id_denuncia = ?
    ");

        $stmt->bind_param(
            "sssi",
            $email_denunciado_usuario,
            $email_denunciado_funcionario,
            $data['status'],
            $data['id_denuncia']
        );

        if (!$stmt->execute()) {
            return ["error" => $stmt->error];
        }

        if ($stmt->affected_rows === 0) {
            return ["error" => "Denúncia não encontrada"];
        }

        return ["message" => "Denúncia atualizada"];
    }

    //cruD / delete - delete denuncia 
    public static function delete($conn, $data)
    {
        if (empty($data['id_denuncia'])) {
            return ["error" => "ID da denúncia obrigatório"];
        }

        $stmt = $conn->prepare("
        DELETE FROM denuncias
        WHERE id_denuncia = ?
    ");

        $stmt->bind_param("i", $data['id_denuncia']);

        if (!$stmt->execute()) {
            return ["error" => $stmt->error];
        }

        if ($stmt->affected_rows === 0) {
            return ["error" => "Denúncia não encontrada"];
        }

        return ["message" => "Denúncia deletada"];
    }
}
