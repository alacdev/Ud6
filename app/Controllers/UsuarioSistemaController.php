<?php

declare(strict_types=1);

namespace Com\Daw2\Controllers;

class UsuarioSistemaController extends \Com\Daw2\Core\BaseController {

    const ROL_ADMIN = 1;
    const ROL_PRODUCTOS = 2;
    const ROL_CATEGORIAS = 3;
    const ROL_PROVEEDOR = 4;
    const ROL_AUDITOR = 5;

    function mostrarLogin() {
        $this->view->show('login.php');
    }

    function processLogin() {
        $errores = $this->checkLogin($_POST);
        if (count($errores) == 0) {
            $model = new \Com\Daw2\Models\UsuarioSistemaModel();
            $usuario = $model->loadUsersByEmail($_POST['email']);
//            var_dump($_POST);
//            die();
            if (!is_null($usuario)) {
                if (password_verify($_POST['password'], $usuario['pass'])) {
                    //Datos de acceso correctos
                    $_SESSION['user'] = $usuario;
                    $_SESSION['permisos'] = $this->getPermisos($usuario['id_rol']);
                    $model->updateLastDate($usuario['id_usuario']);
                    header('location: /');
                    die();
                } else {
                    $errores['password'] = 'Datos de acceso incorrecto';
                }
            } else {
                $errores['password'] = 'Datos de acceso incorrecto';
            }
        }
        $data = [];
        $data['errores'] = $errores;
        $data['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS);
        $this->view->show('login.php', $data);
    }

    private function getPermisos(int $idRol): array {
        $permisos = [
            'usuarios-sistema' => '',
            'categorias' => '',
            'productos' => '',
            'proveedores' => ''
        ];

        switch ($idRol) {
            case self::ROL_ADMIN:
                foreach ($permisos as $zona => $perm) {
                    $permisos[$zona] = 'rwd';
                }
//                $permisos['usuarios-sistema'] = 'rwd';
//                $permisos['productos'] = 'rwd';
//                $permisos['categorias'] = 'rwd';
//                $permisos['proveedores'] = 'rwd';
                break;
            case self::ROL_AUDITOR:
                foreach ($permisos as $zona => $perm) {
                    $permisos[$zona] = 'r';
                }
                break;
            case self::ROL_PRODUCTOS:
                $permisos['productos'] = 'rwd';
                break;
            case self::ROL_CATEGORIAS:
                $permisos['categorias'] = 'rwd';
                break;
            case self::ROL_PROVEEDOR:
                $permisos['proveedores'] = 'rwd';
                break;
            default:
                break;
        }

        return $permisos;
    }

    private function checkLogin(array $data): array {
        $errores = [];
        if (empty($data['email'])) {
            $errores['email'] = 'Inserte un email';
        }
        if (empty($data['password'])) {
            $errores['password'] = 'Inserte la contraseña';
        }

        return $errores;
    }

    function mostrarTodos() {
        $data = [];
        $data['titulo'] = 'Todos los usuarios';
        $data['seccion'] = '/usuarios-sistema';

        $modelo = new \Com\Daw2\Models\UsuarioSistemaModel();
        $data['usuarios'] = $modelo->getAll();

        if (isset($_SESSION['mensaje'])) {
            $data['mensaje'] = $_SESSION['mensaje'];
            unset($_SESSION['mensaje']);
        }

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
        $errores = $this->checkAddForm($_POST);
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

    function mostrarEdit(int $id): void {
        $data = [];
        $data['titulo'] = 'Editar usuario';
        $data['seccion'] = '/usuarios-sistema/edit';
        $data['tituloDiv'] = 'Datos usuario';

        $rolModel = new \Com\Daw2\Models\AuxRolModel();
        $data['roles'] = $rolModel->getAll();

        $idiomaModel = new \Com\Daw2\Models\AuxIdiomasModel();
        $data['idiomas'] = $idiomaModel->getAll();

        $usuarioModel = new \Com\Daw2\Models\UsuarioSistemaModel();
        $data['input'] = $usuarioModel->loadUsersById($id);

        if (!is_null($data['input'])) {
            $this->view->showViews(array('templates/header.view.php', 'edit.usuario_sistema.view.php', 'templates/footer.view.php'), $data);
        } else {

            header('location: /usuarios-sistema');
        }
    }

    function processEdit(int $id): void {
        $errores = $this->checkEditForm($_POST, $id);
        if (count($errores) == 0) {
            $model = new \Com\Daw2\Models\UsuarioSistemaModel();
            if ($model->updateUsuario($id, $_POST) && ((empty($_POST['password']) || $model->editPassword($id, $_POST['password'])))) {
                header('location: /usuarios-sistema');
                die;
            } else {
                $errores['nombre'] = 'Error desconocido. No se ha editado el usuario.';
            }
        }
        $data['titulo'] = 'Editar usuario';
        $data['seccion'] = '/usuarios-sistema/edit';
        $data['tituloDiv'] = 'Datos usuario';
        $data['input'] = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);

        $rolModel = new \Com\Daw2\Models\AuxRolModel();
        $data['roles'] = $rolModel->getAll();

        $idiomaModel = new \Com\Daw2\Models\AuxIdiomasModel();
        $data['idiomas'] = $idiomaModel->getAll();

        $data['errores'] = $errores;

        $this->view->showViews(array('templates/header.view.php', 'edit.usuario_sistema.view.php', 'templates/footer.view.php'), $data);
    }

    function processBaja(int $id): void {
        if ($_SESSION['user']['id_usuario'] === $id) {
            $data['mensaje'] = array(
                'class' => 'danger',
                'texto' => "No es posible darte de baja a ti mismo");
            header('location: /usuarios-sistema');
            $_SESSION['mensaje'] = $data ['mensaje'];
        } else {
            $model = new \Com\Daw2\Models\UsuarioSistemaModel();
            $model->changeBaja($id);
        }

        $this->view->showViews(array('templates/header.view.php', 'edit.usuario_sistema.view.php', 'templates/footer.view.php'), $data);
    }

    private function checkComunForm(array $data): array {
        $errores = [];
        if (empty($data['nombre'])) {
            $errores['nombre'] = 'Inserte un nombre al usuario';
        } else if (!preg_match('/^[a-zA-Z_ ]{4,255}$/', $data['nombre'])) {
            $errores['nombre'] = 'El nombre debe estar formado por letras, espacios o _ y tener una longitud de comprendida entre 4 y 255 caracteres.';
        }
        if (empty($data['id_rol'])) {
            $errores['id_rol'] = 'Por favor, seleccione un rol';
        } else {
            $rolModel = new \Com\Daw2\Models\AuxRolModel();
            if (!filter_var($data['id_rol'], FILTER_VALIDATE_INT) || is_null($rolModel->loadRol((int) $data['id_rol']))) {
                $errores['id_rol'] = 'Valor incorrecto';
            }
        }

        if (empty($data['id_idioma'])) {
            $errores['id_idioma'] = 'Por favor, seleccione un idioma';
        } else {
            $idiomaModel = new \Com\Daw2\Models\AuxIdiomasModel();
            if (!filter_var($data['id_idioma'], FILTER_VALIDATE_INT) || is_null($idiomaModel->loadIdioma((int) $data['id_idioma']))) {
                $errores['id_idioma'] = 'Valor incorrecto';
            }
        }
        return $errores;
    }

    private function checkPassword(array $data): array {
        $errores = [];
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $data['password'])) {
            $errores['password'] = 'El password debe contener una mayúscula, una minúscula y un número y tener una longitud de al menos 8 caracteres';
        } else if ($data['password'] != $data['confirm-password']) {
            $errores['password'] = 'Las contraseñas no coinciden';
        }
        return $errores;
    }

    private function checkAddForm(array $data): array {
        $errores = $this->checkComunForm($data);
        array_merge($errores, $this->checkPassword($data));

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = 'Inserte un email válido';
        } else {
            $model = new \Com\Daw2\Models\UsuarioSistemaModel();
            $usuario = $model->loadUsersByEmail($data['email']);
            if (!is_null($usuario)) {
                $errores['email'] = 'El email seleccionado ya está en uso';
            }
        }
        return $errores;
    }

    private function checkEditForm(array $data, int $idUsuario): array {
        $errores = $this->checkComunForm($data);
        if (!empty($data['password'])) {
            array_merge($errores, $this->checkPassword($data));
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = 'Inserte un email válido';
        } else {
            $model = new \Com\Daw2\Models\UsuarioSistemaModel();
            $usuario = $model->loadUsersByEmailNotId($data['email'], $idUsuario);
            if (!is_null($usuario)) {
                $errores['email'] = 'El email seleccionado ya está en uso';
            }
        }
        return $errores;
    }

    function view(int $id) {
        $data = [];
        $modelo = new \Com\Daw2\Models\UsuarioSistemaModel();
        $input = $modelo->loadUsersById($id);
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

    public function delete(int $idUsuario) {
        $modelo = new \Com\Daw2\Models\UsuarioSistemaModel();
        $data = [];
        if ($_SESSION['user']['id_usuario'] === $idUsuario) {
            $data['mensaje'] = array(
                'class' => 'danger',
                'texto' => "No es posible eliminarte a ti mismo");
        } else
        if ($modelo->deleteUsuario($idUsuario)) {
            $data['mensaje'] = array(
                'class' => 'success',
                'texto' => "Usuario eliminado con éxito");
        } else {
            $data['mensaje'] = array(
                'class' => 'danger',
                'texto' => 'No se ha logrado eliminar el usuario ');
        }
        header('location: /usuarios-sistema');
        $_SESSION['mensaje'] = $data ['mensaje'];
    }

    public function cerrarSesion() {
        session_destroy();
        header('location: /login');
    }
}
