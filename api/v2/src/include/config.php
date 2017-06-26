 <?php
/**
 *  configuration de slim
 */
$config['displayErrorDetails'] = true; // mode debug activé
$config['addContentLengthHeader'] = false;


/**
 *  configuration de la base des donnée
 */
/*
//define('DB_USERNAME', 'root_romand');
//define('DB_PASSWORD', 'romand_2017');
//define('DB_HOST', '80.74.155.203');
//define('DB_NAME', 'tournoi_romandvolley');

$config['db']['host']   = "80.74.155.203";
$config['db']['user']   = "admin_tournoi";
$config['db']['pass']   = "iYm4&m38";
$config['db']['dbname'] = "tournoi_romandvolley";
*/
$config['db']['host']   = "localhost";
$config['db']['user']   = "root";
$config['db']['pass']   = "";
$config['db']['dbname'] = "tournoi_romandvolley";


/* utlisateur courant */
$config['curent_user']['id_user'] = NULL;
$config['curent_user']['nom_user'] = NULL;

$config['curent_user']['prenom_user'] = NULL;
$config['curent_user']['email'] = NULL;
$config['curent_user']['id_role'] = NULL;
$config['curent_user']['token'] = NULL;

/**
 *  configuration des constantes
 */
$config['message']['USER_CREATED_SUCCESSFULLY'] = 0;
$config['message']['USER_CREATE_FAILED'] = 1;
$config['message']['USER_ALREADY_EXISTED'] = 2;
$config['message']['ROLE_CREATED_SUCCESSFULLY'] = 0;
$config['message']['ROLE_CREATE_FAILED'] = 1;

$config['role']['ADMIN'] = 1;
$config['role']['RESPONSABLE'] = 2;
$config['role']['ARBITRE'] = 4;
$config['role']['DEMO'] = 8;