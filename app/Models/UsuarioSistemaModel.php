<?php

declare(strict_types=1);

namespace Com\Daw2\Models;

class UsuarioSistemaModel extends \Com\Daw2\Core\BaseModel {

    private const SELECT_FROM = "SELECT us.*, ar.nombre_rol, ai.nombre_idioma FROM usuario_sistema us LEFT JOIN aux_rol ar ON ar.id_rol = us.id_rol LEFT JOIN aux_idiomas ai ON ai.id_idioma = us.id_idioma ORDER BY us.nombre";

    function getAll(): array {
        return $this->pdo->query(self::SELECT_FROM)->fetchAll();
    }

    public function loadId(string $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM usuario_sistema WHERE id_usuario = ?");
        $stmt->execute([$id]);
        if ($row = $stmt->fetch()) {
            return $row;
        } else {
            return null;
        }
    }
    
    public function loadUsuario(string $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM usuario_sistema WHERE nombre = ?");
        $stmt->execute([$id]);
        if ($row = $stmt->fetch()) {
            return $row;
        } else {
            return null;
        }
    }

    public function loadEmail(string $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM usuario_sistema WHERE email = ?");
        $stmt->execute([$id]);
        if ($row = $stmt->fetch()) {
            return $row;
        } else {
            return null;
        }
    }

    function insertUsuario(array $data): bool {
        $sql = "INSERT INTO usuario_sistema (id_rol, email, pass, nombre, id_idioma) VALUES(:id_rol, :email, :password, :username, :id_idioma)";
        $stmt = $this->pdo->prepare($sql);
        unset($data['enviar']);
        unset($data['confirm-password']);
//        var_dump($data);
//        die;
        
        //Hashear la pass
        $password = $data['password'];
        unset ($data['password']);
        $data['password'] = password_hash($password, PASSWORD_DEFAULT);        

        
        if ($stmt->execute($data)) {
            return $stmt->rowCount() === 1;
        } else {
            return false;
        }
    }
    
    function updateUsuario(array $data): bool {       

        $sql = "UPDATE permisos.usuario_sistema SET id_rol=:id_rol, email=:email, pass=:password, nombre=:username, id_idioma=:id_idioma WHERE nombre=:username";
        $stmt = $this->pdo->prepare($sql);
        unset($data['enviar']);
        unset($data['confirm-password']);
//        var_dump($data);
//        die;
        
        //Hashear la pass
        $password = $data['password'];
        unset ($data['password']);
        $data['password'] = password_hash($password, PASSWORD_DEFAULT);       

        
        if ($stmt->execute($data)) {
            return $stmt->rowCount() === 1;
        } else {
            return false;
        }
    }
    
    public function deleteUsuario(string $id) : bool{
        $stmt = $this->pdo->prepare("DELETE FROM usuario_sistema WHERE id_usuario = ?");       
        if($stmt->execute([$id]) && $stmt->rowCount() == 1){
           return true;
        } 
        else{
            return false;
        }
    }
}
