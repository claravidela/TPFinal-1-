<?php

namespace Controllers;

use DAO\DuenioDAO;
use DAO\GuardianDAO;
use DAO\MascotaDAO;
use DAO\ReservaDAO;
use Exception;
use Models\Duenio;
use Models\EstadoReserva;
use Models\Mascota;
use Models\Chat; 
use DAO\ChatDAO; 

class DuenioController
{
    private $duenioDAO;
    private $mascotaDAO;

    public function __construct()
    {
        $this->duenioDAO = new DuenioDAO();
        $this->mascotaDAO = new MascotaDAO();
    }

    private function validateSession()
    {
        if (isset($_SESSION["loggedUser"]) && $_SESSION["loggedUser"]->getTipo() == 1) {
            return true;
        } else {
            HomeController::Index();
        }
    }

    public function ShowDuenioHome()
    {   
        if ($this->validateSession()) {
            try {
                $avisoMensaje= 0; 
                $flag = 0;
                $cont = 0;
                $reservaDAO = new ReservaDAO();
                $chatDao= new ChatDAO();
        
                $listaReservas = $reservaDAO->GetListaReservasByDuenio($_SESSION["loggedUser"]->getID());
                $mensajes= $chatDao->GetChatsByUser($_SESSION["loggedUser"]->getId());
                foreach ($listaReservas as $reserva) {
                    if ($reserva->getEstado() == EstadoReserva::ESPERA->value) {
                        $flag = 1;
                        $cont++;
                    }
                }
                foreach($mensajes as $msg){
                    if($msg->getIdReceptor() == $_SESSION["loggedUser"]->getId() && $msg->getEstado() == 'enviado'){
                        $avisoMensaje= 1;
                    }
                }
                require_once(VIEWS_PATH . "home-duenio.php");
            } catch (Exception $ex) {

                HomeController::ShowErrorView();
            }
        }
    }



    public function ShowListaGuardianesView($fechaInicio, $fechaFin, $idMascota, $listaGuardianes)
    {

        if ($this->validateSession()) {

            require_once(VIEWS_PATH . "list-guardianes.php");
        }
    }

    public function ShowFiltrarGuardianesView($alert = "")
    {   
        if ($this->validateSession()) {
            try {
                $mascotaList = $this->mascotaDAO->GetListaMascotasByDuenio($_SESSION["loggedUser"]->getId());
                require_once(VIEWS_PATH . "filtrar-guardianes.php");
            } catch (Exception $ex) {
                HomeController::ShowErrorView();
            }
        }
    }

    public function ShowProfileView()
    {   
        if ($this->validateSession()) {
            try {
                $reservaDAO = new ReservaDAO();
                $mascotaList = $this->mascotaDAO->GetListaMascotasByDuenio($_SESSION["loggedUser"]->getId());
                $listaReservas = $reservaDAO->GetListaReservasDuenioByEstado($_SESSION["loggedUser"]->getId(), EstadoReserva::FINALIZADA->value);
                require_once(VIEWS_PATH . "profile-usuario.php");
            } catch (Exception $ex) {
                HomeController::ShowErrorView();
            }
        }
    }

    public function Add($nombre, $apellido, $telefono, $email, $password, $rutaFoto)
    {
        try {
            $guardianDAO = new GuardianDAO();

            if (($this->duenioDAO->GetDuenioByEmail($email) == null) && ($guardianDAO->GetGuardianByEmail($email) == null)) {
                
                $duenio = new Duenio($nombre, $apellido, $telefono, $email, $password);

                if ($rutaFoto["tmp_name"] != "") {
                    $temp = $rutaFoto["tmp_name"];
                    $aux = explode("/", $rutaFoto["type"]);
                    $type = $aux[1];

                    $name = $email . "." . $type;

                    move_uploaded_file($temp, ROOT . VIEWS_PATH . "/img/" . $name);
                    chmod(ROOT . VIEWS_PATH . "/img/" . $name, 0777);

                    $duenio->setRutaFoto($name);
                } else {
                    $duenio->setRutaFoto("undefinedProfile.png");
                }

                
                $this->duenioDAO->Add($duenio);

                
                $duenio = $this->duenioDAO->GetDuenioByEmail($duenio->getEmail());

                $duenio->setPassword(null);
                
                $_SESSION["loggedUser"] = $duenio;
                
                $this->ShowDuenioHome();
            } else {
                $alert = "El email ingresado ya existe.";
                $type = 1;
                $homeController = new HomeController(); 
                $homeController->ShowRegisterView($type, $alert);
            }
        } catch (Exception $ex) {
            HomeController::ShowErrorView();
        }
    }


