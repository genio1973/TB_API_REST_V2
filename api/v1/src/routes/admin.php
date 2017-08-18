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
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] = "Impossible de créer le rôle. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
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
                $data["result"] = $roles;

                return echoRespnse(200, $response, $data);
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] = "Impossible de réucupérer les rôles. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
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
                $data["result"] = $role_id;
                return echoRespnse(200, $response, $data);
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] = "Impossible de réucupérer le rôle. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
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
            $id_role = $request->getParam('id_role');
            $nom = $request->getParam('nom_user');
            $prenom = $request->getParam('prenom_user');
            $status = $request->getParam('status');
            
            // valider adresse email
            $res = validateEmail($email, $response);
            if($res !== true) {
                return $res;
            }

            $db = new DbHandler();
            $res = $db->createUser($email, $password, $id_role, $nom, $prenom, $status);
            
            $data = array();
            if ($res == $config['message']['USER_CREATED_SUCCESSFULLY']) {
                $data["error"] = false;
                $data["message"] = "201";
                $data["result"] = "Nouvel utilisateur inscrit avec succès";
                $code_status = 201;
            } else if ($res == $config['message']['USER_CREATE_FAILED']) {
                $data["error"] = true;
                $data["message"] = "401";
                $data["result"] = "Oops! Une erreur est survenue lors de l'inscription";
                $code_status = 401;
            } else if ($res == $config['message']['USER_ALREADY_EXISTED']) {
                $data["error"] = true;
                $data["message"] = "401";
                $data["result"] = "Désolé, cet E-mail éxiste déja";
                $code_status = 401;
            }
            // echo de la réponse  JSON
            return echoRespnse($code_status, $response, $data);

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

            if ($users != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $users;
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] = "Impossible de réucupérer les utilisateurs. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
            }

            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
        });

/* Get an user by id
 * url - /admin/user
 * headears - content id_user and API_KEY
 * methode - GET
 */
$app->get('/user/{id}', function (Request $request, Response $response) {
            $id = $request->getAttribute('id');
            $db = new DbHandler();
            $user = $db->getUserById($id);
            if ($user != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $user;
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] = "Impossible de réucupérer les utilisateurs. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
            }

            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
        });

/* Get an user by email
 * url - /admin/user
 * headears - content id_user and API_KEY
 * methode - GET
 */
$app->get('/userbyemail/{email}', function (Request $request, Response $response) {
            $email = $request->getAttribute('email');
            $db = new DbHandler();
            $user = $db->getUserByEmail($email);
            if ($user != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $user;
            } else {
                $data["error"] = true;
               $data["message"] = "400";
                $data["result"] = "Impossible de réucupérer les utilisateurs. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
            }

            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
        });
/* Liste des tournois
 * url - /admin/tournois
 * headears - content id_user and API_KEY
 * methode - GET
 */
$app->get('/tournois', function (Request $request, Response $response) {
            $db = new DbHandler();
            $res = $db->getTournaments();
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] = "Impossible de réucupérer les tournois. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
            }
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);

        });

/* Informations d'un tournoi, selon son id
 * url - /admin/tournoi/{id_tournoi}
 * headears - content id_user and API_KEY
 * methode - GET
 */
$app->get('/tournoi/{id}', function (Request $request, Response $response) {
            $id_tournoi = $request->getAttribute('id');

            $db = new DbHandler();
            $res = $db->getTournamentById($id_tournoi);

            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] = "Impossible de réucupérer le tournoi. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
            }
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
        });

/* Liste des tournois créés par un utilateur, selon son email
 * url - /admin/tournoi/email/{email}
 * headears - content id_user and API_KEY
 * methode - GET
 */
$app->get('/tournois/email/{email}', function (Request $request, Response $response) {
            $email = $request->getAttribute('email');

            $db = new DbHandler();
            $res = $db->getTournamentCreatedUserByEmail($email);
            //$res = $db->getTournamentById($id_tournoi);

            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
               $data["message"] = "400";
                $data["result"] = "Impossible de réucupérer le tournoi. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
            }
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);

        });


/* Liste des tournois créés par un utilateur, selon son id
 * url - /admin/tournois/id/{id_user}
 * headears - content id_user and API_KEY
 * methode - GET
 */
$app->get('/tournois/id/{id_user}', function (Request $request, Response $response) {
        $id = $request->getAttribute('id_user');

        $db = new DbHandler();
        $res = $db->getTournamentCreatedUserById($id);
        if ($res != NULL) {
            $data["error"] = false;
            $data["message"] = "200";
            $data["result"] = $res;
        } else {
            $data["error"] = true;
            $data["message"] = "400";
                $data["result"] = "Impossible de réucupérer les données. S'il vous plaît essayer à nouveau";
            return echoRespnse(400, $response, $data);
        }
        // echo de la réponse  JSON
        return echoRespnse(200, $response, $data);
    });

