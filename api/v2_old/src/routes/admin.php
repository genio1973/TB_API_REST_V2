<?php
    $app->get('/hello1', function ($request, $response) {
        $data['auth'] = 'Hello ADMIN 1';
            return echoRespnse(200, $response, $data);
    });
    $app->get('/hello2', function ($request, $response) {
        $data['auth'] = 'Hello ADMIN 2';
            return echoRespnse(200, $response, $data);
    });

    
/**
 *Création d'une nouveau role dans db
 * method POST
 * params - name
 * url - /role/{id}/{droits}
 */
$app->post('/user/role/{id}/{droits}', function(Request $request, Response $response) {

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
 * Récupèreation d'un rôle dans db
 * method GET
 * params - name
 * url - /role/{id}
 */
 
$app->get('/user/role/{id}', function(Request $request, Response $response) {

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

        /**
 * Enregistrement de l'utilisateur
 * url - /register
 * methode - POST
 * params - email, password, role
 */
$app->post('/user/register', function(Request $request, Response $response) {
            // lecture des params de post
            $email = $request->getParam('email');
            $password = $request->getParam('password');
            $id_role = $request->getParam('role');

            // valider adresse email
            $res = validateEmail($email, $response);
            if($res !== true)
            {
                return $res;
            }
            $db = new DbHandler();
            $res = $db->createUser($email, $password, $id_role);
            $data = array();
            if ($res == USER_CREATED_SUCCESSFULLY) {
                $data["error"] = false;
                $data["message"] = "Vous êtes inscrit avec succès";
            } else if ($res == USER_CREATE_FAILED) {
                $data["error"] = true;
                $data["message"] = "Oops! Une erreur est survenue lors de l'inscription";
            } else if ($res == USER_ALREADY_EXISTED) {
                $data["error"] = true;
                $data["message"] = "Désolé, cet E-mail éxiste déja";
            }
            // echo de la repense  JSON
            return echoRespnse(201, $response, $data);
        });
