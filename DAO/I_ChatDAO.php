<?php namespace DAO;

use Models\Chat as Chat;
use DAO\Connection as Connection;

interface I_ChatDAO{

    function Add(Chat $chat); 
    function GetChatByIds($idEmisor, $idReceptor); 
    function GetChatsByUser($idUser); 
    function cambiarEstado($idMensaje, $estado);
    function GetMensajesByEstado($idReceptor);
}

?>