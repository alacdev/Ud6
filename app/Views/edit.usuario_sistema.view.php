<!-- Content Row -->

<div class="row">
    <?php
// var_dump($input);
// var_dump($roles);
    if (isset($errores)) {
        echo var_dump($errores);
        //var_dump($_POST);
    }
    ?>
    <div class="col-12">
        <div class="card shadow mb-4">
            <div
                class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary"><?php echo $tituloDiv; ?></h6>                                    
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <form action="" method="post">         
                    <div class="row">
                        <div class="mb-3 col-sm-6">
                            <label for="nombre">Nombre de usuario</label>
                            <input class="form-control" id="nombre" type="text" name="nombre" placeholder="username" value="<?php echo isset($input['nombre']) ? $input['nombre'] : ''; ?>"
                                   <?php echo ($seccion === '/usuarios-sistema/view') ? 'readonly="readonly"' : '' ?> >
                            <p class="text-danger"><?php echo isset($errores['nombre']) ? $errores['nombre'] : ''; ?></p>
                        </div>
                        <div class="mb-3 col-sm-6">
                            <label for="email">Correo electrónico</label>
                            <input class="form-control" id="email" type="email" name="email" placeholder="email@domain.com" value="<?php echo isset($input['email']) ? $input['email'] : ''; ?>" 
                                   <?php echo ($seccion === '/usuarios-sistema/view') ? 'readonly="readonly"' : '' ?> >
                            <p class="text-danger"><?php echo isset($errores['email']) ? $errores['email'] : ''; ?></p>
                        </div>     
                        <?php
                        if ($seccion != '/usuarios-sistema/view') {
                            ?>
                            <div class="mb-3 col-sm-6">
                                <label for="password">Contraseña</label>
                                <input class="form-control" id="password" type="password" name="password" placeholder="Escribe una contraseña" value="<?php echo isset($input['password']) ? $input['password'] : ''; ?>">
                                <p class="text-danger"><?php echo isset($errores['password']) ? $errores['password'] : ''; ?></p>
                            </div>
                            <div class="mb-3 col-sm-6">
                                <label for="confirm-password">Confirmar contraseña</label>
                                <input class="form-control" id="confirm-password" type="password" name="confirm-password" placeholder="Confirma la contraseña" value="<?php echo isset($input['confirm-password']) ? $input['confirm-password'] : ''; ?>" >
                                <p class="text-danger"><?php echo isset($errores['confirm-password']) ? $errores['confirm-password'] : ''; ?></p>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="mb-3 col-sm-6">
                            <label for="id_rol">Rol</label>
                            <select name="id_rol" id="rol" class="form-control select2" data-placeholder="Rol" <?php echo ($seccion === '/usuarios-sistema/view') ? 'disabled' : '' ?> >
                                <option value="">-</option>
                                <?php foreach ($roles as $rol) { ?>
                                    <option value="<?php echo $rol['id_rol']; ?>" <?php echo (isset($input['id_rol']) && $rol['id_rol'] == $input['id_rol']) ? 'selected' : ''; ?> ><?php echo ucfirst($rol['nombre_rol']); ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                            <p class="text-danger"><?php echo isset($errores['rol']) ? $errores['rol'] : ''; ?></p>
                        </div>
                        <div class="mb-3 col-sm-6">
                            <label for="id_idioma">Idioma:</label>
                            <select name="id_idioma" id="id_idioma" class="form-control select2" data-placeholder="Idioma" <?php echo ($seccion === '/usuarios-sistema/view') ? 'disabled' : '' ?> >
                                <option value="">-</option>
                                <?php foreach ($idiomas as $idioma) { ?>
                                    <option value="<?php echo $idioma['id_idioma']; ?>" <?php echo (isset($input['id_idioma']) && $idioma['id_idioma'] == $input['id_idioma']) ? 'selected' : ''; ?>><?php echo ucfirst($idioma['nombre_idioma']); ?></option>                                        <?php
                                }
                                ?>
                            </select>
                            <p class="text-danger"><?php echo isset($errores['idioma']) ? $errores['idioma'] : ''; ?></p>
                        </div>

                        <div class="col-12 text-right">                            
                            <?php
                            if ($seccion != '/usuarios-sistema/view') {
                                ?>
                                <input type="submit" value="Enviar" name="enviar" class="btn btn-primary"/>
                                <?php
                            }
                            ?>

                            <a href="/usuarios-sistema" class="btn btn-danger ml-3">Cancelar</a>                            
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>                        
</div>