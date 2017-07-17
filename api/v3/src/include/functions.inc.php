<?php
/**
 * Vérification params nécessaires posté ou non
 */
 /*
function verifyRequiredParams($required_fields, $response) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Manipulation paramsde la demande PUT
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        parse_str($app->request()->getBody(), $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }
    if (!$error) {
        //Champ (s) requis sont manquants ou vides
        // echo erreur JSON et d'arrêter l'application
        $data = array();
        $data["error"] = true;
        $data["message"] = 'Champ(s) requis ' . substr($error_fields, 0, -2) . ' est (sont) manquant(s) ou vide(s)';
        return echoRespnse(400, $response, $data);
        //echoRespnse(400, $response);
       // $app->stop();
    }
    return true;
}
*/
/**
 * Validation adresse e-mail
 */
function validateEmail($email, $response) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $data["error"] = true;
        $data["message"] = "Adresse e-mail n'est pas valide";
        return echoRespnse(400, $response, $data);
        //$app->stop();
    }
    return true;
}

/**
 * Vérification des champs nécessaires avant de passé une requête
 * renvoi uniquement les valeur de $data pour les clés
 * se trouvant dans $fieldsToCheck
 * @param Array avec les clés à tester
 * @param Array avec les champs autorisés
 * @return Array avec les clé et valeurs acceptées 
 */
function verifyRequiredFields($data, $fieldsToCheck) {
    $arrayFields = array();
    foreach ($data as $key => $value){
        if(in_array($key, $fieldsToCheck) ){
                $arrayFields[$key] = $value;
        }
    }
    return $arrayFields;
}

/**
 * Faisant écho à la réponse JSON au client
 * @param String $status_code  Code de réponse HTTP
 * @param Response $response
 * @param Data[] $data to convert in Json
 */
function echoRespnse($status_code, $response, $data) {
    // Code de réponse HTTP

    // la mise en réponse type de contenu en JSON
/*
    return $response->withStatus($status_code)
        ->withHeader('Content-Type', 'application/json')
        ->withJson($data);
*/

        return $response->withStatus($status_code)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($data));

    //echo utf8_encode(json_encode($response));
}
