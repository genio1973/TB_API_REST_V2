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
$app->post('/public/user/login', function(Request $request, Response $response) {
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
                    // vérifier que le compte est tourjours actif !
                    if($user['status'] == 1){
                        $result['error'] = false;
                        $result['message'] = NULL;
                        /*
                        $data['id_user'] = $user['id_user'];
                        $data['status'] = $user['status'];
                        $data['name'] = $user['nom_user'];
                        $data['prenom_user'] = $user['prenom_user'];
                        $data['email'] = $user['email'];
                        $data['token'] = $user['token'];
                        $data['token_expire'] = $user['token_expire'];
                        $data['id_role'] = $user['id_role'];
                        */
/*
                        // Générer API key et mettre à jour avec nouvelle durée de vie
                        $updata['apiKey'] = $db->generateApiKey();
                        $updata['valid_until'] = date('Y-m-d H:i:s', strtotime('+2 hour'));//the expiration date will be in two hour from the current moment
                        $data['token'] =  $updata['apiKey'];
                        $data['token_expire'] = $updata['valid_until'];
                        $db->updateByID('users', $updata, $user['id_user']);
*/
                        //$result['result'] = $data;
                         $result['result'] = $user;
                    }
                    else {
                        $result['error'] = true;
                        $result['message'] = "Votre compte a été suspendu";
                    }
                } else {
                    // erreur inconnue est survenue
                    $result['error'] = true;
                    $result['message'] = "Une erreur est survenue. S'il vous plaît essayer à nouveau";
                }
            } else {
                // identificateurs de l'utilisateur sont erronés
                $result['error'] = true;
                $result['message'] = 'Échec de la connexion. identificateurs incorrectes';
            }

            return echoRespnse(200, $response, $result);
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
            $data=NULL;
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "Impossible d'accèder aux données. S'il vous plaît essayer à nouveau";
                return echoRespnse(200, $response, $data);
            }     
             
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
        });

/* Liste des résultats d'un groupe
 * url - /public/resultats/groupe/{id_groupe}
 * methode - GET
 */
$app->get('/public/resultats/groupe/{id_groupe}', function (Request $request, Response $response) {
            $id_groupe = $request->getAttribute('id_groupe');

            $db = new DbHandler();
            $res = array();
            $res = $db->getScoresByGroup($id_groupe);
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "Impossible d'accèder aux données. S'il vous plaît essayer à nouveau";
                return echoRespnse(200, $response, $data);
            }     
             
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
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
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "Impossible d'accèder aux données. S'il vous plaît essayer à nouveau";
                return echoRespnse(200, $response, $data);
            }     
             
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
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
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "Impossible d'accèder aux données. S'il vous plaît essayer à nouveau";
                return echoRespnse(200, $response, $data);
            }     
             
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
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
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "Impossible d'accèder aux données. S'il vous plaît essayer à nouveau";
                return echoRespnse(200, $response, $data);
            }     
             
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
        });

/* Liste des groupes pour un tournoi
* url - /public/tournament/{id_tournoi}/groupes
* headears - content id_user and API_KEY
* methode - GET
*/
$app->get('/public/tournament/{id_tournoi}/groupes', function (Request $request, Response $response) {
            // Prépartaion de la ruquête sur une seule table
            $id = $request->getAttribute('id_tournoi');
            $table = 'groupes';
            $fields = '*';
            $field_filter = 'id_tournoi';

            $db = new DbHandler();
            $res = $db->getDetailsByID($table, $fields, $field_filter, $id);
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "Impossible de récupérer les données. S'il vous plaît essayer à nouveau";
                return echoRespnse(200, $response, $data);
            }
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
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
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "Impossible d'accèder aux données. S'il vous plaît essayer à nouveau";
                return echoRespnse(200, $response, $data);
            }     
             
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
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
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "Impossible d'accèder aux données. S'il vous plaît essayer à nouveau";
                return echoRespnse(200, $response, $data);
            }     
             
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
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
            
            //$res = "test";
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "Impossible d'accèder aux données. S'il vous plaît essayer à nouveau";
                return echoRespnse(200, $response, $data);
            }     
             
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
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
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "Impossible d'accèder aux données. S'il vous plaît essayer à nouveau";
                return echoRespnse(200, $response, $data);
            }     
             
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
        });  


/* Liste les infos d'un terrain selon son id
* url - /public/terrain/{id}
* methode - GET
*/
$app->get('/public/terrain/{id}', function (Request $request, Response $response) {
            $id = $request->getAttribute('id');          
            $db = new DbHandler();
            $res = $db->getPitchById($id);
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "Impossible de récupérer les données. S'il vous plaît essayer à nouveau";
                return echoRespnse(200, $response, $data);
            }
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
        });

/* Liste les tournois qui on un statut de type :  (Nouveau, Ouvert et Clos)
* url - /public/tournaments
* methode - GET
* Paramètre spécifant le statut
     * @Pamam - statuts :
     *              1 : Nouveau
     *              2 : Ouvert
     *              3 : Clos
*/
$app->get('/public/tournaments/statut/{id}', function (Request $request, Response $response) {
            $id = $request->getAttribute('id'); 
            $db = new DbHandler();
            $res = $db->getTournamentsStatut($id);
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "Impossible de récupérer les données. S'il vous plaît essayer à nouveau";
                return echoRespnse(200, $response, $data);
            }
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
        });

/* Liste les tournois qui on un statut de type au minimum 2:  (Ouvert et Clos)
* url - /public/tournaments
* methode - GET
* Paramètre spécifant le statut
     * @Pamam - id du tournoi
*/
$app->get('/public/tournament/{id}', function (Request $request, Response $response) {
            $id = $request->getAttribute('id'); 
            $db = new DbHandler();
            // Prépartaion de la ruquête sur une seule table
            $table = 'tournois';
            $fields = '*';
            $field_filter = 'id_tournoi';
            $res = $db->getDetailsByID($table, $fields, $field_filter, $id);
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res[0];
            } else {
                $data["error"] = true;
                $data["message"] = "Impossible de récupérer les données. S'il vous plaît essayer à nouveau";
                return echoRespnse(200, $response, $data);
            }
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
        });

/* Liste les tournois
* url - /public/tournaments
* methode - GET
*/
$app->get('/public/tournaments', function (Request $request, Response $response) {
            $db = new DbHandler();
            $res = $db->getTournaments();
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "Impossible de récupérer les données. S'il vous plaît essayer à nouveau";
                return echoRespnse(200, $response, $data);
            }
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
        });
