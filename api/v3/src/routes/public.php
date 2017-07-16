<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require 'src/include/config.php';

/************************************************************************************
API public
Routes par défauts : vx/public/route
*************************************************************************************/

/**
 * Login Utilisateur
 * url - /user/login
 * method - POST
 * params - email, password
 */
$app->post('/public/user/login', function(Request $request, Response $response) use ($app){
                        //function() use ($app) {
            require 'src/include/config.php';

            // lecture des params de post
            $email = $request->getParam('email');
            // valider adresse email
            $res = validateEmail($email, $response);
            if($res !== true){
                return $res;
            }

            $password = $request->getParam('password');
            //$password = $allPostPutVars['password'];
            $data = array();

            $db = new DbHandler();
            // vérifier l'Email et le mot de passe sont corrects
            if ($db->checkLogin($email, $password)) {
                // obtenir l'utilisateur par email
                $user = $db->getUserByEmail($email);

                if ($user != NULL) {
                    if($user['status']==1){
                        $data['error'] = false;
                        $data['id_user'] = $user['id_user'];
                        $data['status'] = $user['status'];
                        $data['name'] = $user['nom_user'];
                        $data['prenom_user'] = $user['prenom_user'];
                        $data['email'] = $user['email'];
                        $data['apiKey'] = $user['token'];
                        $data['valid_until'] = $user['token_expire'];
                    }
                    else {
                        $data['error'] = true;
                        $data['message'] = "Votre compte a été suspendu";
                    }
                } else {
                    // erreur inconnue est survenue
                    $data['error'] = true;
                    $data['message'] = "Une erreur est survenue. S'il vous plaît essayer à nouveau";
                }
            } else {
                // identificateurs de l'utilisateur sont erronés
                $data['error'] = true;
                $data['message'] = 'Échec de la connexion. identificateurs incorrectes';
            }

            return echoRespnse(200, $response, $data);
        });

/* Liste des matchs d'un groupe
 * url - /public/matchs/groupe/{id_groupe}
 * methode - GET
 */
$app->get('/public/matchs/groupe/{id_groupe}', function (Request $request, Response $response) {
            $id_groupe = $request->getAttribute('id_groupe');

            $db = new DbHandler();
            $res = array();
            $res = $db->getMatchsByGroup($id_groupe);

            // echo de la réponse  JSON
            return echoRespnse(200, $response, $res);
        });

/* Liste des matchs d'une équipe
 * url - /public/matchs/equipe/{id_equipe}
 * methode - GET
 */
$app->get('/public/matchs/equipe/{id_equipe}', function (Request $request, Response $response) {
            $id_equipe = $request->getAttribute('id_equipe');

            $db = new DbHandler();
            $res = array();
            $res = $db->getTeamMatchsById($id_equipe);
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $res);
        });

/* Liste des matchs pour un tournoi
 * url - /public/tournament/{id_tournoi}/matchs
 * methode - GET
 */
$app->get('/public/tournament/{id_tournoi}/matchs', function (Request $request, Response $response) {
            $id_tournoi = $request->getAttribute('id_tournoi');

            $db = new DbHandler();
            $res = array();
            $res = $db->getMatchsByTournamentId($id_tournoi);

            // echo de la réponse  JSON
            return echoRespnse(200, $response, $res);
        });

/* Liste les noms des équipes pour un tournoi
 * url - /public/tournament/{id_tournoi}/equipes
 * methode - GET
 */
$app->get('/public/tournament/{id_tournoi}/equipes', function (Request $request, Response $response) {
            $id_tournoi = $request->getAttribute('id_tournoi');

            $db = new DbHandler();
            $res = array();
            $res = $db->getTeamsTournamentById($id_tournoi);

            // echo de la réponse  JSON
            return echoRespnse(200, $response, $res);
        });
/* Liste des matchs listés par terrain pour un tounoi spécifique 
 * url - /public/tournament/{id_tournoi}/matchs/terrains
 * methode - GET
 */
