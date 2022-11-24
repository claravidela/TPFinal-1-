<?php
include("header.php");
include("nav-bar.php");
?>

<div style="margin:0 auto; padding-bottom:25px; background:#EBF4FB; width:504px; border:1px solid #ACD8F0;">
  <div style="padding:12.5px 25px 12.5px 25px;">
  <form action="<?php echo FRONT_ROOT . "Chat/Add"; ?>" method="POST"> 
   <?php if($_SESSION["loggedUser"]->getTipo() == 1){ ?>
    <label> Buscar Guardian </label> 
    <select name="idReceptor" style="width: 200px;"> 
        <option> Elige un Guardian </option> 
            <?php foreach($guardianes as $guardian){ ?>
                <option value="<?php echo $guardian->getId(); ?>"> <?php echo $guardian->getNombre() . " " . $guardian->getApellido(); ?> </option> 
            <?php } } else{ ?>
    <label> Buscar Duenio </label> 
    <select name="idReceptor" style="width: 200px;"> 
        <option> Elige un Duenio </option> 
            <?php foreach($duenios as $duenio){ ?>
                <option value="<?php echo $duenio->getId(); ?>"> <?php echo $duenio->getNombre() . " " . $duenio->getApellido(); ?> </option>
            <?php } } ?> 
    <label> Escriba aqui el mensaje </label> 
    <input type="textarea" name="mensaje" rows="7" cols="61"> 
    <input type="submit" value="Enviar"> 

</form> 
            </div>
            </div>







<?php 
 include("footer.php"); 
?>