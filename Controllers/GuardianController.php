<?php

namespace Controllers;

use DAO\DuenioDAO;
use DAO\GuardianDAO;
use DAO\ReservaDAO;
use Models\Guardian;
use Exception;
use Models\EstadoReserva;
use DAO\ChatDAO; 

class GuardianController
{
    private $guardianDAO;

    public function __construct()
    {
        $this->guardianDAO = new GuardianDAO();
    }

    private function validateSession()
    {
        if (isset($_SESSION["loggedUser"]) && $_SESSION["loggedUser"]->getTipo() == 2) {
            return true;
        } else {
            HomeController::Index();
        }
    }

    public function ShowGuardianHome()
    {
        try{
            if($this->validateSession()){
                $avisoMensaje= 0; 
                $chatDao= new ChatDAO();
                $mensajes= $chatDao->GetChatsByUser($_SESSION["loggedUser"]->getId());
                foreach($mensajes as $msg){
                    if($msg->getIdReceptor() == $_SESSION["loggedUser"]->getId() && $msg->getEstado() == 'enviado'){
                        $avisoMensaje= 1;
                    }
                }
                require_once(VIEWS_PATH . "home-guardian.php");
            }
        }catch(Exception $ex){
            HomeController::ShowErrorView();
        }
    }

    public function ShowConfiguracionView($alert = "")
    {
        if ($this->validateSession()) {
            
                $disponibilidad = $_SESSION["loggedUser"]->getDisponibilidad();
                $tamanioArray = $_SESSION["loggedUser"]->getTamanioMascotaCuidar();
                require_once(VIEWS_PATH . "set-configuracion.php");
           
        }
    }

    public function ShowProfileView()
    {
        if ($this->validateSession()) {
            try {
                $reservaDAO = new ReservaDAO();
                $listaReservas = $reservaDAO->GetListaReservasGuardianByEstado($_SESSION["loggedUser"]->getId(), EstadoReserva::FINALIZADA->value);
                require_once(VIEWS_PATH . "profile-usuario.php");
            } catch (Exception $ex) {
                HomeController::ShowErrorView();
            }
        }
    }

    public function Add($nombre, $apellido, $telefono, $email, $password, $calle, $numero, $piso = "", $departamento = "", $codigoPostal = "", $rutaFoto = "")
    {

        try {
            $duenioDAO = new DuenioDAO();

            if (($duenioDAO->GetDuenioByEmail($email) == null) && ($this->guardianDAO->GetGuardianByEmail($email) == null)) {

                $guardian = new Guardian($nombre, $apellido, $telefono, $email, $password, $calle, $numero, $piso, $departamento, $codigoPostal);

                if ($rutaFoto["tmp_name"] != "") {
                    $temp = $rutaFoto["tmp_name"];
                    $aux = explode("/", $rutaFoto["type"]);
                    $type = $aux[1];

                    $name = $email . "." . $type;

                    move_uploaded_file($temp, ROOT . VIEWS_PATH . "/img/" . $name);
                    chmod(ROOT . VIEWS_PATH . "/img/" . $name, 0777);

                    $guardian->setRutaFoto($name);
                } else {
                    $guardian->setRutaFoto("undefinedProfile.png");
                }

                $this->guardianDAO->Add($guardian);

                $guardian = $this->guardianDAO->GetGuardianByEmail($guardian->getEmail());

                $guardian->setPassword(null);
                $_SESSION["loggedUser"] = $guardian;

                $this->ShowGuardianHome();
            } else {
                $alert = "El email ingresado ya existe.";
                $type = 2;
                $homeController = new HomeController();
                $homeController->ShowRegisterView($type, $alert);
            }
        } catch (Exception $ex) {
            HomeController::ShowErrorView();
        }
    }

    public function setConfig($dias = array(), $tamanios = array(), $precio = "")
    {
        if ($this->validateSession()) {
            try {
                $this->guardianDAO->UpdateTamanios($_SESSION["loggedUser"]->getId(), $tamanios);
                $_SESSION["loggedUser"]->setTamanioMascotaCuidar($tamanios);


                $this->guardianDAO->UpdatePrecio($_SESSION["loggedUser"]->getId(), $precio);
                $_SESSION["loggedUser"]->setPrecioXDia($precio);


                $this->guardianDAO->UpdateDisponibilidad($_SESSION["loggedUser"]->getId(), $dias);
                $_SESSION["loggedUser"]->setDisponibilidad($dias);

                $alert = "Configuracion guardada con exito &check;";

                $this->ShowConfiguracionView($alert);
            } catch (Exception $ex) {

                HomeController::ShowErrorView();
            }
        }
    }
}
