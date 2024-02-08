<?php

declare(strict_types=1);

namespace Com\Daw2\Models;

class UsuarioSistemaModel extends \Com\Daw2\Core\BaseModel {

    private const SELECT_FROM = "SELECT us.*, ar.nombre_rol, ai.nombre_idioma FROM usuario_sistema us LEFT JOIN aux_rol ar ON ar.id_rol = us.id_rol LEFT JOIN aux_idiomas ai ON ai.id_idioma = us.id_idioma ORDER BY us.nombre";

    function getAll(): array {
        return $this->pdo->query(self::SELECT_FROM)->fetchAll();
    }

    public function loadUsersById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM usuario_sistema WHERE id_usuario = ?");
        $stmt->execute([$id]);
        if ($row = $stmt->fetch()) {
            return $row;
        } else {
            return null;
        }
    }

    public function loadUsersByName(string $nombre): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM usuario_sistema WHERE nombre = ?");
        $stmt->execute([$nombre]);
        if ($row = $stmt->fetch()) {
            return $row;
        } else {
            return null;
        }
    }

    public function loadUsersByEmail(string $email): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM usuario_sistema WHERE email = ?");
        $stmt->execute([$email]);
        if ($row = $stmt->fetch()) {
            return $row;
        } else {
            return null;
        }
    }

    function loadUsersByEmailNotId(string $email, int $id): ?array {
        $query = "SELECT * FROM usuario_sistema WHERE email = ? AND id_usuario != ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$email, $id]);
        if ($row = $stmt->fetch()) {
            return $row;
        } else {
            return null;
        }
    }

    function insertUsuario(array $data): bool {
        $sql = "INSERT INTO usuario_sistema (id_rol, email, pass, nombre, id_idioma) VALUES(:id_rol, :email, :password, :nombre, :id_idioma)";
        $stmt = $this->pdo->prepare($sql);
        unset($data['enviar']);
        unset($data['confirm-password']);
//        var_dump($data);
//        die;
        //Hashear la pass
        $password = $data['password'];
        unset($data['password']);
        $data['password'] = password_hash($password, PASSWORD_DEFAULT);

        if ($stmt->execute($data)) {
            return $stmt->rowCount() === 1;
        } else {
            return false;
        }
    }

    function updateUsuario(int $idUsuario, array $data): bool {
        $query = "UPDATE usuario_sistema SET id_rol=:id_rol, email=:email, nombre=:nombre, id_idioma=:id_idioma WHERE id_usuario=:id_usuario";
        $stmt = $this->pdo->prepare($query);
        $vars = [
            'id_rol' => $data['id_rol'],
            'email' => $data['email'],
            'nombre' => $data['nombre'],
            'id_idioma' => $data['id_idioma'],
            'id_usuario' => $idUsuario
        ];
        return $stmt->execute($vars);
    }

    function editPassword(int $idUsuario, string $pass): bool {
        $query = "UPDATE usuario_sistema SET pass=? WHERE id_usuario=?";
        $stmt = $this->pdo->prepare($query);
        $encryptedPass = password_hash($pass, PASSWORD_DEFAULT);
        return $stmt->execute([$encryptedPass, $idUsuario]);
    }

    public function deleteUsuario(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM usuario_sistema WHERE id_usuario = ?");
        if ($stmt->execute([$id]) && $stmt->rowCount() == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function changeBaja(int $id): bool {
        $usuario = $this->loadUsersById($id);
        $baja = $usuario['baja'];
        if ($baja == 1) {
            $stmt = $this->pdo->prepare("UPDATE permisos.usuario_sistema SET baja=0 WHERE id_usuario=:id;");
        } else {
            $stmt = $this->pdo->prepare("UPDATE permisos.usuario_sistema SET baja=1 WHERE id_usuario=:id;");
        }
        if ($stmt->execute([$id]) && $stmt->rowCount() == 1) {
            header('location: /usuarios-sistema');
            return true;
        } else {
            return false;
        }
    }
    
    function updateLastDate(int $idUsuario) : bool{
        $query = "UPDATE usuario_sistema SET last_date= NOW() WHERE id_usuario = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$idUsuario]);
    }
}
