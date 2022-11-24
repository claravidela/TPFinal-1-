<?php
include('header.php');
?>

<div class="login container-fluid">

    <main class="form-signin w-100 m-auto text-center">
        <form class="form-center" action="<?php echo FRONT_ROOT . "Home/ChangePassword" ?>" method="POST">
            <a href="<?php echo FRONT_ROOT . "Home/Index"; ?>">
                <img class="mb-4 img-unselect" src=<?php echo ASSETS_PATH . "logo.png" ?> alt="Pet Hero" width="250" height="250">
            </a>
            <?php if($alert != ''){
                echo $alert; 
            }?>
            <div class="form-floating">
            <input type="email" name="email" class="form-control" id="floatingInput" placeholder="nombre@example.com" required>
            <label for="floatingInput">Ingrese su Email</label>
          </div>
          <div class="form-floating">
            <input type="password" name="newPassword" class="form-control" id="floatingPassword" placeholder="contrase&ntilde;a" required>
            <label for="floatingPassword">Nueva Contrase&ntilde;a</label>
          </div>
          <div class="form-floating">
            <input type="text" name="guid" class="form-control" id="floatingPassword" placeholder="codigo" required>
            <label for="floatingPassword">Ingrese el Codigo</label>
          </div>
            <br>
            <button class="w-100 btn btn-lg btn-primary" type="submit">Recuperar Contrasenia</button><br><br>
            <a href="<?php echo  FRONT_ROOT . "Home/Index" ?>">Iniciar Sesi&oacute;n</a>
        </form>
    </main>
</div>

<?php
include('footer.php');
?>