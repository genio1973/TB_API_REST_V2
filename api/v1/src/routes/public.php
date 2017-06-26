<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$app->get('/public/login/{user}/{pass}', function (Request $request, Response $response) {
   
    // Grab User submitted information
    $email = $request->getAttribute('user');
    $pass = $request->getAttribute('pass');
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);

    $sql = 'SELECT id, email, id_role, token, token_expire FROM user WHERE email = "'.$email.'" AND mot_de_passe = "' .$pass .'"';

    try{
        // Get DB
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->query($sql);
        $user = $stmt->fetch(PDO::FETCH_OBJ);
        if($user){
            $token = bin2hex(openssl_random_pseudo_bytes(8)); //generate a random token
            $tokenExpiration = date('Y-m-d H:i:s', strtotime('+1 hour'));//the expiration date will be in one hour from the current moment

            //update User's token
            $sql = "UPDATE user SET
                token = :token,
                token_expire = :token_expire
                WHERE email= :email";

                $stmt = $db->prepare($sql);

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