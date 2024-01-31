<?php

declare(strict_types=1);

namespace Com\Daw2\Controllers;
USE \PDO;

class UsuarioSistemaController extends \Com\Daw2\Core\BaseController {

    function mostrarTodos() {
        $data = [];
        $data['titulo'] = 'Todos los usuarios';
        $data['seccion'] = '/usuarios-sistema';

        $modelo = new \Com\Daw2\Models\UsuarioSistemaModel();
        $data['usuarios'] = $modelo->getAll();

        $this->view->showViews(array('templates/header.view.php', 'usuario_sistema.view.php', 'templates/footer.view.php'), $data);
    }

    function mostrarAdd() {
        $data = [];
        $modelo = new \Com\Daw2\Models\UsuarioSistemaModel();
        $input = $modelo->getAll();
        if (is_null($input)) {
            header('location: /usuarios-sistema');
        } else {
            $data['titulo'] = 'Añadir usuario';
            $data['seccion'] = '/usuarios-sistema/add';
            $data['tituloDiv'] = 'Alta usuario';

            $data['input'] = $input;

            $rolModel = new \Com\Daw2\Models\AuxRolModel();
            $data['roles'] = $rolModel->getAll();

            $idiomaModel = new \Com\Daw2\Models\AuxIdiomasModel();
            $data['idiomas'] = $idiomaModel->getAll();

            $this->view->showViews(array('templates/header.view.php', 'edit.usuario_sistema.view.php', 'templates/footer.view.php'), $data);
        }
    }

    function processAdd() {
        $errores = $this->checkForm($_POST);
        if (count($errores) > 0) {
            $data = [];
            $data['titulo'] = 'Añadir usuario';
            $data['seccion'] = '/usuarios-sistema/add';
            $data['tituloDiv'] = 'Alta usuario';

            $rolModel = new \Com\Daw2\Models\AuxRolModel();
            $data['roles'] = $rolModel->getAll();

            $idiomaModel = new \Com\Daw2\Models\AuxIdiomasModel();
            $data['idiomas'] = $idiomaModel->getAll();

            $data['input'] = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            $data['errores'] = $errores;

            $this->view->showViews(array('templates/header.view.php', 'edit.usuario_sistema.view.php', 'templates/footer.view.php'), $data);
        } else {
            //Procesar el alta
            $modelo = new \Com\Daw2\Models\UsuarioSistemaModel();
            $saneado = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            if ($modelo->insertUsuario($saneado)) {
                header('location: /usuarios-sistema');
            } else {
                $data = [];
                $data['titulo'] = 'Insertar usuario';
                $data['seccion'] = '/usuarios-sistema/add';
                $data['tituloDiv'] = 'Alta usuario';

                $usuariosModel = new \Com\Daw2\Models\UsuarioSistemaModel();
                $data['input'] = $usuariosModel->getAll();
                $data['input'] = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
                $data['errores'] = ['codigo' => 'Error desconocido al insertar el usuario'];

                $this->view->showViews(array('templates/header.view.php', 'edit.usuario_sistema.view.php', 'templates/footer.view.php'), $data);
            }
        }
    }

//    private function checkFormEdit(array $post, bool $alta = true) {        
//        $sql = "SELECT * FROM usuario_sistema us WHERE us.email = :email AND us.id_usuario=:id_usuario";
//        $stmt = $this->pdo->query($sql)->fetchAll();
//        $errores = $this->checkForm($post, $alta);
//        
//    }
    
    private function checkForm(array $post, bool $alta = true) {
        $errores = [];
        if ($post['nombre'] == '') {
            $errores['nombre'] = 'Inserte un nombre de usuario';
        } else if (strlen($post['nombre']) > 20) {
            $errores['nombre'] = 'El nombre de usuario debe tener una longitud máxima de 20 caracteres';
        } else if ($alta) {
            $modelo = new \Com\Daw2\Models\UsuarioSistemaModel();
            $row = $modelo->loadUsuario($post['nombre']);
            if (!is_null($row)) {
                $errores['nombre'] = 'El nombre de usuario ya está en uso.';
            }
        }

        if (strlen($post['email']) == 0) {
            $errores['email'] = 'Debe insertar un email.';
        } else if (!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = 'Debe insertar un email válido.';
        } else if ($alta) {
            $modelo = new \Com\Daw2\Models\UsuarioSistemaModel();
            $row = $modelo->loadEmail($post['email']);
            if (!is_null($row)) {
                $errores['email'] = 'El email ya está en uso.';
            }
        }

        if (empty($post['password'])) {
            $errores['password'] = "La contraseña no puede estar vacía";
        } else
        if (strlen($post['password']) < 8) {
            $errores['password'] = "La contraseña debe contener al menos 8 caracteres";
        } else
        if (!preg_match('`[a-z]`', $post['password'])) {
            $errores['password'] = "La contraseña debe contener al menos una letra minúscula";
        } else
        if (!preg_match('`[A-Z]`', $post['password'])) {
            $errores['password'] = "La contraseña debe contener al menos una letra mayúscula";
        } else
        if (!preg_match('`[0-9]`', $post['password'])) {
            $errores['password'] = "La contraseña debe contener al menos un caracter numérico";
        }

        if ($post['password'] != $post['confirm-password']) {
//            $errores['password'] = 'Las contraseñas no coinciden';
            $errores['confirm-password'] = 'Las contraseñas no coinciden';
        }

        if (empty($post['id_rol'])) {
            $errores['rol'] = "Debe seleccionar algún rol";
        } else {
            $rolModel = new \Com\Daw2\Models\AuxRolModel();
            if (!filter_var($post['id_rol'], FILTER_VALIDATE_INT) || is_null($rolModel->loadRol((int) $post['id_rol']))) {
                $errores['rol'] = "Valor inválido";
            }
        }


        if (empty($post['id_idioma'])) {
            $errores['idioma'] = "Debe seleccionar algún idioma";
        } else {
            $idiomaModel = new \Com\Daw2\Models\AuxIdiomasModel();
            if (!filter_var($post['id_idioma'], FILTER_VALIDATE_INT) || is_null($idiomaModel->loadIdioma((int) $post['id_idioma']))) {
                $errores['idioma'] = "Valor inválido";
            }
        }

//        var_dump($post);
//        die;

        return $errores;
    }
    
    

