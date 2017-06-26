<?php
    class db{
        // Properties
        private $dbhost = '80.74.155.203';
        private $dbuser = 'root_romand';
        private $dbpass = 'romand_2017';
        private $dbname = 'romandvolley_bd';

        // Connect
        public function connect(){
            $mysql_connect_str = "mysql:host=$this->dbhost;dbname=$this->dbname";
            $dbConnection = new PDO($mysql_connect_str, $this->dbuser, $this->dbpass);
            $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
           // $dbConnection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $dbConnection;
        }
    }
    