$app->get('/public/tournament/{id_tournoi}/matchs/terrains', function (Request $request, Response $response) {
            $id_tournoi = $request->getAttribute('id_tournoi');

            $db = new DbHandler();
            $res = array();
            $res = $db->getMatchsPitchesByTournamentId($id_tournoi);

            // echo de la réponse  JSON
            return echoRespnse(200, $response, $res);
        });

/* Liste des matchs pour un terrain spécifique 
 * url - /public/matchs/terrain/{id_terrain}
 * methode - GET
 */
$app->get('/public/matchs/terrain/{id_terrain}', function (Request $request, Response $response) {
            $id_terrain = $request->getAttribute('id_terrain');

            $db = new DbHandler();
            $res = array();
            $res = $db->getMatchsByPitchId($id_terrain);

            // echo de la réponse  JSON
            return echoRespnse(200, $response, $res);
        });


/* Obtient le classement d'un groupe par l'id du groupe
 * url - /public/classement/groupe/{id_groupe}
 * methode - GET
 */
$app->get('/public/classement/groupe/{id_groupe}', function (Request $request, Response $response) {
            $id_groupe = $request->getAttribute('id_groupe');

            $db = new DbHandler();
            $res = array();
            $res = $db->getRankingByGroupID($id_groupe);

            // echo de la réponse  JSON
            return echoRespnse(201, $response, $res);
        });   


/* Obtient le détail des équipes d'un groupe par l'id du groupe
 * url - /public/equipes/groupe/{id_groupe}
 * methode - GET
 */
$app->get('/public/equipes/groupe/{id_groupe}', function (Request $request, Response $response) {
            $id_groupe = $request->getAttribute('id_groupe');

            $db = new DbHandler();
            $res = array();
            $res = $db->getTeamDetailsgByGroupID($id_groupe);

            // echo de la réponse  JSON
            return echoRespnse(200, $response, $res);
        });  



/************************************************************************************
API admin
Routes par défauts : vx/admin/route
*************************************************************************************/

/* Enregistrement  de l'utilisateur (création d'un nouveau')
 * url - /admin/register
 * methode - POST
 * headears - content id_user and API_KEY
 * params - email, password, role
 */
