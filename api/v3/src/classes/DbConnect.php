<?php

/**
 * Gérer la connexion à la base
 *
 */
 

class DbConnect {

    //private $conn;
    private $pdo = NULL;

    function __construct() {        
    }

    /**
     * établissement de la connexion
     * @return gestionnaire de connexion de base de données
     */
    function connect() {
       try{
            include 'src/include/config.php';

            $db = $config['db'];

            $options  = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
            $this->pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'], $db['user'], $db['pass'],$options);
            return $this->pdo;
        }
        catch(\Exception $ex){
            $this->pdo=NULL;
             return $ex->getMessage();
        }
    }

}

?>