    public function FiltrarGuardianes($fechaInicio = "", $fechaFin = "", $idMascota = "")
    {
        if ($this->validateSession()) {
            if ($fechaInicio != "" && $fechaFin != "" && $idMascota != "") {
                try {

                    $guardianDAO = new GuardianDAO();

                    $listaGuardianes = $guardianDAO->GetAll();

                    $listaGuardianes = $this->FiltrarGuardianesPorFecha($listaGuardianes, $fechaInicio, $fechaFin);

                    $mascota = $this->mascotaDAO->GetMascotaById($idMascota);
                    $listaGuardianes = $this->FiltrarGuardianesPorTamanio($listaGuardianes, $mascota->getTamanioDescripcionComp());

                    $listaGuardianes = $this->FiltrarGuardianesPorRaza($listaGuardianes, $mascota->getAnimal(), $mascota->getRaza(), $fechaInicio, $fechaFin);

                    if (!empty($listaGuardianes)) {
                        $this->ShowListaGuardianesView($fechaInicio, $fechaFin, $idMascota, $listaGuardianes);
                    } else {
                        $alert = "No hay guardianes disponibles para las fechas y mascota seleccionadas.";
                        $this->ShowFiltrarGuardianesView($alert);
                    }
                } catch (Exception $ex) {
                    HomeController::ShowErrorView();
                }
            } else {
                $this->ShowFiltrarGuardianesView();
            }
        }
    }


    private function FiltrarGuardianesPorFecha($listaGuardianes, $fechaInicio, $fechaFin)
    {
        if ($this->validateSession()){
            try {

                $timeInicio = strtotime($fechaInicio);

                while ($timeInicio <= strtotime($fechaFin)) {

                    $dias[] = $this->traducirDias(date("l", $timeInicio));

                    $timeInicio += 86400;
                }

                $listaGuardianesDisponibles = array();

                foreach ($listaGuardianes as $guardian) {

                    $disponibilidad = $guardian->getDisponibilidad();

                    $flag = 1;

                    foreach ($dias as $dia) {
                        if (!in_array($dia, $disponibilidad)) {
                            $flag = 0;
                        }
                    }

                    if ($flag) {
                        array_push($listaGuardianesDisponibles, $guardian);
                    }
                }

                return $listaGuardianesDisponibles;
            } catch (Exception $ex) {
                HomeController::ShowErrorView();
            }
        }
    }

    private function FiltrarGuardianesPorTamanio($listaGuardianes, $tamanio)
    {
        try {

            $listaFiltrada = array();

            foreach ($listaGuardianes as $guardian) {
                if (in_array($tamanio, $guardian->getTamanioMascotaCuidar())) {
                    array_push($listaFiltrada, $guardian);
                }
            }

            return $listaFiltrada;
        } catch (Exception $ex) {
            HomeController::ShowErrorView();
        }
    }

    private function FiltrarGuardianesPorRaza($listaGuardianes, $animal, $raza, $fechaInicio, $fechaFin)
    {
        try {

            $reservaDAO = new ReservaDAO();
            $listaFiltrada = array();

            $timeInicio = strtotime($fechaInicio);
            $timeFin = strtotime($fechaFin);

            while ($timeInicio <= $timeFin) {

                $dias[] = date("Y-m-d", $timeInicio);

                $timeInicio += 86400;
            }

            foreach ($listaGuardianes as $guardian) {

                $flag = 1;

                foreach ($dias as $dia) {
                    $reserva = $reservaDAO->GetReservaGuardianByDia($guardian->getId(), $dia);

                    if ($reserva && ($reserva->getEstado() == EstadoReserva::ESPERA->value || $reserva->getEstado() == EstadoReserva::CONFIRMADA->value || $reserva->getEstado() == EstadoReserva::EN_CURSO->value)) {
                        $mascota = $this->mascotaDAO->GetMascotaById($reserva->getFkIdMascota());

                        if ($mascota->getAnimal() != $animal && $mascota->getRaza() != $raza) {
                            $flag = 0;
                        }
                    }
                }

                if ($flag) {
                    $listaFiltrada[] = $guardian;
                }
            }

            return $listaFiltrada;
        } catch (Exception $ex) {
            HomeController::ShowErrorView();
        }
    }


    private function traducirDias($diaSemana)
    {

        switch ($diaSemana) {
            case "Monday":
                $diaSemana = "Lunes";
                break;
            case "Tuesday":
                $diaSemana = "Martes";
                break;
            case "Wednesday":
                $diaSemana = "Miercoles";
                break;
            case "Thursday":
                $diaSemana = "Jueves";
                break;
            case "Friday":
                $diaSemana = "Viernes";
                break;
            case "Saturday":
                $diaSemana = "Sabado";
                break;
            case "Sunday":
                $diaSemana = "Domingo";
                break;
        }

        return $diaSemana;
    }
}
