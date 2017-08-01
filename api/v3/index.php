<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once '../../vendor/autoload.php';
require_once 'src/include/config.php';
require_once 'src/include/functions.inc.php';

// Accèder à toutes les données de configuration par l'intermédiaire de l'instance slim $app
// les constantes sont également dans $config (messages d'erreur, roles...)
$app = new \Slim\App(["settings" => $config]);


// Perment de charger les classes se trouvant définies dans le répertoires classes
// Evite de faire pour chaque classe utilisée un require_once...
//require ("./classes/AuthenticateApiKey.php");

spl_autoload_register(function ($classname) {
    require ("./src/classes/" . $classname . ".php");
});

// ID utilisateur - variable globale
//global $user_id;
$user_id = NULL;

// toutes les routes accessibles à tout utilisateur de l'API
require_once('src/routes/public.php');

//Groupement des routes pour les administrateurs
$app->group('/admin', function () use ($app) {
//    require_once('src/routes/arbitre.php');
//    require_once('src/routes/responsable.php');
    require_once('src/routes/admin.php');
})->add(new AuthenticateApiKey($config['role']['ADMIN'], $config['role']['ADMIN']));

//Groupement des routes pour les responsables de tournois
$app->group('/resp', function () use ($app) {
//    require_once('src/routes/arbitre.php');
    require_once('src/routes/responsable.php');
})->add(new AuthenticateApiKey($config['role']['RESPONSABLE'], $config['role']['ADMIN']));

//Groupement des routes pour les arbitres
$app->group('/arbitre', function () use ($app) {
    require_once('src/routes/arbitre.php');
})->add(new AuthenticateApiKey($config['role']['ARBITRE'], $config['role']['ADMIN']));

$app->run();
?>