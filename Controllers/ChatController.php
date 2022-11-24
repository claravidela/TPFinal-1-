<?php namespace Controllers; 

use Models\Chat as Chat; 
use DAO\ChatDAO as ChatDAO; 
use DAO\DuenioDAO as DuenioDAO; 
use DAO\GuardianDAO as GuardianDAO; 
use Exception;

class ChatController{

    private $chatDao; 
    private $duenioDao; 
    private $guardianDao; 

    public function __construct(){
        $this->chatDao= new ChatDAO();
        $this->duenioDao= new DuenioDAO(); 
        $this->guardianDao= new GuardianDAO();
    }

    public function validateSession()
    {
        if (isset($_SESSION["loggedUser"])) {
            return true;
        } else {
            HomeController::Index("Permisos Insuficientes");
        }
    }

    public function Add($idReceptor, $mensaje){
        if($this->validateSession()){ 
            try{ 
                $chat= new Chat();

                $chat->setidReceptor($idReceptor); 
                $chat->setIdEmisor($_SESSION["loggedUser"]->getId()); 
                $chat->setMensaje($mensaje);

                $this->chatDao->Add($chat);
                $this->ShowMensajeViewFromAdd($idReceptor); 
            } catch(Exception $ex){
                HomeController::ShowErrorView();
            }
        }
    }

    public function ShowMensajesView($alert= ''){
        if($this->validateSession()){
            try{
                $chats= $this->chatDao->GetChatsByUser($_SESSION["loggedUser"]->getId());
        
                $finalList = array();
                foreach ($chats as $chat) {
                    if ($chat->getIdReceptor() != $_SESSION['loggedUser']->getId()) { 
                       array_push($finalList, $chat->getIdReceptor()); 
                    }
                    if ($chat->getIdEmisor() != $_SESSION['loggedUser']->getId()) {
                        array_push($finalList, $chat->getIdEmisor());  
                    }
                }
            
                $list = array_unique($finalList);
                
                $userList = array();
                foreach ($list as $userid) {
                    $userDuenio= $this->duenioDao->GetDuenioById($userid); 
                    $userGuardian= $this->guardianDao->GetGuardianById($userid); 
                    if($userDuenio != null){
                        array_push($userList, $userDuenio);
                    } else 
                    if($userGuardian != null){
                        array_push($userList, $userGuardian);
                    }
                    
                }

                $mensNoLeidos= $this->chatDao->GetMensajesByEstado($_SESSION["loggedUser"]->getId()); 
               
              
                require_once(VIEWS_PATH . "mensajes-list.php"); 
               
            } catch (Exception $ex) {
                HomeController::ShowErrorView();
            }
        } 

    }

    public function ShowAddMensajeView($alert= ''){
        if($this->validateSession()){
            try{
                $guardianes= array();
                $duenios= array(); 
                $guardianes= $this->guardianDao->GetAll(); 
                $duenios= $this->duenioDao->GetAll(); 
                require_once(VIEWS_PATH . "add-mensajes.php"); 
            } catch(Exception $ex){
                HomeController::ShowErrorView();
            }
        }
    }

    public function ShowMensajeViewFromBottonChat($idUser){
        if($this->validateSession()){
            try{

                $flag= 1; 
                
                $receptor= $this->duenioDao->getDuenioById($idUser);
                if($receptor != null){
                        $flag= 0;
                } 
                if($flag != 0){
                    $receptor= $this->guardianDao->getGuardianById($idUser);
                }
    
                $mensajes= $this->chatDao->GetChatByIds($_SESSION["loggedUser"]->getId(), $idUser);
                foreach($mensajes as $row){
                    $idMens= $row->getIdMensaje(); 
                    $this->chatDao->cambiarEstado($idMens, "leido");
                }               
                
                require_once(VIEWS_PATH . "chat.php");
            } catch(Exception $ex){
                HomeController::ShowErrorView();
            }
        }
    }
    public function ShowMensajeViewFromAdd($idUser){
        if($this->validateSession()){
            try{

                $flag= 1; 
                
                $receptor= $this->duenioDao->getDuenioById($idUser);
                if($receptor != null){
                        $flag= 0;
                } 
                if($flag != 0){
                    $receptor= $this->guardianDao->getGuardianById($idUser);
                }
    
                $mensajes= $this->chatDao->GetChatByIds($_SESSION["loggedUser"]->getId(), $idUser);              
                
                require_once(VIEWS_PATH . "chat.php");
            } catch(Exception $ex){
                HomeController::ShowErrorView();
            }
        }
    }
}


?>