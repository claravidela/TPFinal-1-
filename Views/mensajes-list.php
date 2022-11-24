<?php
include("header.php");
include("nav-bar.php");
?>

<div class="container">
    <h2 id="list-title">Mis Mensajes</h2>
    <?php if(empty($chats)) { ?>
            <div class="alert alert-primary" role="alert" style="width: fit-content">
                <?php echo "Todavia no tiene mensajes." ?>
            </div>
    <?php } ?>
                <table class="table bg-light">
                    <thead>
                    <th>Usuario</th>
                    <th>Accion</th>
                    </thead>
                    <tbody>
                    <?php
                    
                    foreach ($userList as $user) {
                        $flag= 0; 
                        if($mensNoLeidos != null){
                            foreach($mensNoLeidos as $mens){
                                if($user->getId() == $mens->getIdEmisor()){
                                    $flag= 1;
                                }
                            }

                        }
                        ?>
                        <tr>
                            <td><?php echo $user->getNombre() . " " . $user->getApellido()  ?></td>
                            <td>     
                                <?php if($flag){?>
                                    <a href="<?php echo FRONT_ROOT . "Chat/ShowMensajeViewFromBottonChat/" . $user->getId() ?>" class="btn btn-danger btn-sm">
                                <?php } else { ?>
                                    <a href="<?php echo FRONT_ROOT . "Chat/ShowMensajeViewFromBottonChat/" . $user->getId() ?>" class="btn btn-light btn-sm">
                                <?php } ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="current color" class="bi bi-chat" viewBox="0 0 16 16">
                                        <path d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"></path>
                                    </svg>
                                    Ir al chat </a>
                             

                            </td>
                        </tr>
                        <?php }?>
                    </tbody>
                </table>
        </div>    
    
    <a href="<?php echo FRONT_ROOT . "Chat/ShowAddMensajeView";?>"><button class="btn btn-primary">Nuevo Mensaje</button></a><br>

    
    
    </div>


<?php 
 include("footer.php"); 
?>