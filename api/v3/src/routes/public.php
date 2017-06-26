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
            if($res !== true)
            {
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
                    if($user['status']==1)
                    {
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


/************************************************************************************
API admin
Routes par défauts : vx/admin/route
*************************************************************************************/

 /* Enregistrement  de l'utilisateur (création d'un nouveau')
 * url - /admin/register
 * methode - POST
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

            if($res !== true)
            {
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
            // echo de la repense  JSON
            return echoRespnse(201, $response, $data);

        });


// Get All users
/* Liste des utilisateurs
 * url - /admin/users
 * methode - GET
 */
$app->get('/admin/users', function (Request $request, Response $response) {
            $db = new DbHandler();
            $users = $db->getUsers();

            // echo de la repense  JSON
            return echoRespnse(201, $response, $users);

        });

/* Liste des tournois
 * url - /admin/tournaments
 * methode - GET
 */
$app->get('/admin/tournaments', function (Request $request, Response $response) {
            $db = new DbHandler();
            $users = $db->getTournaments();

            // echo de la repense  JSON
            return echoRespnse(201, $response, $users);

        });

/* Informations d'un tournoi, selon son id
 * url - /admin/tournament/{id_tournoi}
 * methode - GET
 */
$app->get('/admin/tournament/{id}', function (Request $request, Response $response) {
            $id_tournoi = $request->getAttribute('id');

            $db = new DbHandler();
            $res = $db->getTournamentById($id_tournoi);

            // echo de la repense  JSON
            return echoRespnse(201, $response, $res);

        });

/* Liste des tournois créés par un utilateur, selon son email
 * url - /admin/tournament/{email}
 * methode - GET
 */
$app->get('/admin/tournaments/email/{email}', function (Request $request, Response $response) {
            $email = $request->getAttribute('email');

            $db = new DbHandler();
            $res = $db->getTournamentCreatedUserByEmail($email);

            // echo de la repense  JSON
            return echoRespnse(201, $response, $res);

        });

/* Liste des tournois créés par un utilateur, selon son id
 * url - /admin/tournament/{email}
 * methode - GET
 */
$app->get('/admin/tournaments/id/{id}', function (Request $request, Response $response) {
            $id = $request->getAttribute('id');

            $db = new DbHandler();
            $res = $db->getTournamentCreatedUserById($id);

            // echo de la repense  JSON
            return echoRespnse(201, $response, $res);

        });


/************************************************************************************
API responsables de tournoi
Routes par défauts : vx/responsable/route
*************************************************************************************/


/* Liste des tournois créés par l'utilisateur en cours, selon son id dans son entête
 * url - /resp/tournaments
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

            // echo de la repense  JSON
            return echoRespnse(201, $response, $res);
        });


/* Liste des équipes pour un tournoi appartenant à l'utilisateur en cours, selon son id dans son entête
 * url - /resp/tournament/{id_tournoi}
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

            // echo de la repense  JSON
            return echoRespnse(201, $response, $res);
        });

/* Liste des équipes dans un groupe appartenant à l'utilisateur en cours, selon son id dans son entête
 * url - /resp/tournament/{id_tournoi}/equipes/groupe/{id_groupe}
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

            // echo de la repense  JSON
            return echoRespnse(201, $response, $res);
        });



/* Liste des matchs pour un tournoi à l'utilisateur en cours, selon son id dans son entête
 * url - /resp/tournament/{id_tournoi}/matchs
 * methode - GET
 */
$app->get('/resp/tournament/{id_tournoi}/matchs', function (Request $request, Response $response) {
            // Obtenir les en-têtes de requêtes
            // Nullement besoin de test la présence, car cela est fait précédement
            // en vérifiant l'authentifcation sur la route du group responsable
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            $id_tournoi = $request->getAttribute('id_tournoi');

            $db = new DbHandler();
            $res = array();
            $res = $db->getMatchsByTournamentIdAndUserId($id_current_user, $id_tournoi);

            // echo de la repense  JSON
            return echoRespnse(201, $response, $res);
        });

/* Liste des matchs d'un groupe pour l'utilisateur en cours, selon son id dans son entête
 * url - /resp/tournament/matchs/groupe/{id_groupe}
 * methode - GET
 */
$app->get('/resp/tournament/matchs/groupe/{id_groupe}', function (Request $request, Response $response) {
            // Obtenir les en-têtes de requêtes
            // Nullement besoin de test la présence, car cela est fait précédement
            // en vérifiant l'authentifcation sur la route du group responsable
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            $id_groupe = $request->getAttribute('id_groupe');

            $db = new DbHandler();
            $res = array();
            $res = $db->getMatchsByGroupAndUserId($id_current_user, $id_groupe);
            //$res['id_groupe'] = $id_groupe;
            //$res['id_current_user'] = $id_current_user;
            // echo de la repense  JSON
            return echoRespnse(201, $response, $res);
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
