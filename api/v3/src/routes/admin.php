<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require 'src/include/config.php';

/************************************************************************************
API admin
Routes par défauts : vx/admin/route
*************************************************************************************/
    
/**
 *Création d'une nouveau role dans db
 * method POST
 * params - name
 * url - /role/{id}/{droits}
 */
$app->post('/role/{id}/{droits}', function(Request $request, Response $response) {

            $data = array();
            //$task = $app->request->post('task');

            // lecture des params de post
            $id = $request->getAttribute('id');
            $droits = $request->getAttribute('droits');

            $db = new DbHandler();

            //Création d'un nouveau role
            $role_id = $db->createRole($id, $droits);

            if ($role_id != NULL) {
                $data["error"] = false;
                $data["message"] = "Rôle créé avec succès";
                $data["role_id"] = $role_id;
                return echoRespnse(201, $response, $data);
                //echoRespnse(201, $response);
            } else {
                $data["error"] = true;
                $data["message"] = "Impossible de créer le rôle. S'il vous plaît essayer à nouveau";
                return echoRespnse(200, $response, $data);
                //echoRespnse(200, $response);
            }
        });


/**
 * Récupèreation rôles dans db
 * method GET
 * params - name
 * url - /admin/roles
 */
 
$app->get('/roles', function(Request $request, Response $response) {

            $data = array();

            $db = new DbHandler();

            //Récupération des roles
            $roles = $db->getRoles();

            if ($roles != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["roles"] = $roles;

                return echoRespnse(200, $response, $data);
                //echoRespnse(201, $response);
            } else {
                $data["error"] = true;
                $data["message"] = "Impossible de réucupérer les rôles. S'il vous plaît essayer à nouveau";
                return echoRespnse(200, $response, $data);
                //echoRespnse(200, $response);
            }
        });


/**
 * Récupèreation d'un rôle dans db
 * method GET
 * params - name
 * url - /admin/role/{id}
 */
 
$app->get('/role/{id}', function(Request $request, Response $response) {

            $data = array();

            // lecture des params de post
            $id = $request->getAttribute('id');

            $db = new DbHandler();

            //Récupération d'un role
            $role_id = $db->getRoleById($id);

            if ($role_id != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["role"] = $role_id;
                return echoRespnse(200, $response, $data);
                //echoRespnse(201, $response);
            } else {
                $data["error"] = true;
                $data["message"] = "Impossible de réucupérer le rôle. S'il vous plaît essayer à nouveau";
                return echoRespnse(200, $response, $data);
                //echoRespnse(200, $response);
            }
        });

/* Enregistrement  de l'utilisateur (création d'un nouveau')
 * url - /admin/register
 * methode - POST
 * headears - content id_user and API_KEY
 * params - email, password, role
 */
$app->post('/register', function(Request $request, Response $response) {
            require 'src/include/config.php';
            // lecture des params de post
            $email = $request->getParam('email');
            $password = $request->getParam('password');
            $id_role = $request->getParam('role');
            $nom = $request->getParam('nom');
            $prenom = $request->getParam('prenom');
            
            // valider adresse email
            $res = validateEmail($email, $response);
            if($res !== true) {
                return $res;
            }

            $db = new DbHandler();
            $res = $db->createUser($email, $password, $id_role, $nom, $prenom);
            
            $data = array();
            if ($res == $config['message']['USER_CREATED_SUCCESSFULLY']) {
                $data["error"] = false;
                $data["message"] = "Nouvel utilisateur inscrit avec succès";
            } else if ($res == $config['message']['USER_CREATE_FAILED']) {
                $data["error"] = true;
                $data["message"] = "Oops! Une erreur est survenue lors de l'inscription";
            } else if ($res == $config['message']['USER_ALREADY_EXISTED']) {
                $data["error"] = true;
                $data["message"] = "Désolé, cet E-mail éxiste déja";
            }
            // echo de la réponse  JSON
            return echoRespnse(201, $response, $data);

        });


// Get All users
/* Liste des utilisateurs
 * url - /admin/users
 * headears - content id_user and API_KEY
 * methode - GET
 */
$app->get('/users', function (Request $request, Response $response) {
            $db = new DbHandler();
            $users = $db->getUsers();

            // echo de la réponse  JSON
            return echoRespnse(201, $response, $users);

        });

/* Liste des tournois
 * url - /admin/tournaments
 * headears - content id_user and API_KEY
 * methode - GET
 */
$app->get('/tournaments', function (Request $request, Response $response) {
            $db = new DbHandler();
            $users = $db->getTournaments();

            // echo de la réponse  JSON
            return echoRespnse(201, $response, $users);

        });

/* Informations d'un tournoi, selon son id
 * url - /admin/tournament/{id_tournoi}
 * headears - content id_user and API_KEY
 * methode - GET
 */
$app->get('/tournament/{id}', function (Request $request, Response $response) {
            $id_tournoi = $request->getAttribute('id');

            $db = new DbHandler();
            $res = $db->getTournamentById($id_tournoi);

            // echo de la réponse  JSON
            return echoRespnse(200, $response, $res);

        });

/* Liste des tournois créés par un utilateur, selon son email
 * url - /admin/tournament/email/{email}
 * headears - content id_user and API_KEY
 * methode - GET
 */
$app->get('/tournaments/email/{email}', function (Request $request, Response $response) {
            $email = $request->getAttribute('email');

            $db = new DbHandler();
            $res = $db->getTournamentCreatedUserByEmail($email);

            // echo de la réponse  JSON
            return echoRespnse(200, $response, $res);

        });

/* Liste des tournois créés par un utilateur, selon son id
 * url - /admin/tournaments/id/{id_user}
 * headears - content id_user and API_KEY
 * methode - GET
 */
$app->get('/tournaments/id/{id_user}', function (Request $request, Response $response) {
            $id = $request->getAttribute('id_user');

            $db = new DbHandler();
            $res = $db->getTournamentCreatedUserById($id);

            // echo de la réponse  JSON
            return echoRespnse(200, $response, $res);

        });

