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

$app->post('/public/user/login', function(Request $request, Response $response){
                        //function() use ($app) {

            // lecture des params de post
            $email = $request->getParam('email');
            //$email = $allPostPutVars['email'];
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
                    var_dump($user);
                    if($user['status']==1)
                    {
                        $data['error'] = false;
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

 /* Enregistrement de l'utilisateur
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





/************************************************************************************
API responsables de tournoi
Routes par défauts : vx/responsable/route
*************************************************************************************/





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
