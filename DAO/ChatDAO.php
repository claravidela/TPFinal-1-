<?php namespace DAO; 

use Models\Chat as Chat; 
use \Exception as Exception;
use DAO\Connection as Connection;
use DAO\I_ChatDAO as I_ChatDAO; 

class ChatDAO implements I_ChatDAO{

    private $connection; 
    private $tableName= "chats"; 

    public function Add(Chat $chat){

        try{
            $query= "INSERT INTO chats (mensaje, idEmisor, idReceptor) VALUES (:mensaje, :idEmisor, :idReceptor);";

            $parameters['mensaje']= $chat->getMensaje(); 
            $parameters['idEmisor']= $chat->getIdEmisor(); 
            $parameters['idReceptor']= $chat->getIdReceptor();

            $this->connection= Connection::GetInstance();

            $this->connection->ExecuteNonQuery($query, $parameters); 

        }catch(Exception $ex){
            throw $ex;
        }
    }

    public function GetChatByIds($idEmisor, $idReceptor){
        try{
            $chatList = array();
            $query = "SELECT * FROM chats WHERE (idReceptor = :idReceptor AND idEmisor = :idEmisor OR idEmisor = :idReceptor AND idReceptor = :idEmisor) ORDER BY 'idMensaje'";

            $parameters["idReceptor"] = $idReceptor;
            $parameters["idEmisor"] = $idEmisor;

            $this->connection = Connection::GetInstance();
            $results = $this->connection->Execute($query, $parameters);

            foreach ($results as $row) {
                $chat = new Chat();

                $chat->setIdMensaje($row['idMensaje']);
                $chat->setMensaje($row['mensaje']); 
                $chat->setIdEmisor($row['idEmisor']); 
                $chat->setIdReceptor($row['idReceptor']);
                $chat->setEstado($row['estado']);

                array_push($chatList, $chat);
            }

            return $chatList;
        }catch (Exception $ex) {
        throw $ex;
    }
}

    public function GetChatsByUser($idUser){
        try{
            $query= "SELECT * FROM chats WHERE idEmisor= :idUser OR idReceptor= :idUser;";

            $parameters['idUser']= $idUser; 

            $this->connection= Connection::GetInstance();

            $results= $this->connection->Execute($query, $parameters); 

            $todosLosChats= array();
            if(isset($results)){ 
                foreach($results as $row){
                    $chat= new Chat(); 

                    $chat->setIdMensaje($row['idMensaje']);
                    $chat->setMensaje($row['mensaje']); 
                    $chat->setIdEmisor($row['idEmisor']); 
                    $chat->setIdReceptor($row['idReceptor']);
                    $chat->setEstado($row['estado']);

                    array_push($todosLosChats, $chat);
                }

                return $todosLosChats; 
            } else{
                return null;
            }
        }catch(Exception $ex){ 
            throw $ex;
        }
    }

    public function cambiarEstado($idMensaje, $estado){
        try{
            $query= "UPDATE chats SET estado= :estado WHERE idMensaje= :idMensaje;"; 

            $parameters['estado']= $estado; 
            $parameters['idMensaje']= $idMensaje; 

            $this->connection= Connection::GetInstance(); 
            $this->connection->ExecuteNonQuery($query, $parameters); 

        }catch(Exception $ex){
            throw $ex; 
        }
    }

    public function GetMensajesByEstado($idReceptor){
        try{
            $query= "SELECT * FROM chats WHERE idReceptor= :idReceptor AND estado= :estado;";

            $parameters['idReceptor']= $idReceptor; 
            $parameters['estado']= "enviado";

            $this->connection= Connection::GetInstance(); 

            $results= $this->connection->Execute($query, $parameters); 
            $mensNoLeidos= array(); 
            if(isset($results)){
                foreach($results as $row){
                    $mensj= new Chat(); 
                    $mensj->setIdMensaje($row['idMensaje']);
                    $mensj->setMensaje($row['mensaje']); 
                    $mensj->setIdEmisor($row['idEmisor']); 
                    $mensj->setIdReceptor($row['idReceptor']);
                    $mensj->setEstado($row['estado']);

                    array_push($mensNoLeidos, $mensj); 
                }
                return $mensNoLeidos; 
            } else {
                return null; 
            }

        }catch(Exception $ex){
            throw $ex;
        }
    }
}



?> 