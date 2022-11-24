<?php

namespace DAO;

use \Exception as Exception;
use DAO\IGuardianDAO as IGuardianDAO;
use Models\Guardian as Guardian;
use DAO\Connection as Connection;
use Models\EstadoReserva;

class GuardianDAO implements IGuardianDAO
{
    private $connection;
    private $tableName = "guardianes";

    public function Add(Guardian $guardian)
    {
        try {

            $parameters = array();

            $query = "INSERT INTO direcciones (calle, numero, piso, departamento, codigoPostal) VALUES (:calle, :numero, :piso, :departamento, :codigoPostal);";

            $parameters["calle"] = $guardian->getCalle();
            $parameters["numero"] = $guardian->getNumero();
            $parameters["piso"] = $guardian->getPiso();
            $parameters["departamento"] = $guardian->getDepartamento();
            $parameters["codigoPostal"] = $guardian->getCodigoPostal();

            $this->connection = Connection::GetInstance();
            //se guardan los datos de direccion del guardian en la tabla direcciones
            $this->connection->ExecuteNonQuery($query, $parameters);

            $parameters = array();

            $query = "INSERT INTO guardianes (nombre, apellido, telefono, email, password, tipo, rutaFoto, alta, fk_idDireccion) VALUES (:nombre, :apellido, :telefono, :email, aes_encrypt(:password, :encryptpass), :tipo, :rutaFoto, :alta, LAST_INSERT_ID());";

            $parameters["encryptpass"] = ENCRYPTPASS;
            $parameters["nombre"] = $guardian->getNombre();
            $parameters["apellido"] = $guardian->getApellido();
            $parameters["telefono"] = $guardian->getTelefono();
            $parameters["email"] = $guardian->getEmail();
            $parameters["password"] = $guardian->getPassword();
            $parameters["tipo"] = $guardian->getTipo();
            $parameters["rutaFoto"] = $guardian->getRutaFoto();
            $parameters["alta"] = $guardian->getAlta();

            $this->connection = Connection::GetInstance();
            //se guardan los datos del guardian en la tabla guardianes
            $this->connection->ExecuteNonQuery($query, $parameters);

            $parameters = array();

            $query = "INSERT INTO tamaniomascota (pequenia) values (:pequenia);";

            $parameters["pequenia"] = 0;

            $this->connection = Connection::GetInstance();

            $this->connection->ExecuteNonQuery($query, $parameters);

            $parameters = array();
            
            $query = "UPDATE guardianes SET fk_idTamanioMascota = LAST_INSERT_ID() WHERE email = :email";

            $parameters["email"] = $guardian->getEmail();

            $this->connection = Connection::GetInstance();

            $this->connection->ExecuteNonQuery($query, $parameters);


            $parameters = array();

            $query = "INSERT INTO disponibilidades (lunes) values (:lunes);";

            $parameters["lunes"] = 0;

            $this->connection = Connection::GetInstance();

            $this->connection->ExecuteNonQuery($query, $parameters);

            $parameters = array();

            $query = "UPDATE guardianes SET fk_idDisponibilidad = LAST_INSERT_ID() WHERE email = :email";

            $parameters["email"] = $guardian->getEmail();

            $this->connection = Connection::GetInstance();

            $this->connection->ExecuteNonQuery($query, $parameters);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function GetAll()
    {
        try {
            $guardianesList = array();

            $query = "SELECT * FROM guardianes INNER JOIN direcciones ON guardianes.fk_idDireccion = direcciones.idDireccion INNER JOIN tamaniomascota ON guardianes.fk_idTamanioMascota = tamaniomascota.idTamanioMascota INNER JOIN disponibilidades ON guardianes.fk_idDisponibilidad = disponibilidades.idDisponibilidad;";

            $this->connection = Connection::GetInstance();

            $resultSet = $this->connection->Execute($query);

            foreach ($resultSet as $row) {

                $guardian = new Guardian(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

                $guardian->setId($row["idGuardian"]);
                $guardian->setNombre($row["nombre"]);
                $guardian->setApellido($row["apellido"]);
                $guardian->setTelefono($row["telefono"]);
                $guardian->setEmail($row["email"]);
                $guardian->setPrecioXDia($row["precioXDia"]);
                $guardian->setReputacion($row["reputacion"]);
                $guardian->setAlta($row["alta"]);
                $guardian->setTipo($row["tipo"]);
                $guardian->setRutaFoto($row["rutaFoto"]);

                $guardian->setCalle($row["calle"]);
                $guardian->setNumero($row["numero"]);
                $guardian->setPiso($row["piso"]);
                $guardian->setDepartamento($row["departamento"]);
                $guardian->setCodigoPostal($row["codigoPostal"]);

                $TamanioMascota = array();

                if ($row["pequenia"]) $TamanioMascota[] = "Pequeño";
                if ($row["mediana"]) $TamanioMascota[] = "Mediano";
                if ($row["grande"]) $TamanioMascota[] = "Grande";

                $guardian->setTamanioMascotaCuidar($TamanioMascota);

                $disponibilidad = array();

                if ($row["lunes"]) $disponibilidad[] = "Lunes";
                if ($row["martes"]) $disponibilidad[] = "Martes";
                if ($row["miercoles"]) $disponibilidad[] = "Miercoles";
                if ($row["jueves"]) $disponibilidad[] = "Jueves";
                if ($row["viernes"]) $disponibilidad[] = "Viernes";
                if ($row["sabado"]) $disponibilidad[] = "Sabado";
                if ($row["domingo"]) $disponibilidad[] = "Domingo";

                $guardian->setDisponibilidad($disponibilidad);

                array_push($guardianesList, $guardian);
            }

            return $guardianesList;
        } catch (Exception $ex) {
            throw $ex;
        }
    }


    public function GetGuardianByEmail($email)
    {
        try {
            $guardian = null;

            $query = "SELECT *, aes_decrypt(password, :encryptpass) as password FROM guardianes INNER JOIN direcciones ON guardianes.fk_idDireccion = direcciones.idDireccion INNER JOIN tamaniomascota ON guardianes.fk_idTamanioMascota = tamaniomascota.idTamanioMascota INNER JOIN disponibilidades ON guardianes.fk_idDisponibilidad = disponibilidades.idDisponibilidad WHERE (email = :email);";

            $parameters["email"] = $email;
            $parameters["encryptpass"] = ENCRYPTPASS;

            $this->connection = Connection::GetInstance();

            $resultSet = $this->connection->Execute($query, $parameters);

            if (isset($resultSet)) {

                foreach ($resultSet as $row) {

                    $guardian = new Guardian(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

                    $guardian->setId($row["idGuardian"]);
                    $guardian->setNombre($row["nombre"]);
                    $guardian->setApellido($row["apellido"]);
                    $guardian->setTelefono($row["telefono"]);
                    $guardian->setEmail($row["email"]);
                    $guardian->setPassword($row["password"]); //Se usa para el login
                    $guardian->setAlta($row["alta"]);
                    $guardian->setTipo($row["tipo"]);
                    $guardian->setRutaFoto($row["rutaFoto"]);
                    $guardian->setPrecioXDia($row["precioXDia"]);
                    $guardian->setReputacion($row["reputacion"]);

                    $guardian->setCalle($row["calle"]);
                    $guardian->setNumero($row["numero"]);
                    $guardian->setPiso($row["piso"]);
                    $guardian->setDepartamento($row["departamento"]);
                    $guardian->setCodigoPostal($row["codigoPostal"]);

                    $TamanioMascota = array();

                    if ($row["pequenia"]) $TamanioMascota[] = "Pequeño";
                    if ($row["mediana"]) $TamanioMascota[] = "Mediano";
                    if ($row["grande"]) $TamanioMascota[] = "Grande";

                    $guardian->setTamanioMascotaCuidar($TamanioMascota);

                    $disponibilidad = array();

                    if ($row["lunes"]) $disponibilidad[] = "Lunes";
                    if ($row["martes"]) $disponibilidad[] = "Martes";
                    if ($row["miercoles"]) $disponibilidad[] = "Miercoles";
                    if ($row["jueves"]) $disponibilidad[] = "Jueves";
                    if ($row["viernes"]) $disponibilidad[] = "Viernes";
                    if ($row["sabado"]) $disponibilidad[] = "Sabado";
                    if ($row["domingo"]) $disponibilidad[] = "Domingo";

                    $guardian->setDisponibilidad($disponibilidad);
                }

                return $guardian;
            } else {

                return null;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function GetGuardianById($idGuardian)
    {
        try {
            $guardian = null;

            $query = "SELECT * FROM guardianes INNER JOIN direcciones ON guardianes.fk_idDireccion = direcciones.idDireccion INNER JOIN tamaniomascota ON guardianes.fk_idTamanioMascota = tamaniomascota.idTamanioMascota INNER JOIN disponibilidades ON guardianes.fk_idDisponibilidad = disponibilidades.idDisponibilidad WHERE (idGuardian = :idGuardian)";

            $parameters["idGuardian"] = $idGuardian;

            $this->connection = Connection::GetInstance();

            $resultSet = $this->connection->Execute($query, $parameters);

            if (isset($resultSet)) {

                foreach ($resultSet as $row) {

                    $guardian = new Guardian(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

                    $guardian->setId($row["idGuardian"]);
                    $guardian->setNombre($row["nombre"]);
                    $guardian->setApellido($row["apellido"]);
                    $guardian->setTelefono($row["telefono"]);
                    $guardian->setEmail($row["email"]);
                    $guardian->setAlta($row["alta"]);
                    $guardian->setTipo($row["tipo"]);
                    $guardian->setRutaFoto($row["rutaFoto"]);
                    $guardian->setPrecioXDia($row["precioXDia"]);
                    $guardian->setReputacion($row["reputacion"]);

                    $guardian->setCalle($row["calle"]);
                    $guardian->setNumero($row["numero"]);
                    $guardian->setPiso($row["piso"]);
                    $guardian->setDepartamento($row["departamento"]);
                    $guardian->setCodigoPostal($row["codigoPostal"]);

                    $TamanioMascota = array();

                    if ($row["pequenia"]) $TamanioMascota[] = "Pequeño";
                    if ($row["mediana"]) $TamanioMascota[] = "Mediano";
                    if ($row["grande"]) $TamanioMascota[] = "Grande";

                    $guardian->setTamanioMascotaCuidar($TamanioMascota);

                    $disponibilidad = array();

                    if ($row["lunes"]) $disponibilidad[] = "Lunes";
                    if ($row["martes"]) $disponibilidad[] = "Martes";
                    if ($row["miercoles"]) $disponibilidad[] = "Miercoles";
                    if ($row["jueves"]) $disponibilidad[] = "Jueves";
                    if ($row["viernes"]) $disponibilidad[] = "Viernes";
                    if ($row["sabado"]) $disponibilidad[] = "Sabado";
                    if ($row["domingo"]) $disponibilidad[] = "Domingo";

                    $guardian->setDisponibilidad($disponibilidad);
                }

                return $guardian;
            } else {

                return null;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function UpdateDisponibilidad($idGuardian, $disponibilidad)
    {
        try {
            $query = "UPDATE disponibilidades INNER JOIN guardianes ON disponibilidades.idDisponibilidad = guardianes.fk_idDisponibilidad SET lunes = :lunes, martes = :martes, miercoles = :miercoles, jueves = :jueves, viernes = :viernes, sabado = :sabado, domingo = :domingo WHERE idGuardian = :idGuardian;";

            $parameters["lunes"] = in_array("Lunes", $disponibilidad) ? 1 : 0;
            $parameters["martes"] = in_array("Martes", $disponibilidad) ? 1 : 0;
            $parameters["miercoles"] = in_array("Miercoles", $disponibilidad) ? 1 : 0;
            $parameters["jueves"] = in_array("Jueves", $disponibilidad) ? 1 : 0;
            $parameters["viernes"] = in_array("Viernes", $disponibilidad) ? 1 : 0;
            $parameters["sabado"] = in_array("Sabado", $disponibilidad) ? 1 : 0;
            $parameters["domingo"] = in_array("Domingo", $disponibilidad) ? 1 : 0;

            $parameters["idGuardian"] = $idGuardian;

            $this->connection = Connection::GetInstance();

            $this->connection->ExecuteNonQuery($query, $parameters);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function UpdateTamanios($idGuardian, $tamanios)
    {
        try {
            $query = "UPDATE tamaniomascota INNER JOIN guardianes ON tamaniomascota.idTamanioMascota = guardianes.fk_idTamanioMascota SET pequenia = :pequenia, mediana = :mediana, grande = :grande WHERE idGuardian = :idGuardian;";

            $parameters["pequenia"] = in_array("Pequeño", $tamanios) ? 1 : 0;
            $parameters["mediana"] = in_array("Mediano", $tamanios) ? 1 : 0;
            $parameters["grande"] = in_array("Grande", $tamanios) ? 1 : 0;

            $parameters["idGuardian"] = $idGuardian;

            $this->connection = Connection::GetInstance();

            $this->connection->ExecuteNonQuery($query, $parameters);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function UpdatePrecio($idGuardian, $precioXDia)
    {
        try {
            $query = "UPDATE guardianes SET precioXDia = :precioXDia WHERE idGuardian = :idGuardian;";

            $parameters["precioXDia"] = $precioXDia;
            $parameters["idGuardian"] = $idGuardian;

            $this->connection = Connection::GetInstance();

            $this->connection->ExecuteNonQuery($query, $parameters);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function UpdateReputacion($idReserva)
    {
        try {

            $cant = 0;
            $suma = 0;

            $reservaDAO = new ReservaDAO();

            $reserva = $reservaDAO->GetReservaById($idReserva);

            $listaReservas = $reservaDAO->GetListaReservasByGuardian($reserva->getFkIdGuardian());

            foreach ($listaReservas as $reserva) {

                if ($reserva->getEstado() == EstadoReserva::FINALIZADA->value) {
                    $review = $reservaDAO->GetReviewByIdReserva($reserva->getIdReserva());

                    if ($review) {
                        $suma += $review->getPuntaje();
                        $cant++;
                    }
                }
            }

            if ($cant != 0) {
                $reputacion = ($suma / $cant);
            }

            $query = "UPDATE guardianes SET reputacion = :reputacion WHERE idGuardian = :idGuardian;";

            $parameters["reputacion"] = $reputacion;
            $parameters["idGuardian"] = $reserva->getFkIdGuardian();


            $this->connection = Connection::GetInstance();

            $this->connection->ExecuteNonQuery($query, $parameters);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function SetPasswordGuardian($idGuardian, $newPassword){
        try{
            $query= "UPDATE guardian SET password= aes_encrypt(:password, :encryptpass) WHERE idGuardian= :idGuardian;";

            $parameters['encryptpass']= ENCRYPTPASS;
            $parameters['password']= $newPassword;  
            $parameters['idGuardian']= $idGuardian; 

            $this->connection= Connection::GetInstance(); 

            $this->connection->ExecuteNonQuery($query, $parameters);
        } catch(Exception $ex){
            throw $ex;
        }
    }
}
