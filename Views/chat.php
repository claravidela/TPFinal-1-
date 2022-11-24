<?php
 include("header.php");
 include("nav-bar.php");
?>


<div style="margin:0 auto; padding-bottom:25px; background:#EBF4FB; width:504px; border:1px solid #ACD8F0;">
  <div style="padding:12.5px 25px 12.5px 25px;">
        <p style="float:left;">Chat con <?php echo $receptor->getNombre(); ?> <b></b></p>
        <div style="clear:both"></div>
    </div>
     
    <div style="text-align:left; margin:0 auto; margin-bottom:25px; padding:10px; background:#fff; height:270px; width:430px; border:1px solid #ACD8F0;
    overflow:auto;"> 
    <?php foreach($mensajes as $mensaje){ 
        if($_SESSION["loggedUser"]->getId() == $mensaje->getIdEmisor()){?>

           <p style="float: left;"> <?php echo $mensaje->getMensaje(); ?> </p><br>
        <?php } else if($_SESSION["loggedUser"]->getId() == $mensaje->getIdReceptor()) { ?>
            <p style="float: right;"> <?php echo $mensaje->getMensaje(); ?> </p><br> 
            <?php } ?>
    
            
        <?php } ?>
</div>
     
    <form action="<?php echo FRONT_ROOT . "Chat/Add"; ?>" method="POST">
    <input type="hidden" name="idReceptor" value="<?php echo $receptor->getId(); ?>">
        <input name="mensaje" type="textarea"  size="63"/>
        <input type="submit"  value="Send" />
    </form>
</div>

<?php
 include("footer.php");
?>