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