$app->post('/admin/register', function(Request $request, Response $response) {
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
$app->get('/admin/users', function (Request $request, Response $response) {
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
$app->get('/admin/tournaments', function (Request $request, Response $response) {
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
$app->get('/admin/tournament/{id}', function (Request $request, Response $response) {
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
$app->get('/admin/tournaments/email/{email}', function (Request $request, Response $response) {
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
$app->get('/admin/tournaments/id/{id_user}', function (Request $request, Response $response) {
            $id = $request->getAttribute('id_user');

            $db = new DbHandler();
            $res = $db->getTournamentCreatedUserById($id);

            // echo de la réponse  JSON
            return echoRespnse(200, $response, $res);

        });


/************************************************************************************
API responsables de tournoi
Routes par défauts : vx/responsable/route
*************************************************************************************/

        /* Liste des tournois créés par l'utilisateur en cours, selon son id dans son entête
        * url - /resp/tournaments
        * headears - content id_user and API_KEY
        * methode - GET
        */
        $app->get('/resp/tournaments', function (Request $request, Response $response)  {
            // Obtenir les en-têtes de requêtes
            // Nullement besoin de test la présence, car cela est fait précédement
            // en vérifiant l'authentifcation sur la route du group responsable
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            $db = new DbHandler();
            $res = array();
            $res = $db->getTournamentCreatedUserById($id_current_user);

            // echo de la réponse  JSON
            return echoRespnse(200, $response, $res);
        });


        /* Liste des équipes pour un tournoi appartenant à l'utilisateur en cours, selon son id dans son entête
        * url - /resp/tournament/{id_tournoi}/equipes
        * headears - content id_user and API_KEY
        * methode - GET
        */
        $app->get('/resp/tournament/{id_tournoi}/equipes', function (Request $request, Response $response) {
            // Obtenir les en-têtes de requêtes
            // Nullement besoin de test la présence, car cela est fait précédement
            // en vérifiant l'authentifcation sur la route du group responsable
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            $id_tournoi = $request->getAttribute('id_tournoi');

            $db = new DbHandler();
            $res = array();
            $res = $db->getTeamsTournamentByIdAndUserId($id_current_user, $id_tournoi);

            // echo de la réponse  JSON
            return echoRespnse(200, $response, $res);
        });

        /* Liste des équipes dans un groupe appartenant 
        * url - /resp/groupe/{id_groupe}/equipes
        * headears - content id_user and API_KEY
        * methode - GET
        */
        $app->get('/resp/groupe/{id_groupe}/equipes', function (Request $request, Response $response) {
            $id_groupe = $request->getAttribute('id_groupe');

            $db = new DbHandler();
            $res = array();
            $res = $db->getTeamsByGroupById( $id_groupe);

            // echo de la réponse  JSON
            return echoRespnse(200, $response, $res);
        });



        /* Liste des équipes dans un groupe appartenant à l'utilisateur en cours et en précisant un id de tournoi, selon son id dans son entête
        * url - /resp/tournament/{id_tournoi}/equipes/groupe/{id_groupe}
        * headears - content id_user and API_KEY
        * methode - GET
        */
        $app->get('/resp/tournament/{id_tournoi}/equipes/groupe/{id_groupe}', function (Request $request, Response $response) {
            // Obtenir les en-têtes de requêtes
            // Nullement besoin de test la présence, car cela est fait précédement
            // en vérifiant l'authentifcation sur la route du group responsable
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            $id_tournoi = $request->getAttribute('id_tournoi');
            $id_groupe = $request->getAttribute('id_groupe');

            $db = new DbHandler();
            $res = array();
            $res = $db->getTeamsByGroupTournamentByIdAndUserId($id_current_user, $id_tournoi, $id_groupe);

            // echo de la réponse  JSON
            return echoRespnse(200, $response, $res);
        });        


        /* Ajout d'un tournoi (création d'un nouveau')
        * url - /resp/tournoi
        * methode - POST
        * headears - content id_user and API_KEY
        * body - Json : {"nom_tournoi":"2017-09-15 SVRN""}
        * return - {
        *            "error": false,
        *            "error_mgs": null,
        *            "nombre_insert": 1,
        *            "id_dernier_insert": 5,
        *            "id_premier_insert": 5
        *           }
        */
        $app->post('/resp/tournoi', function(Request $request, Response $response) use ($app) {
            // récupère les données passée aux forma json
            $json = $request->getBody();
            $data = json_decode($json, true); // transofme en tableau associatif

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            $db = new DbHandler();
            $res = $db->createTournament($data['nom_tournoi'], $id_current_user);

            // echo de la réponse  JSON
            return echoRespnse(201, $response, $res);
        });


        /* Ajout de personnes représentant les équipes
        * url - /resp/personnes
        * methode - POST
        * headears - content id_user and API_KEY
        * body - Json : [
        *	{"prenom":"Nicole","nom":"Schnyder","courriel":"nicole.schnyder@vebb.com","tel":"+41 31 336 54 78","tel_mobile":"+41 78 123 45 67","adresse":"Feuille 12","localite":"Bienne","Pays":"Suisse", "id_equipe":"5" },
        *	{"prenom":"André","nom":"Duprès","courriel":"a.dup@gmail.com","tel":"+41 22 123 45 678","tel_mobile":"+41 77 777 77 67","adresse":"Treffle 1a","localite":"Cointrin","Pays":"Suisse", "id_equipe":"6" }
        * ]
        * return - {
        *            "error": false,
        *            "error_mgs": null,
        *            "nombre_insert": 2,
        *            "id_dernier_insert": 55,
        *            "id_premier_insert": 53
        *           }
        */
        $app->post('/resp/personnes', function(Request $request, Response $response) use ($app) {
            // récupère les données passée aux forma json
            $json = $request->getBody();
            $data = json_decode($json, true); // transofme en tableau associatif
            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            $db = new DbHandler();
            // permission pour ajouter dans des personnes avec les équipes des groupes lui appartenant
            foreach($data as $personne){
                if( !$db->isTeamOwner($id_current_user, $personne['id_equipe']) ){
                        $resultat['error'] = TRUE;
                        $resultat['error_mgs'] = "Permission refusée pour votre identitifant. Au moins une équipe mentionnées ne correspond pas au numéro de groupe !";
                        return echoRespnse(201, $response, $resultat);
                }
            }

            //$res = $db->createPersons($data);
            $res = $db->createMultiple('personnes', $data);
            // echo de la réponse  JSON
            return echoRespnse(201, $response, $res);
        });


        /* Ajout d'équipes
        * url - /resp/equipes
        * methode - POST
        * headears - content id_user and API_KEY
        * body - Json : [
        *	{"niveau":"M15","nom_equipe":"VDT","id_groupe":"2" },
        *	{"niveau":"FM17","nom_equipe":"Savagnier","id_groupe":"2" }
        *  ]
        * return - {
        *            "error": false,
        *            "error_mgs": null,
        *            "nombre_insert": 2,
        *            "id_dernier_insert": 55,
        *            "id_premier_insert": 53
        *           }
        */
        $app->post('/resp/equipes', function(Request $request, Response $response) use ($app) {
            // récupère les données passée aux forma json
            $json = $request->getBody();
            $data = json_decode($json, true); // transofme en tableau associatif

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            $db = new DbHandler();

            // permission pour ajouter dans les groupes lui appartenant
            foreach($data as $equipe){
                $res = $db->isGroupOwner($id_current_user, $equipe['id_groupe']);
                if(!$res){
                    $resultat['error'] = TRUE;
                    $resultat['error_mgs'] = "Permission refusée pour votre identitifant pour au moins une équipe mentionnées dans le mauvais numéro de groupe !";
                    return echoRespnse(201, $response, $resultat);
                }
            }
            
            // insertion des enregistrements
            $res = $db->createMultiple('equipes', $data);
            // echo de la réponse  JSON
            return echoRespnse(201, $response, $res);
        });


        /* Ajout de groupes 
        * url - /resp/groupes
        * methode - POST
        * headears - content id_user and API_KEY
        * body - Json : [
        *	{"nom_groupe":"GrA","id_tournoi":"1"},
        *	{"nom_groupe":"GrB","id_tournoi":"1"}
        * ]
        * return - {
        *            "error": false,
        *            "error_mgs": null,
        *            "nombre_insert": 2,
        *            "id_dernier_insert": 55,
        *            "id_premier_insert": 53
        *           }
        */
        $app->post('/resp/groupes', function(Request $request, Response $response) use ($app) {
            // récupère les données passée aux forma json
            $json = $request->getBody();
            $data = json_decode($json, true); // transofme en tableau associatif

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            $db = new DbHandler();
            // permission pour ajouter dans les groupes lui appartenant
            foreach($data as $groupe){
                $res = $db->isTournamentOwner($id_current_user, $groupe['id_tournoi']);
                if(!$res){
                    $resultat['error'] = TRUE;
                    $resultat['error_mgs'] = "Permission refusée pour votre identifiant dans au moins un groupe mentionnés dans le mauvais numéro de tournoi !";
                    return echoRespnse(201, $response, $resultat);
                }
            }

            // insertion des enregistrements
            $res = $db->createMultiple('groupes', $data);
            // echo de la réponse  JSON
            return echoRespnse(201, $response, $res);
        });

        /* Ajout de terrains 
        * url - /resp/terrains
        * methode - POST
        * headears - content id_user and API_KEY
        * body - Json : [
        *        {"nom_terrain":"Terrain OUEST" },
        *        {"nom_terrain":"Terrain EST" }
        *        {"nom_terrain":"Terrain PRINCIPAL" }
        *    ]
        * return - {
        *            "error": false,
        *            "error_mgs": null,
        *            "nombre_insert": 3,
        *            "id_dernier_insert": 55,
        *            "id_premier_insert": 53
        *           }
        */ 

        $app->post('/resp/terrains', function(Request $request, Response $response) use ($app) {
                    // récupère les données passée aux forma json
                    $json = $request->getBody();
                    $data = json_decode($json, true); // transofme en tableau associatif

                    $db = new DbHandler();
                    // insertion des enregistrements
                    $res = $db->createMultiple('terrains', $data);
                    // echo de la réponse  JSON
                    return echoRespnse(201, $response, $res);
                });

        /* Ajout de matchs
        * url - /resp/matchs
        * methode - POST
        * headears - content id_user and API_KEY
        * body - Json : [
        *	                {"date_match":"2017-09-28","heure":"18:00:00","id_terrain":"3","id_equipe_home":"1","id_equipe_visiteur":"2" },
        *	                {"date_match":"2017-09-28","heure":"20:00:00","id_terrain":"3","id_equipe_home":"2","id_equipe_visiteur":"3" },
        *	                {"date_match":"2017-09-28","heure":"21:00:00","id_terrain":"3","id_equipe_home":"3","id_equipe_visiteur":"2" }
        *               ]
        *
        * Il égaglement possible de spécifier les champs (ou mixer les possibilités) : id_user_dirige, id_equipe_arbitre
        * [
        *	{"date_match":"2017-09-28","heure":"18:00:00","id_user_dirige":"12","id_terrain":"3","id_equipe_home":"1","id_equipe_visiteur":"2","id_equipe_arbitre":"2" },
        *	{"date_match":"2017-09-28","heure":"20:00:00","id_user_dirige":"15","id_terrain":"3","id_equipe_home":"3","id_equipe_visiteur":"2","id_equipe_arbitre":"4" },
        *   {"date_match":"2017-09-28","heure":"21:00:00","id_user_dirige":"10","id_terrain":"3","id_equipe_home":"1","id_equipe_visiteur":"3","id_equipe_arbitre":"3" }
        *  ]
        * return - {
        *            "error": false,
        *            "error_mgs": null,
        *            "nombre_insert": 3,
        *            "id_dernier_insert": 55,
        *            "id_premier_insert": 53
        *           }
        */
        $app->post('/resp/matchs', function(Request $request, Response $response) use ($app) {
            // récupère les données passée aux forma json
            $json = $request->getBody();
            $data = json_decode($json, true); // transofme en tableau associatif

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            $db = new DbHandler();

            // permission pour ajouter dans des matchs des groupes lui appartenant
            foreach($data as $match){
                if(    !$db->isTeamOwner($id_current_user, $match['id_equipe_home']) 
                    || !$db->isTeamOwner($id_current_user, $match['id_equipe_visiteur'])
                    || (isset($match['id_equipe_arbitre']) && !$db->isTeamOwner($id_current_user, $match['id_equipe_arbitre']))){
                        $resultat['error'] = TRUE;
                        $resultat['error_mgs'] = "Permission refusée pour votre identitifant. Au moins une équipe mentionnées ne correspond pas au numéro de groupe !";
                        return echoRespnse(201, $response, $resultat);
                }
            }
            
            // insertion des enregistrements
            $res = $db->createMultiple('matchs', $data);
            // echo de la réponse  JSON
            return echoRespnse(201, $response, $res);
        });

        /* Ajout de sets
        * url - /resp/sets
        * methode - POST
        * headears - content id_user and API_KEY
        * body - Json : [
        *	                {"date_match":"2017-09-28","heure":"18:00:00","id_terrain":"3","id_equipe_home":"1","id_equipe_visiteur":"2" },
        *	                {"date_match":"2017-09-28","heure":"20:00:00","id_terrain":"3","id_equipe_home":"2","id_equipe_visiteur":"3" },
        *	                {"date_match":"2017-09-28","heure":"21:00:00","id_terrain":"3","id_equipe_home":"3","id_equipe_visiteur":"2" }
        *               ]
        *
        * Il égaglement possible de spécifier les champs (ou mixer les possibilités) : id_user_dirige, id_equipe_arbitre
        * [
        *	{"date_match":"2017-09-28","heure":"18:00:00","id_user_dirige":"12","id_terrain":"3","id_equipe_home":"1","id_equipe_visiteur":"2","id_equipe_arbitre":"2" },
        *	{"date_match":"2017-09-28","heure":"20:00:00","id_user_dirige":"15","id_terrain":"3","id_equipe_home":"3","id_equipe_visiteur":"2","id_equipe_arbitre":"4" },
        *   {"date_match":"2017-09-28","heure":"21:00:00","id_user_dirige":"10","id_terrain":"3","id_equipe_home":"1","id_equipe_visiteur":"3","id_equipe_arbitre":"3" }
        *  ]
        * return - {
        *            "error": false,
        *            "error_mgs": null,
        *            "nombre_insert": 3,
        *            "id_dernier_insert": 55,
        *            "id_premier_insert": 53
        *           }
        */
        $app->post('/resp/sets', function(Request $request, Response $response) use ($app) {
            // récupère les données passée aux forma json
            $json = $request->getBody();
            $data = json_decode($json, true); // transofme en tableau associatif

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            $db = new DbHandler();

            // permission pour ajouter dans des matchs des groupes lui appartenant
            foreach($data as $match){
                if( !$db->isMatchOwner($id_current_user, $match['id_match']) ){
                        $resultat['error'] = TRUE;
                        $resultat['error_mgs'] = "Permission refusée pour votre identitifant. Au moins un sets pour un match mentionnés ne correspond pas au numéro de groupe vous appartenant !";
                        return echoRespnse(201, $response, $resultat);
                }
            }
            
            // insertion des enregistrements
            $res = $db->createMultiple('sets', $data);
            // echo de la réponse  JSON
            return echoRespnse(201, $response, $res);
        });



       /* Suppression d'un tournoi 
        * url - /resp/tournoi
        * methode - DELETE
        * headears - content id_user and API_KEY
        * body - Json : -
        * return - {
        *            "error": false,
        *            "error_mgs": null,
        *            "nombre_suppression": 1,
        *            "id_supprimer": "
        *           }
        */
        $app->delete('/resp/tournoi/{id}', function(Request $request, Response $response) use ($app) {
            $resultat['error'] = FALSE;
            $resultat['error_mgs'] = "";

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];
            $id = $request->getAttribute('id');

            $db = new DbHandler();
            $res = $db->isTournamentOwner($id_current_user, $id); // Vérifie que l'utilisateur courant est le propriétaire
            if(!$res){
                $resultat['error'] = TRUE;
                $resultat['error_mgs'] = "Permission refusée pour votre identifiant ou id non trouvé !";

                return echoRespnse(201, $response, $resultat);
            }
            
            // supprime les terrains du tournoi manuellement
            // Pas de cascade, car on veut les consrver en cas de suppression de matchs !
            if(!$db->deletePitchByTournamentID($id)){
                $resultat['error'] = TRUE;
                $resultat['error_mgs'] = "Problème de suppression des terrains !";
                return echoRespnse(201, $response, $resultat);
            }
            
            // suppression du tournoi en cascade avec les enfants du tournoi
            $res = $db->deleteByID('tournois', $id);
            
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $res);
        });


       /* Suppression d'une équipe 
        * url - /resp/equipe/{id}
        * methode - DELETE
        * headears - content id_user and API_KEY
        * body - Json : -
        * return - {
        *            "error": false,
        *            "error_mgs": null,
        *            "nombre_suppression": 1,
        *            "id_supprimer": "17"
        *           }
        */
        $app->delete('/resp/equipe/{id}', function(Request $request, Response $response) use ($app) {
            $resultat['error'] = FALSE;
            $resultat['error_mgs'] = "";

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];
            $id = $request->getAttribute('id');
            
            $db = new DbHandler();
            $res = $db->isTeamOwner($id_current_user, $id); // Vérifie que l'utilisateur courant est le propriétaire
            if(!$res){
                $resultat['error'] = TRUE;
                $resultat['error_mgs'] = "Permission refusée pour votre identifiant ou id non trouvé !";

                return echoRespnse(201, $response, $resultat);
            }
                       
            // suppression de l'équipe en cascade avec ses enfants
            $res = $db->deleteByID('equipes', $id);
            
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $res);
        });


       /* Suppression d'un groupe 
        * url - /resp/groupe/{id}
        * methode - DELETE
        * headears - content id_user and API_KEY
        * body - Json : -
        * return - {
        *            "error": false,
        *            "error_mgs": null,
        *            "nombre_suppression": 1,
        *            "id_supprimer": "17"
        *           }
        */
        $app->delete('/resp/groupe/{id}', function(Request $request, Response $response) use ($app) {
            $resultat['error'] = FALSE;
            $resultat['error_mgs'] = "";

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];
            $id = $request->getAttribute('id');

            $db = new DbHandler();
            $res = $db->isGroupOwner($id_current_user, $id); // Vérifie que l'utilisateur courant est le propriétaire
            if(!$res){
                $resultat['error'] = TRUE;
                $resultat['error_mgs'] = "Permission refusée pour votre identifiant ou id non trouvé !";

                return echoRespnse(201, $response, $resultat);
            }
                       
            // suppression de l'équipe en cascade avec ses enfants
            $res = $db->deleteByID('groupes', $id);
            
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $res);
        });


/*
$app->get('/hello', function ($request, $response) use ($app) {

        // Obtenir les en-têtes de requêtes
        $headers = $request->getHeaders();
        
        $data = array();


        $db = new DbHandler();

        //Obtenir l'id
        $id = $headers['HTTP_USERID'][0];
        $data['auth'] = 'Hello';
        $data['user_id_global'] = $id;
            return echoRespnse(200, $response, $data);

        $container = $app->getContainer();
        $settings = $container->get('settings');
        $data['user_id_global'] = $settings['curent_user_id'];
            return echoRespnse(200, $response, $data);
    });
*/
/************************************************************************************
API arbitre
Routes par défauts : vx/arbitre/route
*************************************************************************************/








/************************************************************************************
API en test
Routes par défauts : vx/public/route
*************************************************************************************/

/**
 * GET API Key
 * url - /public/userkey/
 * method - GET
 * params - email, password
 */
 /*
$app->get('/public/userkey/{email}/{pass}', function (Request $request, Response $response) {
   
    // Grab User submitted information
    $email = $request->getAttribute('email');
    $pass = $request->getAttribute('pass');
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $pass = sha1(filter_var($pass, FILTER_SANITIZE_STRING));

    $sql = 'SELECT email, id_role, token, token_expire FROM users WHERE email = "'.$email.'" AND mot_de_passe = "' .$pass .'"';

    try{
        // Get DB
        $db = new DbConnect();
        $pdo = $db->connect();

        $stmt = $pdo->query($sql);
        $user = $stmt->fetch(PDO::FETCH_OBJ);
        if($user){
            $token = bin2hex(openssl_random_pseudo_bytes(8)); //generate a random token
            $tokenExpiration = date('Y-m-d H:i:s', strtotime('+1 hour'));//the expiration date will be in one hour from the current moment

            //update User's token
            $sql = "UPDATE users SET
                token = :token,
                token_expire = :token_expire
                WHERE email= :email";

                $stmt = $pdo->prepare($sql);

                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':token', $token);
                $stmt->bindParam(':token_expire', $tokenExpiration);

                $stmt->execute();    
                $db = null;        

                // update token in user object
                $user->token = $token;
                $user->token_expire = $tokenExpiration;
                echo json_encode($user);
        }
        else{
            echo '{"error":{"texte": false}';
        }

    } catch(PDOException $e){
        echo '{"error":{"texte": '.$e->getMessage().'}';
    }
});
*/
