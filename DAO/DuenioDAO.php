<?php

namespace DAO;

use \Exception as Exception;
use DAO\IDuenioDAO as IDuenioDAO;
use Models\Duenio as Duenio;
use DAO\Connection as Connection;

class DuenioDAO implements IDuenioDAO
{
    private $connection;
    private $tableName = "duenios";

    public function Add(Duenio $duenio)
    {
        try {
            //se crea la llamada a la base de datos para insertar en la tabla duenios los datos del duenio
            $query = "INSERT INTO duenios (nombre, apellido, telefono,email, password, tipo, rutaFoto, alta) VALUES (:nombre, :apellido, :telefono, :email, aes_encrypt(:password, :encryptpass), :tipo, :rutaFoto, :alta);";

            $parameters["encryptpass"] = ENCRYPTPASS;
            $parameters["nombre"] = $duenio->getNombre();
            $parameters["apellido"] = $duenio->getApellido();
            $parameters["telefono"] = $duenio->getTelefono();
            $parameters["email"] = $duenio->getEmail();
            $parameters["password"] = $duenio->getPassword();
            $parameters["tipo"] = $duenio->getTipo();
            $parameters["rutaFoto"] = $duenio->getRutaFoto();
            $parameters["alta"] = $duenio->getAlta();

            $this->connection = Connection::GetInstance();

            // se llama a la funcion executenonquery y se le pasa la llamada preparada y los parametros
            $this->connection->ExecuteNonQuery($query, $parameters);
            
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function GetAll()
    {
        try {
            $duenioList = array();
            //se crea la query que selecciona todos los datos de la tabla duenios
            $query = "SELECT * FROM duenios";

            $this->connection = Connection::GetInstance();
            //se ejecuta esa query y lo que devuelve se guarda en una lista 
            $resultSet = $this->connection->Execute($query);
            //se recorre la lista
            foreach ($resultSet as $row) {

                $duenio = new Duenio(NULL, NULL, NULL, NULL, NULL);

                $duenio->setId($row["idDuenio"]);
                $duenio->setNombre($row["nombre"]);
                $duenio->setApellido($row["apellido"]);
                $duenio->setTelefono($row["telefono"]);
                $duenio->setEmail($row["email"]);
                $duenio->setTipo($row["tipo"]);
                $duenio->setRutaFoto($row["rutaFoto"]);
                $duenio->setAlta($row["alta"]);
                //y se va guardando en una lista de duenios 
                array_push($duenioList, $duenio);
            }

            return $duenioList;
        } catch (Exception $ex) {
            throw $ex;
        }
    }


    public function GetDuenioByEmail($email)
    {
        try {
            $duenio = NULL;
            //se crea la query pidiendo contrasenia y mail de la tabla duenios donde el mail coincida con el mail mandado por param
            $query = "SELECT *, aes_decrypt(password, :encryptpass) as password FROM duenios WHERE (email = :email)";

            $parameters["email"] = $email;
            $parameters["encryptpass"] = ENCRYPTPASS;

            $this->connection = Connection::GetInstance();
            //se guarda lo obtenido al ejecutar la query 
            $resultSet = $this->connection->Execute($query, $parameters);
            //si no es nulo
            if (isset($resultSet)) {
                //se recorre la lista 
                foreach ($resultSet as $row) {
                    //se crea una variable duenio
                    $duenio = new Duenio(NULL, NULL, NULL, NULL, NULL);
                    //se guardan en la variable creada los datos obtenidos al ejecutar la query 
                    $duenio->setId($row["idDuenio"]);
                    $duenio->setNombre($row["nombre"]);
                    $duenio->setApellido($row["apellido"]);
                    $duenio->setTelefono($row["telefono"]);
                    $duenio->setEmail($row["email"]);
                    $duenio->setPassword($row["password"]); //Se usa para el login
                    $duenio->setTipo($row["tipo"]);
                    $duenio->setRutaFoto($row["rutaFoto"]);
                    $duenio->setAlta($row["alta"]);
                }

                return $duenio;
            } else {

                return null;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function GetDuenioById($idDuenio)
    {
        try {
            $duenio = NULL;

            $query = "SELECT * FROM duenios WHERE (idDuenio = :idDuenio)";

            $parameters["idDuenio"] = $idDuenio;

            $this->connection = Connection::GetInstance();

            $resultSet = $this->connection->Execute($query, $parameters);

            if (isset($resultSet)) {

                foreach ($resultSet as $row) {

                    $duenio = new Duenio(NULL, NULL, NULL, NULL, NULL);

                    $duenio->setId($row["idDuenio"]);
                    $duenio->setNombre($row["nombre"]);
                    $duenio->setApellido($row["apellido"]);
                    $duenio->setTelefono($row["telefono"]);
                    $duenio->setEmail($row["email"]);
                    $duenio->setTipo($row["tipo"]);
                    $duenio->setRutaFoto($row["rutaFoto"]);
                    $duenio->setAlta($row["alta"]);
                }

                return $duenio;
            } else {

                return null;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function SetPasswordDuenios($idDuenio, $newPassword){
        try{
            $query= "UPDATE duenios SET password= aes_encrypt(:password, :encryptpass) WHERE idDuenio= :idDuenio;";

            $parameters['encryptpass']= ENCRYPTPASS;
            $parameters['password']= $newPassword; 
            $parameters['idDuenio']= $idDuenio; 

            $this->connection= Connection::GetInstance(); 

            $this->connection->ExecuteNonQuery($query, $parameters);
        } catch(Exception $ex){
            throw $ex;
        }
    }
}