    function mostrarEdit(string $id) {
        $data = [];
        $modelo = new \Com\Daw2\Models\UsuarioSistemaModel();
        $input = $modelo->loadId($id);
        if (is_null($input)) {
            header('location: /usuarios-sistema');
        } else {
            $data['titulo'] = 'Editando usuario: ' . $input['nombre'];
            $data['tituloDiv'] = 'Modificar usuario';
            $data['seccion'] = '/usuarios-sistema/edit';

            $data['input'] = $input;

            $idiomasModel = new \Com\Daw2\Models\AuxIdiomasModel();
            $data['idiomas'] = $idiomasModel->getAll();

            $rolModel = new \Com\Daw2\Models\AuxRolModel();
            $data['roles'] = $rolModel->getAll();

            $this->view->showViews(array('templates/header.view.php', 'edit.usuario_sistema.view.php', 'templates/footer.view.php'), $data);
        }
    }

    function processEdit() {
        $errores = $this->checkFormEdit($_POST);
        if (count($errores) > 0) {
            $data = [];
            $data['titulo'] = 'Editar usuario';
            $data['seccion'] = '/usuarios-sistema/edit';
            $data['tituloDiv'] = 'Modificar usuario';

            $rolModel = new \Com\Daw2\Models\AuxRolModel();
            $data['roles'] = $rolModel->getAll();

            $idiomaModel = new \Com\Daw2\Models\AuxIdiomasModel();
            $data['idiomas'] = $idiomaModel->getAll();

            $data['input'] = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            $data['errores'] = $errores;

            $this->view->showViews(array('templates/header.view.php', 'edit.usuario_sistema.view.php', 'templates/footer.view.php'), $data);
        } else {
            //Procesar el alta
            $modelo = new \Com\Daw2\Models\UsuarioSistemaModel();
            $saneado = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            if ($modelo->updateUsuario($saneado)) {
                header('location: /usuarios-sistema');
            } else {
                $data = [];
                $data['titulo'] = 'Editar usuario';
                $data['seccion'] = '/usuarios-sistema/edit';
                $data['tituloDiv'] = 'Modificar usuario';

                $usuariosModel = new \Com\Daw2\Models\UsuarioSistemaModel();
                $data['input'] = $usuariosModel->getAll();
                $data['input'] = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
                $data['errores'] = ['codigo' => 'Error desconocido al modificar el usuario'];

                $this->view->showViews(array('templates/header.view.php', 'edit.usuario_sistema.view.php', 'templates/footer.view.php'), $data);
            }
        }
    }
    
    function view(string $id) {
        $data = [];
        $modelo = new \Com\Daw2\Models\UsuarioSistemaModel();
        $input = $modelo->loadId($id);
        if (is_null($input)) {
            header('location: /usuarios-sistema');
        } else {
            $data['titulo'] = 'Visualizando usuario: ' . $input['nombre'];
            $data['tituloDiv'] = 'Ver usuario';
            $data['seccion'] = '/usuarios-sistema/view';

            $data['input'] = $input;

            $idiomasModel = new \Com\Daw2\Models\AuxIdiomasModel();
            $data['idiomas'] = $idiomasModel->getAll();

            $rolModel = new \Com\Daw2\Models\AuxRolModel();
            $data['roles'] = $rolModel->getAll();

            $this->view->showViews(array('templates/header.view.php', 'edit.usuario_sistema.view.php', 'templates/footer.view.php'), $data);
        }
    }

    public function delete(string $codigo) {
        $modelo = new \Com\Daw2\Models\UsuarioSistemaModel();
        $data = [];
        if ($modelo->deleteUsuario($codigo)) {

            $data['mensaje'] = array(
                'class' => 'success',
                'texto' => "Usuario $codigo eliminado con éxito");
        } else {
            $data['mensaje'] = array(
                'class' => 'danger',
                'texto' => 'No se ha logrado eliminar el usuario ' . $codigo);
        }
        header('location: /usuarios-sistema');
    }
}
