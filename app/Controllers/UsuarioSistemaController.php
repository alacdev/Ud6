<?php

declare(strict_types=1);

namespace Com\Daw2\Controllers;

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
        $data['titulo'] = 'Añadir usuario';
        $data['seccion'] = '/usuarios-sistema/add';
        $data['tituloDiv'] = 'Alta usuario';

        $modelo = new \Com\Daw2\Models\UsuarioSistemaModel();
        $data['usuarios'] = $modelo->getAll();

        $rolModel = new \Com\Daw2\Models\AuxRolModel();
        $data['roles'] = $rolModel->getAll();

        $idiomaModel = new \Com\Daw2\Models\AuxIdiomasModel();
        $data['idiomas'] = $idiomaModel->getAll();

        $this->view->showViews(array('templates/header.view.php', 'add.usuario_sistema.view.php', 'templates/footer.view.php'), $data);
    }

    function processAdd() {
        $errores = $this->checkForm($_POST);
        if (count($errores) > 0) {
            $data = [];
            $data['titulo'] = 'Añadir usuario';
            $data['seccion'] = '/usuarios-sistema/add';
            $data['tituloDiv'] = 'Alta usuario';

            $usuariosModel = new \Com\Daw2\Models\UsuarioSistemaModel();
            $data['usuarios'] = $usuariosModel->getAll();

            $rolModel = new \Com\Daw2\Models\AuxRolModel();
            $data['roles'] = $rolModel->getAll();

            $idiomaModel = new \Com\Daw2\Models\AuxIdiomasModel();
            $data['idiomas'] = $idiomaModel->getAll();

            $data['input'] = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            $data['errores'] = $errores;

            $this->view->showViews(array('templates/header.view.php', 'add.usuario_sistema.view.php', 'templates/footer.view.php'), $data);
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
                $data['usuarios'] = $usuariosModel->getAll();
                $data['input'] = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
                $data['errores'] = ['codigo' => 'Error indeterminado al realizar el guardado'];

                $this->view->showViews(array('templates/header.view.php', 'add.usuario_sistema.view.php', 'templates/footer.view.php'), $data);
            }
        }
    }

    private function checkForm(array $post, bool $alta = true) {
        $errores = [];
        if ($post['username'] == '') {
            $errores['username'] = 'Inserte un nombre de usuario';
        } else if (strlen($post['username']) > 20) {
            $errores['username'] = 'El nombre de usuario debe tener una longitud máxima de 20 caracteres';
        } else if ($alta) {
            $modelo = new \Com\Daw2\Models\UsuarioSistemaModel();
            $row = $modelo->loadUsuario($post['username']);
            if (!is_null($row)) {
                $errores['username'] = 'El nombre de usuario ya está en uso.';
            }
        }
        //Es una modificación
//        else{
//            $modelo = new \Com\Daw2\Models\ProductoModel();
//            $row = $modelo->loadProducto($post['codigo']);
//            if(is_null($row)){
//                $errores['codigo'] = 'No se encuentra en base de datos el producto que se desea editar.';
//            }
//        }

        if (strlen($post['email']) == 0) {
            $errores['email'] = 'Debe insertar un email.';
        } else if ($alta) {
            $modelo = new \Com\Daw2\Models\UsuarioSistemaModel();
            $row = $modelo->loadEmail($post['email']);
            if (!is_null($row)) {
                $errores['username'] = 'El email ya está en uso.';
            }
        }

        if (strlen($post['password']) < 8) {
            $errores['password'] = "La contraseña debe contener al menos 8 caracteres";
        }
        if (!preg_match('`[a-z]`', $post['password'])) {
            $errores['password'] = "La contraseña debe contener al menos una letra minúscula";
        }
        if (!preg_match('`[A-Z]`', $post['password'])) {
            $errores['password'] = "La contraseña debe contener al menos una letra mayúscula";
        }
        if (!preg_match('`[0-9]`', $post['password'])) {
            $errores['password'] = "La contraseña debe contener al menos un caracter numérico";
        }

        if ($post['password'] != $post['confirm-password']) {
            $errores['password'] = 'Las contraseñas no coinciden';
            $errores['confirm-password'] = 'Las contraseñas no coinciden';
        }
        return $errores;
    }
}
