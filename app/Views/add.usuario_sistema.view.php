<!-- Content Row -->
<?php 
//var_dump($roles);
//var_dump($idiomas);
?>
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div
                class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Introduzca los datos del nuevo usuario</h6>                                    
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <form action="/usuarios-sistema/add" method="post">         
                    <!--form method="get"-->
                    <div class="row">
                        <div class="mb-3 col-sm-12">
                            <label for="email">Nombre de usuario</label>
                            <input class="form-control" id="username" type="text" name="username" placeholder="username" value="<?php echo isset($input['username']) ? $input['username'] : ''; ?>" required>
                                   <p class="text-danger"><?php echo isset($errores['username']) ? $errores['username'] : ''; ?></p>
                        </div>
                        <div class="mb-3 col-sm-12">
                            <label for="email">Correo electrónico</label>
                            <input class="form-control" id="email" type="email" name="email" placeholder="email@domain.com" value="<?php echo isset($input['email']) ? $input['email'] : ''; ?>" required>
                                   <p class="text-danger"><?php echo isset($errores['email']) ? $errores['email'] : ''; ?></p>
                        </div>
                        <div class="mb-3 col-sm-12">
                            <label for="password">Contraseña</label>
                            <input class="form-control" id="password" type="password" name="password" placeholder="Escribe una contraseña" value="<?php echo isset($input['password']) ? $input['password'] : ''; ?>" required>
                                   <p class="text-danger"><?php echo isset($errores['password']) ? $errores['password'] : ''; ?></p>

                        </div>
                        <div class="mb-3 col-sm-12">
                            <label for="confirm-password">Confirmar contraseña</label>
                            <input class="form-control" id="confirm-password" type="password" name="confirm-password" placeholder="Confirma la contraseña" value="<?php echo isset($input['confirm-password']) ? $input['confirm-password'] : ''; ?>" required>
                                   <p class="text-danger"><?php echo isset($errores['confirm-password']) ? $errores['confirm-password'] : ''; ?></p>

                        </div>
                        <div class="mb-3 col-sm-12">
                                <label for="rol">Rol</label>
                                <select name="rol" id="rol" class="form-control select2" data-placeholder="Rol" required>
                                    <option value="">-</option>
                                    <?php foreach ($roles as $rol) { ?>
                                        <option value="<?php echo $rol['id_rol']; ?>" <?php echo (isset($input['id_rol']) && $rol['id_rol'] == $input['id_rol']) ? 'selected' : ''; ?> ><?php echo ucfirst($rol['nombre_rol']); ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                        </div>
                        <div class="mb-3 col-sm-12">
                                <label for="idioma">Idioma:</label>
                                <select name="idioma" id="idioma" class="form-control select2" data-placeholder="Idioma" required>
                                    <option value="">-</option>
                                    <?php foreach ($idiomas as $idioma) { ?>
                                        <option value="<?php echo $idioma['id_idioma']; ?>" <?php echo (isset($input['id_idioma']) && $idioma['id_idioma'] == $input['id_idioma']) ? 'selected' : ''; ?>><?php echo ucfirst($idioma['nombre_idioma']); ?></option>                                        <?php
                                    }
                                    ?>
                                </select>
                        </div>
                        <div class="col-12 text-right">                            
                            <input type="submit" value="Enviar" name="enviar" class="btn btn-primary"/>
                            <a href="/usuarios-sistema/add" class="btn btn-danger ml-3">Cancelar</a>                            
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>                        
</div>