<?php
class Pessoa //feito
{
    public static function create($conn, $data)
    {
        $senhaHash = password_hash(
            $data['senha'],
            PASSWORD_DEFAULT
        );

        $stmt = $conn->prepare("INSERT INTO pessoas (nome, email, senha, numero_contato, tipo) VALUES(?,?,?,?,?)");
        $stmt->bind_param(
            "sssss",
            $data['nome'],
            $data['email'],
            $senhaHash,
            $data['numero_contato'],
            $data['tipo']
        );

        if (!$stmt->execute()) {
            return ["error" => $stmt->error];
        }

        return [
            "message" => "Pessoa criada!",
            "id_pessoa" => $conn->insert_id
        ];
    }

    public static function all($conn)
    {
        return $conn->query("
                SELECT
                    p.id_pessoa,
                    p.nome,
                    p.email,
                    p.numero_contato,
                    p.tipo,
                    u.registro_academico,
                    c.nome_curso,
                    f.cargo
                FROM pessoas p
                LEFT JOIN usuarios u
                    ON p.id_pessoa = u.pessoa_id
                LEFT JOIN cursos c
                    ON u.curso_id = c.id_curso
                LEFT JOIN funcionarios f
                    ON p.id_pessoa = f.pessoa_id
            ")->fetch_all(MYSQLI_ASSOC);
    }
}

class Usuario //feito
{
    public static function create($conn, $data)
    {
        $stmt = $conn->prepare("
                INSERT INTO usuarios
                (
                    pessoa_id,
                    registro_academico,
                    curso_id
                )
                VALUES (?,?,?)
            ");

        $stmt->bind_param(
            "isi",
            $data['pessoa_id'],
            $data['registro_academico'],
            $data['curso_id']
        );

        if (!$stmt->execute()) {
            return ["error" => $stmt->error];
        }

        return ["message" => "Usuário criado"];
    }

    public static function get($conn, $id)
    {
        $stmt = $conn->prepare("
            SELECT
                p.id_pessoa,
                p.nome,
                p.email,
                p.numero_contato,
                p.tipo,
                u.id_usuario,
                u.registro_academico,
                c.id_curso,
                c.nome_curso,
            FROM pessoas p
            INNER JOIN usuarios u
                ON p.id_pessoa = u.pessoa_id
            INNER JOIN cursos c
                ON u.curso_id = c.id_curso
            WHERE p.id_pessoa = ?
            ");
        $stmt->bind_param(
            "i",
            $id
        );
        $stmt->execute();

        return $stmt
            ->get_result()
            ->fetch_assoc();
    }
}

class Funcionario //feito
{
    public static function get($conn, $id)
    {
        $stmt = $conn->prepare("
                SELECT
                    p.id_pessoa,
                    p.nome,
                    p.email,
                    p.numero_contato,
                    p.tipo,
                    f.id_funcionario,
                    f.cargo,
                FROM pessoas p
                INNER JOIN funcionarios f
                    ON p.id_pessoa = f.pessoa_id
                WHERE p.id_pessoa = ?
                ");
        $stmt->bind_param(
            "i",
            $id
        );
        $stmt->execute();

        return $stmt
            ->get_result()
            ->fetch_assoc();
    }
}

class Objeto //feito
{

    public static function create($conn, $data)
    {
        $stmt = $conn->prepare("
            INSERT INTO objetos
            (
                nome_objeto,
                descricao,
                local_encontrado,
                onde_deixou,
                encontrado_por
            )
            VALUES (?,?,?,?,?)
        ");

        $stmt->bind_param(
            "ssssi",
            $data['nome_objeto'],
            $data['descricao'],
            $data['local_encontrado'],
            $data['onde_deixou'],
            $data['encontrado_por']
        );

        if (!$stmt->execute()) {
            return ["error" => $stmt->error];
        }

        return ["message" => "Objeto criado"];
    }

    public static function recuperar($conn, $data)
    {
        $stmt = $conn->prepare("
            UPDATE objetos
            SET
                recuperado_por = ?,
                data_recuperacao = NOW(),
                ja_recuperado = TRUE
            WHERE id_objeto = ?
        ");

        $stmt->bind_param(
            "ii",
            $data['recuperado_por'],
            $data['id_objeto']
        );

        if (!$stmt->execute()) {
            return ["error" => $stmt->error];
        }

        return ["message" => "Objeto recuperado"];
    }

    public static function all($conn)
    {
        return $conn->query("
            SELECT
                o.*,
                p.nome AS encontrado_por_nome,
                r.nome AS recuperado_por_nome
            FROM objetos o
            INNER JOIN pessoas p
                ON o.encontrado_por = p.id_pessoa
            LEFT JOIN pessoas r
                ON o.recuperado_por = r.id_pessoa
        ")->fetch_all(MYSQLI_ASSOC);
    }

    public static function get($conn, $id)
    {
        $stmt = $conn->prepare("
            SELECT
                o.*,
                encontrou.id_pessoa as id_encontrou,
                encontrou.nome as nome_encontrou,

                recuperou.id_pessoa as id_recuperou,
                recuperou.nome as nome.recuperou,
            FROM objetos o
            INNER JOIN pessoas encontrou
                ON o.encontrado_por = encontrou.id_pessoa
            LEFT JOIN pessoas recuperou
                ON o.recuperado_por = recuperou.id_pessoa
            WHERE o.id_objeto=?
        ");
        $stmt->bind_param(
            "i",
            $id
        );
        $stmt->execute();
        return $stmt
            ->get_result()
            ->fetch_assoc();
    }
}

class Denuncias  //get especifico
{
    public static function create($conn, $data)
    {
        $stmt = $conn->prepare("
            INSERT INTO denuncias
            (
                titulo,
                descricao,
                objeto_id,
                denunciante,
                denunciado
            )
            VALUES (?,?,?,?,?)
        ");

        $stmt->bind_param(
            "ssiii",
            $data['titulo'],
            $data['descricao'],
            $data['objeto_id'],
            $data['denunciante'],
            $data['denunciado']
        );

        if (!$stmt->execute()) {
            return ["error" => $stmt->error];
        }

        return ["message" => "Denúncia criada"];
    }

    public static function all($conn)
    {
        return $conn->query("
            SELECT
                d.*,
                o.nome_objeto,
                denunciante.nome AS denunciante_nome,
                denunciado.nome AS denunciado_nome,
                analisador.nome AS analisado_por_nome
            FROM denuncias d
            INNER JOIN objetos o
                ON d.objeto_id = o.id_objeto
            INNER JOIN pessoas denunciante
                ON d.denunciante = denunciante.id_pessoa
            INNER JOIN pessoas denunciado
                ON d.denunciado = denunciado.id_pessoa
            LEFT JOIN pessoas analisador
                ON d.analisado_por = analisador.id_pessoa
        ")->fetch_all(MYSQLI_ASSOC);
    }

    public static function resolver($conn, $data)
    {
        $stmt = $conn->prepare("
            UPDATE denuncias
            SET
                status_denuncia = ?,
                justificativa = ?,
                analisado_por = ?,
                data_resposta = NOW()
            WHERE id_denuncia = ?
        ");

        $stmt->bind_param(
            "ssii",
            $data['status_denuncia'],
            $data['justificativa'],
            $data['analisado_por'],
            $data['id_denuncia']
        );

        if (!$stmt->execute()) {
            return ["error" => $stmt->error];
        }
    }

    public static function get($conn, $id)
    {
        $stmt = $conn->prepare("

        SELECT

            d.*,

            o.nome_objeto,

            denunciante.nome as nome_denunciante,

            denunciado.nome as nome_denunciado,

            analista.nome as nome_analista

        FROM denuncias d

        INNER JOIN objetos o
        ON d.objeto_id=o.id_objeto

        INNER JOIN pessoas denunciante
        ON d.denunciante=denunciante.id_pessoa

        INNER JOIN pessoas denunciado
        ON d.denunciado=denunciado.id_pessoa

        LEFT JOIN pessoas analista
        ON d.analisado_por=analista.id_pessoa

        WHERE d.id_denuncia=?
        ");

        $stmt->bind_param(
            "i",
            $id
        );

        $stmt->execute();

        return $stmt
            ->get_result()
            ->fetch_assoc();
    }
}