/* Suppression d'un user (responsable) 
* url - /admin/user/{id}
* methode - DELETE
* headears - content id_user and API_KEY
* body - Json : -
* return - {
*            "error": false,
*            "message": null,
*            "result": ...
*           }
*/
$app->delete('/user/{id}', function(Request $request, Response $response) use ($app) {
        $resultat['error'] = FALSE;
        $resultat['message'] = "";

        // récupère l'id du responsable en cours
        $headers = $request->getHeaders();
        $id_current_user = $headers['HTTP_USERID'][0];
        $id = $request->getAttribute('id');

        if($id_current_user == $id){
            $resultat['error'] = TRUE;
            $data["message"] = "400";
            $data["result"] = "Permission refusée. Vous ne pouvez pas supprimer votre compte !";
            return echoRespnse(400, $response, $resultat);
        }

        $db = new DbHandler();
        // suppression de l'utilisateur' mais pas de cascade
        $res = $db->deleteByID('users', $id);
        $data=NULL;
        if ($res != NULL) {
            $data["error"] = false;
            $data["message"] = "Attention aux tournois créés par l'utilisateur supprimé. Ils existent toujours, mais ne sont affectés à personne. A vous de les supprimer ou des les affecter à qq d'autres";
            $data["result"] = $res;
        } else {
            $data["error"] = true;
            $data["message"] = "400";
            $data["result"] = "Impossible de supprimer les données. S'il vous plaît essayer à nouveau";
            return echoRespnse(400, $response, $data);
        }     

        // echo de la réponse  JSON
        return echoRespnse(200, $response, $data);
    });   


/* Suppression d'un tournoi 
* url - /admin/tournoi
* methode - DELETE
* headears - content id_user and API_KEY
* body - Json : -
* return - {
*            "error": false,
*            "message": null,
*            "nombre_suppression": 1,
*            "id_supprimer": "
*           }
*/
$app->delete('/tournoi/{id}', function(Request $request, Response $response) use ($app) {
        $resultat['error'] = FALSE;
        $resultat['message'] = "";

        // récupère l'id du responsable en cours
        $headers = $request->getHeaders();
        $id_current_user = $headers['HTTP_USERID'][0];
        $id = $request->getAttribute('id');

        $db = new DbHandler();                                      
        // suppression du tournoi en cascade avec les enfants du tournoi
        $res = $db->deleteByID('tournois', $id);
        $data=NULL;
        if ($res != NULL) {
            $data["error"] = false;
            $data["message"] = "200";
            $data["result"] = $res;
        } else {
            $data["error"] = true;
            $data["message"] = "400";
            $data["result"] = "Impossible de supprimer les données. S'il vous plaît essayer à nouveau";
            return echoRespnse(400, $response, $data);
        }

        // echo de la réponse  JSON
        return echoRespnse(200, $response, $data);
    });

  
/* Modifier les données d'un utilisateur, l'un ou plusieurs champs : 'email', 'mot_de_passe', 'token', 'token_expire', 'id_role', 'nom_user', 'prenom_user', 'satus'
* url - /admin/personne/{id}
* methode - PUT
* headears - content id_user and API_KEY
* body - Json : Ne mettre que les champ que l'en veut modifier
*               {"prenom_user":"Nicole","nom_user":"Schnyder","email":"nicole.schnyder@vebb.com","mot_de_passe":"pass","id_role":"2", "status":"1" }
* return - {
*            "error": false,
*            "message": null,
*            "id": 1,
*           }
*/
$app->put('/user/{id}', function(Request $request, Response $response) use ($app) {
            // récupère les données passée aux forma json
            $json = $request->getBody();
            $data = json_decode($json, true); // transofme en tableau associatif
            $id_user = $request->getAttribute('id');

            // filtre les champs qu'il faut mettre à jour
            $fieldsToCheck = array('email', 'mot_de_passe', 'prenom_user', 'nom_user', 'id_role', 'status');
            $arrayFields = filterRequiredFields($data, $fieldsToCheck);

            // si le mot de passe est mis à jour, alors le hacher !
            if(isset($arrayFields['mot_de_passe'])){
                //Générer un hash de mot de passe
                $arrayFields['mot_de_passe'] = PassHash::hash($arrayFields['mot_de_passe']);
            }

            // si le mail est mis à jour, alors le vérifier !
            if(isset($arrayFields['email'])){
                $res = validateEmail($arrayFields['email'], $response);
                if($res !== true){
                    return $res;
                }
            }

            $db = new DbHandler();
            $res = $db->updateByID('users', $arrayFields, $id_user);
            $data=NULL;
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] = "Impossible de mettre à jour les données. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
            }  

            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
        });