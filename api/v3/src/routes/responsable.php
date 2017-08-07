<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require 'src/include/config.php';

/************************************************************************************
API responsable
Routes par défauts : vx/resp/route
*************************************************************************************/
        /** Récuère les infos de l'utilisateur en cours
        * url - /resp/account
        * headears - content id_user and API_KEY
        * methode - GET
        **/
        $app->get('/account', function (Request $request, Response $response)  {
            // Obtenir les en-têtes de requêtes
            // Nullement besoin de test la présence, car cela est fait précédement
            // en vérifiant l'authentifcation sur la route du group responsable
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            $db = new DbHandler();
            $res = array();
            $res = $db->getUserById($id_current_user);
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] = "Impossible de récupérer les données. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
            }
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
        });

        /** Récuère tous les status à dispo
        * url - /resp/tournois/statuts
        * headears - content id_user and API_KEY
        * methode - GET
        **/
        $app->get('/tournois/statuts', function (Request $request, Response $response)  {
            $db = new DbHandler();
            $res = array();
            $res = $db->getAllStatuts();
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] = "Impossible de récupérer les données. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
            }
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
        });

        /* Liste des équipes coachées par une personne
        * url - /resp/personne/{id_tournoi}/equipes
        * methode - GET
        */
        $app->get('/personne/{id_personne}/equipes', function (Request $request, Response $response) {
            $headers = $request->getHeaders();            
            $id_current_user = $headers['HTTP_USERID'][0];
            $id = $request->getAttribute('id_personne');

            $db = new DbHandler();
            $res = $db->isPeopleOwner($id_current_user, $id); // Vérifie que l'utilisateur courant est le propriétaire
            if(!$res){
                $resultat['error'] = TRUE;
                $resultat['message'] = "400";
                $resultat["result"] = "Permission refusée pour votre identifiant ou id non trouvé !";
                return echoRespnse(400, $response, $resultat);
            }

            // Préparation de la requête
            $table = 'equipes';
            $fields = '*';
            $clause = 'WHERE id_personne LIKE ' . $id;
            $res = $db->getDetailsByClause($table, $fields, $clause);
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] = "Impossible d'accèder aux données. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
            }     
             
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
        });


        /* Liste des personnes du responsable en cours
        * url - /resp/personne/{id_tournoi}/equipes
        * methode - GET
        */
        $app->get('/personnes', function (Request $request, Response $response) {
            $headers = $request->getHeaders();            
            $id_current_user = $headers['HTTP_USERID'][0];

            $db = new DbHandler();
            // Préparation de la requête
            $table = 'personnes';
            $fields = '*';
            $clause = 'WHERE id_user LIKE ' . $id_current_user;
            $res = $db->getDetailsByClause($table, $fields, $clause);
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] = "Impossible d'accèder aux données. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
            }     
             
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
        });


        /* Liste des tournois créés par l'utilisateur en cours, selon son id dans son entête
        * url - /resp/tournois
        * headears - content id_user and API_KEY
        * methode - GET
        */
        $app->get('/tournois', function (Request $request, Response $response)  {

            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            $db = new DbHandler();
            $res = array();
            $res = $db->getTournamentCreatedUserById($id_current_user);
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] = "Impossible de récupérer les données. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
            }
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
        });


        /* Liste les info d'un tournoi
        * url - /public/tournoi
        * headears - content id_user and API_KEY
        * methode - GET
        * Paramètre spécifant le statut
            * @Pamam - id du tournoi
        */
        $app->get('/tournoi/{id}', function (Request $request, Response $response) {
            require 'src/include/config.php';
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            $id = $request->getAttribute('id'); 
            $db = new DbHandler();

            $res = $db->getTournamentById($id);
            if ($res != NULL) {
                //est-il le propriéataire de ce tournoi ?
                $prop = $db->isTournamentOwner($id_current_user, $res['id_tournoi']);
                $role = $db->getRoleById($id_current_user); // ou alors on est admin
                if(!$prop && $role['id_role'] != $config['role']['ADMIN']){
                    $resultat['error'] = TRUE;
                    $resultat['message'] = "401";
                    $resultat["result"] = "Permission refusée pour votre identifiant, mauvais numéro de tournoi !";
                    return echoRespnse(401, $response, $resultat);
                }
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] = "Impossible de récupérer les données. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
            }
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
        });


        /* Liste des équipes pour un tournoi appartenant à l'utilisateur en cours, selon son id dans son entête
        * url - /resp/tournoi/{id_tournoi}/equipes
        * headears - content id_user and API_KEY
        * methode - GET
        */
        $app->get('/tournoi/{id_tournoi}/equipes', function (Request $request, Response $response) {
            // Obtenir les en-têtes de requêtes
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            $id_tournoi = $request->getAttribute('id_tournoi');

            $db = new DbHandler();
            $res = array();
            $res = $db->getTeamsTournamentByIdAndUserId($id_current_user, $id_tournoi);
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] = "Impossible de récupérer les données. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
            }
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
        });

       /** Liste des personnes coachant une équipe dans un tournoi appartenant à l'utilisateur en cours, selon son id dans son entête
        * url - /resp/tournoi/{id_tournoi}/personnes
        * headears - content id_user and API_KEY
        * methode - GET
        */
        $app->get('/tournoi/{id_tournoi}/personnes', function (Request $request, Response $response) {
            // Obtenir les en-têtes de requêtes
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            $id_tournoi = $request->getAttribute('id_tournoi');

            $db = new DbHandler();
            $res = array();
            $res = $db->getPeopleTournamentById($id_current_user, $id_tournoi);
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] = "Impossible de récupérer les données. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
            }
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
        });
        

        /* Liste des équipes dans un groupe appartenant 
        * url - /resp/groupe/{id_groupe}/equipes
        * headears - content id_user and API_KEY
        * methode - GET
        */
        $app->get('/groupe/{id_groupe}/equipes', function (Request $request, Response $response) {
            $id_groupe = $request->getAttribute('id_groupe');

            $db = new DbHandler();
            $res = array();
            $res = $db->getTeamsByGroupById( $id_groupe);
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] = "Impossible de récupérer les données. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
            }
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
        });

        /* Liste des terrains de l'utilisateur courant
        * url - /resp/terrains
        * headears - content id_user and API_KEY
        * methode - GET
        */
        $app->get('/terrains', function (Request $request, Response $response) {
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];            

            $db = new DbHandler();
            $res = array();
            $res = $db->getPitchesByUserId($id_current_user);
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] = "Impossible de récupérer les données. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
            }
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
        });

       

        /* Liste des équipes dans un groupe appartenant à l'utilisateur en cours et en précisant un id de tournoi, selon son id dans son entête
        * url - /resp/tournoi/{id_tournoi}/equipes/groupe/{id_groupe}
        * headears - content id_user and API_KEY
        * methode - GET
        */
        $app->get('/tournoi/{id_tournoi}/equipes/groupe/{id_groupe}', function (Request $request, Response $response) {
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
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] = "Impossible de récupérer les données. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
            }
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
        });        


        /* Ajout d'un tournoi (création d'un nouveau')
        * url - /resp/tournoi
        * methode - POST
        * headears - content id_user and API_KEY
        * body - Json : {"nom_tournoi":"2017-09-15 SVRN", "date_Debut"}
        * return - {
        *            "error": false,
        *            "message": null,
        *            "nombre_insert": 1,
        *            "id_dernier_insert": 5,
        *            "id_premier_insert": 5
        *           }
        */
        $app->post('/tournoi', function(Request $request, Response $response) use ($app) {
            // récupère les données passée aux forma json
            $json = $request->getBody();
            $data = json_decode($json, true); // transofme en tableau associatif

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            // filtre les champs qu'il faut mettre à jour
            $fieldsToCheck = array("nom_tournoi","date_debut");
            $data = filterRequiredFields($data, $fieldsToCheck);
            //return echoRespnse(400, $response, verifyRequiredFields($data, $fieldsToCheck));
            
            if(!verifyRequiredFields($data, $fieldsToCheck) ){
                $resultat['error'] = TRUE;
                $resultat['message'] = "400";
                $resultat["result"] = "Contrôllez les noms des champs. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $resultat);
            }

            $db = new DbHandler();
            $res = $db->createTournament($data['nom_tournoi'], $id_current_user, $data['date_debut']);
            $data = NULL;
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] = "Impossible d'exécuter la requête. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
            }

            // echo de la réponse  JSON
            return echoRespnse(201, $response, $data);
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
        *            "message": null,
        *            "nombre_insert": 2,
        *            "id_dernier_insert": 55,
        *            "id_premier_insert": 53
        *           }
        */
        $app->post('/personnes', function(Request $request, Response $response) use ($app) {
            // récupère les données passée aux forma json
            $json = $request->getBody();
            $data = json_decode($json, true); // transofme en tableau associatif
            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            // pour chaque personne ajouter l'utilisateur qui en sera le propriétaire
            foreach($data as $p){
                $p['id_user'] = $id_current_user;
                $dataNew[] = $p;
            }
            $data = $dataNew;
            
            // Contrôle que les champs soient cohérents
            $fieldsToCheck = array("prenom", "nom", "courriel", "tel", "tel_mobile", "adresse", "localite", "pays", "id_user");
            $data = filterRequiredFieldsArray($data, $fieldsToCheck);
            if(!verifyRequiredFieldsArray($data, $fieldsToCheck) ){
                $resultat['error'] = TRUE;
                $resultat['message'] = "402";
                $resultat["result"] = "Contrôllez les noms des champs. S'il vous plaît essayer à nouveau";
                return echoRespnse(402, $response, $resultat);
            }
            $db = new DbHandler();
            //$res = $db->createPersons($data);
            $res = $db->createMultiple('personnes', $data);
            $data=NULL;
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] = "Impossible d'insérer les données. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
            }
            // echo de la réponse  JSON
            return echoRespnse(201, $response, $data);
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
        *            "message": null,
        *            "nombre_insert": 2,
        *            "id_dernier_insert": 55,
        *            "id_premier_insert": 53
        *           }
        */
        $app->post('/equipes', function(Request $request, Response $response) use ($app) {
            // récupère les données passée aux forma json
            $json = $request->getBody();
            $data = json_decode($json, true); // transofme en tableau associatif

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            // Contrôle que les champs soient cohérents
            $fieldsToCheck = array("niveau", "nb_pts", "nom_equipe","id_groupe", "id_personne");
            $data = filterRequiredFieldsArray($data, $fieldsToCheck);
            if(!verifyRequiredFieldsArray($data, $fieldsToCheck) ){
                $resultat['error'] = TRUE;
                $resultat['message'] = "400";
                $resultat["result"] =  "Contrôllez les noms des champs. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $resultat);
            }

            $db = new DbHandler();

            // permission pour ajouter dans les groupes lui appartenant
            foreach($data as $equipe){
                $res = $db->isGroupOwner($id_current_user, $equipe['id_groupe']);
                if(!$res){
                    $resultat['error'] = TRUE;
                    $resultat['message'] = "201";
                    $resultat["result"] =  "Permission refusée pour votre identitifant pour au moins une équipe mentionnées dans le mauvais numéro de groupe !";
                    return echoRespnse(201, $response, $resultat);
                }
            }
            
            // insertion des enregistrements
            $res = $db->createMultiple('equipes', $data);
            $data=NULL;
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] =  "Impossible d'insérer les données. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
            }
            // echo de la réponse  JSON
            return echoRespnse(201, $response, $data);
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
        *            "message": null,
        *            "nombre_insert": 2,
        *            "id_dernier_insert": 55,
        *            "id_premier_insert": 53
        *           }
        */
        $app->post('/groupes', function(Request $request, Response $response) use ($app) {
            // récupère les données passée aux forma json
            $json = $request->getBody();
            $data = json_decode($json, true); // transofme en tableau associatif

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            // Contrôle que les champs soient cohérents
            $fieldsToCheck = array("nom_groupe","id_tournoi");
            $data = filterRequiredFieldsArray($data, $fieldsToCheck);
            if(!verifyRequiredFieldsArray($data, $fieldsToCheck) ){
                $resultat['error'] = TRUE;
                $resultat['message'] = "400";
                $resultat["result"] = "Contrôllez les noms des champs. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $resultat);
            }

            $db = new DbHandler();
            // permission pour ajouter dans les groupes lui appartenant
            foreach($data as $groupe){
                $res = $db->isTournamentOwner($id_current_user, $groupe['id_tournoi']);
                if(!$res){
                    $resultat['error'] = TRUE;
                    $resultat['message'] = "201";
                    $resultat["result"] = "Permission refusée pour votre identifiant dans au moins un groupe mentionnés dans le mauvais numéro de tournoi !";
                    return echoRespnse(201, $response, $resultat);
                }
            }

            // insertion des enregistrements
            $res = $db->createMultiple('groupes', $data);
            $data=NULL;
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] = "Impossible d'insérer les données. S'il vous plaît essayer à nouveau";
                return echoRespnse(200, $response, $data);
            }
            // echo de la réponse  JSON
            return echoRespnse(201, $response, $data);            
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
        *            "message": null,
        *            "nombre_insert": 3,
        *            "id_dernier_insert": 55,
        *            "id_premier_insert": 53
        *           }
        */ 

        $app->post('/terrains', function(Request $request, Response $response) use ($app) {
            // récupère les données passée aux forma json
            $json = $request->getBody();
            $data = json_decode($json, true); // transofme en tableau associatif
            
            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            // filtre les champs qu'il faut mettre à jour
            $fieldsToCheck = array("nom_terrain");
            $data = filterRequiredFieldsArray($data, $fieldsToCheck);

            if(!verifyRequiredFieldsArray($data, $fieldsToCheck) ){
                $resultat['error'] = TRUE;
                $resultat['message'] = "400";
                $resultat["result"] = "Contrôllez les noms des champs. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $resultat);
            }

            // Ajouter l'appartenance des terrains créés
            // foreach($data as $key=>$terrain){
            //     $data[$key]['id_user'] = $id_current_user;
            // }
            //return echoRespnse(201, $response, $data);

            $db = new DbHandler();
            // insertion des enregistrements
            $res = $db->createMultiple('terrains', $data);
            $data=NULL;
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] = "Impossible d'insérer les données. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
            }
            // echo de la réponse  JSON
            return echoRespnse(201, $response, $data);   
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
        *            "message": null,
        *            "nombre_insert": 3,
        *            "id_dernier_insert": 55,
        *            "id_premier_insert": 53
        *           }
        */
        $app->post('/matchs', function(Request $request, Response $response) use ($app) {
            // récupère les données passée aux forma json
            $json = $request->getBody();
            $data = json_decode($json, true); // transofme en tableau associatif

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            // Contrôle que les champs soient cohérents
            $fieldsToCheck = array("date_match","heure","id_user_dirige","id_terrain","id_equipe_home","id_equipe_visiteur","id_equipe_arbitre", "satut");
            $data = filterRequiredFieldsArray($data, $fieldsToCheck);
            if(!verifyRequiredFieldsArray($data, $fieldsToCheck) ){
                $resultat['error'] = TRUE;
                $resultat['message'] = "400";
                $resultat["result"] = "Contrôllez les noms des champs. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $resultat);
            }

            $db = new DbHandler();
            // permission pour ajouter dans des matchs des groupes lui appartenant
            foreach($data as $match){
                if(    !$db->isTeamOwner($id_current_user, $match['id_equipe_home']) 
                    || !$db->isTeamOwner($id_current_user, $match['id_equipe_visiteur'])
                    || (isset($match['id_equipe_arbitre']) && !$db->isTeamOwner($id_current_user, $match['id_equipe_arbitre']))){
                        $resultat['error'] = TRUE;
                        $resultat['message'] = "201";
                        $resultat["result"] = "Permission refusée pour votre identitifant. Au moins une équipe mentionnées ne correspond pas au numéro de groupe !";
                        return echoRespnse(201, $response, $resultat);
                }
            }
            
            // insertion des enregistrements
            $res = $db->createMultiple('matchs', $data);
            //return echoRespnse(400, $response, $res);
            $data=NULL;
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] = "Impossible d'insérer les données. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
            }
            // echo de la réponse  JSON
            return echoRespnse(201, $response, $data);
        });

        /* Ajout de sets
        * url - /resp/sets
        * methode - POST
        * headears - content id_user and API_KEY
        * body - Json : [
        *	                {"score_home":"28","score_visiteur":"30","id_match":"11" },
        *	                {"score_home":"25","score_visiteur":"23","id_match":"11" },
        *	                {"score_home":"25","score_visiteur":"15","id_match":"11" }
        *               ]
        *
        * return - {
        *            "error": false,
        *            "message": null,
        *            "nombre_insert": 3,
        *            "id_dernier_insert": 55,
        *            "id_premier_insert": 53
        *           }
        */
        $app->post('/sets', function(Request $request, Response $response) use ($app) {
            // récupère les données passée aux forma json
            $json = $request->getBody();
            $data = json_decode($json, true); // transofme en tableau associatif

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            // Contrôle que les champs soient cohérents
            $fieldsToCheck = array("score_home","score_visiteur","id_match");
            $data = filterRequiredFieldsArray($data, $fieldsToCheck);
            if(!verifyRequiredFieldsArray($data, $fieldsToCheck) ){
                $resultat['error'] = TRUE;
                $resultat['message'] = "400";
                $resultat["result"] = "Contrôllez les noms des champs. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $resultat);
            }

            $db = new DbHandler();
            // permission pour ajouter dans des matchs des groupes lui appartenant
            foreach($data as $match){
                if( !$db->isMatchOwner($id_current_user, $match['id_match']) ){
                        $resultat['error'] = TRUE;
                        $resultat['message'] = "400";
                        $resultat["result"] = "Permission refusée pour votre identitifant. Au moins un sets pour un match mentionnés ne correspond pas au numéro de groupe vous appartenant !";
                        return echoRespnse(400, $response, $resultat);
                }
            }
            
            // insertion des enregistrements
            $res = $db->createMultiple('sets', $data);
            $data=NULL;
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] = "Impossible d'insérer les données. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
            }
            // echo de la réponse  JSON
            return echoRespnse(201, $response, $data);
        });



       /* Suppression d'un tournoi 
        * url - /resp/tournoi
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
            require 'src/include/config.php';
            $resultat['error'] = FALSE;
            $resultat['message'] = "";

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];
            $id = $request->getAttribute('id');

            $db = new DbHandler();            
            $res = $db->isTournamentOwner($id_current_user, $id); // Vérifie que l'utilisateur courant est le propriétaire
            $role = $db->getRoleById($id_current_user); // ou alors on est admin
            if(!$res && $role['id_role'] != $config['role']['ADMIN']){
                $resultat['error'] = TRUE;
                $resultat['message'] = "400";
                $resultat["result"] = "Permission refusée pour votre identifiant ou id non trouvé !";
                return echoRespnse(400, $response, $resultat);
            }
            /*            
            // supprime les terrains du tournoi manuellement
            // Pas de cascade, car on veut les consrrver en cas de suppression de matchs !
            if(!$db->deletePitchByTournamentID($id)){
                $resultat['error'] = TRUE;
                $resultat['message'] = "Problème de suppression des terrains !";
                return echoRespnse(200, $response, $resultat);
            }
            */           
            // suppression du tournoi en cascade avec les enfants du tournoi
            $res = $db->deleteByID('tournois', $id);
            $data=NULL;
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "Le tournoi est supprimé. Les terrains restent à dipsosition, si nécessaire supprimez-les!";
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


       /* Suppression d'un terrain 
        * url - /resp/terrain/{id}
        * methode - DELETE
        * headears - content id_user and API_KEY
        * body - Json : -
        * return - {
        *            "error": false,
        *            "message": null,
        *            "nombre_suppression": 1,
        *            "id_supprimer": "17"
        *           }
        */
        $app->delete('/terrain/{id}', function(Request $request, Response $response) use ($app) {
            $resultat['error'] = FALSE;
            $resultat['message'] = "";

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];
            $id = $request->getAttribute('id');
            
            $db = new DbHandler();
            //$res = $db->isPitchOwner($id_current_user, $id); // Vérifie que l'utilisateur courant est le propriétaire
            if(!$res){
                $resultat['error'] = TRUE;
                $resultat['message'] = "400";
                $resultat["result"] = "Permission refusée pour votre identifiant ou id non trouvé !";
                return echoRespnse(400, $response, $resultat);
            }
                       
            // suppression de l'équipe en cascade avec ses enfants
            $res = $db->deleteByID('terrains', $id);
            $data=NULL;
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] = "Impossible de supprimer les données. S'il vous plaît essayer à nouveau";
                return echoRespnse(401, $response, $data);
            }
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
        });



       /* Suppression d'une équipe 
        * url - /resp/equipe/{id}
        * methode - DELETE
        * headears - content id_user and API_KEY
        * body - Json : -
        * return - {
        *            "error": false,
        *            "message": null,
        *            "nombre_suppression": 1,
        *            "id_supprimer": "17"
        *           }
        */
        $app->delete('/equipe/{id}', function(Request $request, Response $response) use ($app) {
            $resultat['error'] = FALSE;
            $resultat['message'] = "";

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];
            $id = $request->getAttribute('id');
            
            $db = new DbHandler();
            $res = $db->isTeamOwner($id_current_user, $id); // Vérifie que l'utilisateur courant est le propriétaire
            if(!$res){
                $resultat['error'] = TRUE;
                $resultat['message'] = "400";
                $resultat["result"] = "Permission refusée pour votre identifiant ou id non trouvé !";
                return echoRespnse(400, $response, $resultat);
            }
                       
            // suppression de l'équipe en cascade avec ses enfants
            $res = $db->deleteByID('equipes', $id);
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


       /* Suppression d'un groupe 
        * url - /resp/groupe/{id}
        * methode - DELETE
        * headears - content id_user and API_KEY
        * body - Json : -
        * return - {
        *            "error": false,
        *            "message": null,
        *            "nombre_suppression": 1,
        *            "id_supprimer": "17"
        *           }
        */
        $app->delete('/groupe/{id}', function(Request $request, Response $response) use ($app) {
            $resultat['error'] = FALSE;
            $resultat['message'] = "";

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];
            $id = $request->getAttribute('id');

            $db = new DbHandler();
            $res = $db->isGroupOwner($id_current_user, $id); // Vérifie que l'utilisateur courant est le propriétaire
            if(!$res){
                $resultat['error'] = TRUE;
                $resultat['message'] = "400";
                $resultat["result"] = "Permission refusée pour votre identifiant ou id non trouvé !";
                return echoRespnse(400, $response, $resultat);
            }
                       
            // suppression de l'équipe en cascade avec ses enfants
            $res = $db->deleteByID('groupes', $id);
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

       /* Suppression d'un match 
        * url - /resp/match/{id}
        * methode - DELETE
        * headears - content id_user and API_KEY
        * body - Json : -
        * return - {
        *            "error": false,
        *            "message": null,
        *            "result": ...
        *           }
        */
        $app->delete('/match/{id}', function(Request $request, Response $response) use ($app) {
            $resultat['error'] = FALSE;
            $resultat['message'] = "";

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];
            $id = $request->getAttribute('id');

            $db = new DbHandler();
            $res = $db->isTeamOwner($id_current_user, $id); // Vérifie que l'utilisateur courant est le propriétaire
            if(!$res){
                $resultat['error'] = TRUE;
                $resultat['message'] = "400";
                $resultat["result"] = "Permission refusée pour votre identifiant ou id non trouvé !";
                return echoRespnse(400, $response, $resultat);
            }
                       
            // suppression de l'équipe en cascade avec ses enfants
            $res = $db->deleteByID('matchs', $id);
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


       /* Suppression d'une personne 
        * url - /resp/personne/{id}
        * methode - DELETE
        * headears - content id_user and API_KEY
        * body - Json : -
        * return - {
        *            "error": false,
        *            "message": null,
        *            "nombre_suppression": 1,
        *            "id_supprimer": "17"
        *           }
        */
        $app->delete('/personne/{id}', function(Request $request, Response $response) use ($app) {
            $resultat['error'] = FALSE;
            $resultat['message'] = "";

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];
            $id = $request->getAttribute('id');

            $db = new DbHandler();
            $res = $db->isPeopleOwner($id_current_user, $id); // Vérifie que l'utilisateur courant est le propriétaire
            if(!$res){
                $resultat['error'] = TRUE;
                $resultat['message'] = "400";
                $resultat["result"] = "Permission refusée pour votre identifiant ou id non trouvé !";
                return echoRespnse(400, $response, $resultat);
            }
                       
            // suppression de l'équipe en cascade avec ses enfants
            $res = $db->deleteByID('personnes', $id);
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



       /* Suppression d'un terrain 
        * url - /resp/tournoi/{id_tournament}/terrain/{id}
        * methode - DELETE
        * headears - content id_user and API_KEY
        * body - Json : -
        * return - {
        *            "error": false,
        *            "message": null,
        *            "nombre_suppression": 1,
        *            "id_supprimer": "17"
        *           }
        */
        $app->delete('/tournoi/{id_tournament}/terrain/{id}', function(Request $request, Response $response) use ($app) {
            $resultat['error'] = FALSE;
            $resultat['message'] = "";

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];
            $id = $request->getAttribute('id');
            $id_tournament = $request->getAttribute('id_tournament');

            $db = new DbHandler();
            $res = $db->isTournamentOwner($id_current_user, $id_tournament); // Vérifie que l'utilisateur courant est le propriétaire
            if(!$res){
                $resultat['error'] = TRUE;
                $resultat['message'] = "400";
                $resultat["result"] = "Permission refusée pour votre identifiant ou id non trouvé !";
                return echoRespnse(400, $response, $resultat);
            }
                       
            // suppression de l'équipe en cascade avec ses enfants
            $res = $db->deleteByID('terrains', $id);
            $data=NULL;
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $resultat["result"] = "Impossible de supprimer les données. S'il vous plaît essayer à nouveau";
                return echoRespnse(400, $response, $data);
            }     
             
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
        });


       /* Suppression des matchs (plannings, terrains et résultats)
        * url - /resp/tournoi/{id}/matchs
        * methode - DELETE
        * headears - content id_user and API_KEY
        * body - Json : -
        * return - {
        *            "error": false,
        *            "message": null,
        *            "result": ...
        *           }
        */
        $app->delete('/tournoi/{id}/matchs', function(Request $request, Response $response) use ($app) {
            $resultat['error'] = FALSE;
            $resultat['message'] = "";

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];
            $id = $request->getAttribute('id');

            $db = new DbHandler();
            $res = $db->isTournamentOwner($id_current_user, $id); // Vérifie que l'utilisateur courant est le propriétaire
            if(!$res){
                $resultat['error'] = TRUE;
                $resultat['message'] = "400";
                $resultat["result"] = "Permission refusée pour votre identifiant ou id non trouvé !";
                return echoRespnse(400, $response, $resultat);
            }
            // suppression des matchs en cascade avec ses enfants
            $res = $db->deleteMatchsByTournamentID($id);
            
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


       /* Suppression des terrains utilisés pendant un tournoi
        * url - /resp/tournoi/{id}/terrains
        * methode - DELETE
        * headears - content id_user and API_KEY
        * body - Json : -
        * return - {
        *            "error": false,
        *            "message": null,
        *            "result": ...
        *           }
        */
        $app->delete('/tournoi/{id}/terrains', function(Request $request, Response $response) use ($app) {
            $resultat['error'] = FALSE;
            $resultat['message'] = "";

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];
            $id = $request->getAttribute('id');

            $db = new DbHandler();
            $res = $db->isTournamentOwner($id_current_user, $id); // Vérifie que l'utilisateur courant est le propriétaire
            if(!$res){
                $resultat['error'] = TRUE;
                $resultat['message'] = "400";
                $resultat["result"] = "Permission refusée pour votre identifiant ou id non trouvé !";
                return echoRespnse(400, $response, $resultat);
            }
            // suppression des terrains
            $res=$db->deletePitchsByTournamentID($id);
            
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



       /* Suppression des sets d'un match
        * url - /resp/score/match/{id}
        * methode - DELETE
        * headears - content id_user and API_KEY
        * body - Json : -
        * return - {
        *            "error": false,
        *            "message": null,
        *            "nombre_suppression": 1,
        *            "id_supprimer": "17"
        *           }
        */
        $app->delete('/score/match/{id}', function(Request $request, Response $response) use ($app) {
            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];
            $id = $request->getAttribute('id');

            $db = new DbHandler();
            $res = $db->isMatchOwner($id_current_user, $id); // Vérifie que l'utilisateur courant est le propriétaire
            if(!$res){
                $resultat['error'] = TRUE;
                $resultat['message'] = "400";
                $resultat["result"] = "Permission refusée pour votre identifiant ou id non trouvé !";
                return echoRespnse(400, $response, $resultat);
            }
                       
            // suppression de l'équipe en cascade avec ses enfants
            $res = $db->deleteScoreByMatchID($id);
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


       /* Modifier les données d'un tournoi, uniquement le nom et le statut peuvent être changés 
        * url - /resp/tournoi/{id}
        * methode - PUT
        * headears - content id_user and API_KEY
        * body - Json : {"nom_tournoi":"2017-09-15 SVRN","id_statut":2}
        * return - {
        *            "error": false,
        *            "message": null,
        *            "id": 1,
        *           }
        */
        $app->put('/tournoi/{id}', function(Request $request, Response $response) use ($app) {
            require 'src/include/config.php';
            // récupère les données passée aux forma json
            $json = $request->getBody();
            $data = json_decode($json, true); // transofme en tableau associatif
            $id = $request->getAttribute('id');

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            $db = new DbHandler();
            $res = $db->isTournamentOwner($id_current_user, $id); // Vérifie que l'utilisateur courant est le propriétaire
            $role = $db->getRoleById($id_current_user); // ou alors on est admin
            if(!$res && $role['id_role'] != $config['role']['ADMIN']){
                $resultat['error'] = TRUE;
                $resultat['message'] = "400";
                $resultat["result"] = "Permission refusée pour votre identifiant ou id non trouvé !";
                return echoRespnse(400, $response, $resultat);
            }

            // filtre les champs qu'il faut mettre à jour
            $fieldsToCheck = array('nom_tournoi', 'id_statut', 'date_debut');
            $arrayFields = filterRequiredFields($data, $fieldsToCheck);


            //$res = $fieldsToCheck;
            $res = $db->updateByID('tournois', $arrayFields, $id);
            //return echoRespnse(400, $response, $arrayFields);

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

       /* Modifier les données d'un match, l'un ou plusieurs champs : date_match, heure, id_terrain, statut, id_user_dirige, id_equipe_arbitre
        * url - /resp/match/{id}
        * methode - PUT
        * headears - content id_user and API_KEY
        * body - Json : Ne mettre que les champ que l'en veut modifier
        *               {"date_match":"2017-09-28","heure":"18:00:00","id_terrain":"3", "statut":"1","id_user_dirige":"1","id_equipe_arbitre":"2" }
        * return - {
        *            "error": false,
        *            "message": null,
        *            "id": 1,
        *           }
        */
        $app->put('/match/{id}', function(Request $request, Response $response) use ($app) {
            // récupère les données passée aux forma json
            $json = $request->getBody();
            $data = json_decode($json, true); // transofme en tableau associatif
            $id = $request->getAttribute('id');

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            $db = new DbHandler();
            $res = $db->isTournamentOwner($id_current_user, $id); // Vérifie que l'utilisateur courant est le propriétaire
            if(!$res){
                $resultat['error'] = TRUE;
                $resultat['message'] = "400";
                $resultat["result"] = "Permission refusée pour votre identifiant ou id non trouvé !";
                return echoRespnse(400, $response, $resultat);
            }

            // filtre les champs qu'il faut mettre à jour
            $fieldsToCheck = array('date_match', 'heure', 'id_terrain', 'statut', 'id_user_dirige', 'id_equipe_arbitre');
            $arrayFields = filterRequiredFields($data, $fieldsToCheck);

            //$res = $fieldsToCheck;
            $res = $db->updateByID('matchs', $arrayFields, $id);
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

       /* Modifier les données d'une personne, l'un ou plusieurs champs : "prenom","nom","courriel","tel","tel_mobile","adresse","localite","Pays"
        * Il n'est pas possible de modifer l'id de l'équipe qui n'appartient pas à l'utilisateur courant.
        * url - /resp/personne/{id}
        * methode - PUT
        * headears - content id_user and API_KEY
        * body - Json : Ne mettre que les champ que l'en veut modifier
        *               {"prenom":"Nicole","nom":"Schnyder","courriel":"nicole.schnyder@vebb.com","tel":"+41 31 336 54 78","tel_mobile":"+41 78 123 45 67","adresse":"Feuille 12","localite":"Bienne","Pays":"Suisse","id_equipe":"3" }
        * return - {
        *            "error": false,
        *            "message": null,
        *            "id": 1,
        *           }
        */
        $app->put('/personne/{id}', function(Request $request, Response $response) use ($app) {
            // récupère les données passée aux forma json
            $json = $request->getBody();
            $data = json_decode($json, true); // transofme en tableau associatif
            $id = $request->getAttribute('id');

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            $db = new DbHandler();
            $res = $db->isPeopleOwner($id_current_user, $id); // Vérifie que l'utilisateur courant est le propriétaire
            if(!$res){
                $resultat['error'] = TRUE;
                $resultat['message'] = "Permission refusée pour votre identifiant, ou id non trouvé !";
                return echoRespnse(200, $response, $resultat);
            }

            // filtre les champs qu'il faut mettre à jour
            $fieldsToCheck = array("prenom","nom","courriel","tel","tel_mobile","adresse","localite","pays","id_user");
            $arrayFields = filterRequiredFields($data, $fieldsToCheck);
/*
            // vérifie que l'id_equipe corresponde à un équipe de propriété de l'utilisateur courant
            if(isset($data['id_equipe'])){
                $res = $db->isTeamOwner($id_current_user, $data['id_equipe']); // Vérifie que l'utilisateur courant est le propriétaire
                if(!$res){
                    $resultat['error'] = TRUE;
                    $resultat['message'] = "400";
                    $resultat["result"] = "Permission refusée pour l'id_equipe ne correspond pas à une de vos équipes !";
                    return echoRespnse(400, $response, $resultat);
                }
            }
*/
            //$res = $fieldsToCheck;
            $res = $db->updateByID('personnes', $arrayFields, $id);
            //return echoRespnse(400, $response, $res);
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



       /* Modifier les données d'une équipe, l'un ou plusieurs champs : "prenom","nom","courriel","tel","tel_mobile","adresse","localite","Pays"
        * Il n'est pas possible de modifer l'id de l'équipe qui n'appartient pas à l'utilisateur courant.
        * url - /resp/equipe/{id}
        * methode - PUT
        * headears - content id_user and API_KEY
        * body - Json : Ne mettre que les champ que l'en veut modifier
        *               {"prenom":"Nicole","nom":"Schnyder","courriel":"nicole.schnyder@vebb.com","tel":"+41 31 336 54 78","tel_mobile":"+41 78 123 45 67","adresse":"Feuille 12","localite":"Bienne","Pays":"Suisse","id_equipe":"3" }
        * return - {
        *            "error": false,
        *            "message": null,
        *            "id": 1,
        *           }
        */
        $app->put('/equipe/{id}', function(Request $request, Response $response) use ($app) {
            // récupère les données passée aux forma json
            $json = $request->getBody();
            $data = json_decode($json, true); // transofme en tableau associatif
            $id = $request->getAttribute('id');

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            $db = new DbHandler();
            $res = $db->isTeamOwner($id_current_user, $id); // Vérifie que l'utilisateur courant est le propriétaire
            if(!$res){
                $resultat['error'] = TRUE;
                $resultat['message'] = "Permission refusée pour votre identifiant, ou id non trouvé !";
                return echoRespnse(200, $response, $resultat);
            }

            // filtre les champs qu'il faut mettre à jour
            $fieldsToCheck = array("nom_equipe", "nb_pts", "niveau", "id_groupe", "id_personne");
            $arrayFields = filterRequiredFields($data, $fieldsToCheck);

            //$res = $fieldsToCheck;
            $res = $db->updateByID('equipes', $arrayFields, $id);
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

       /* Modifier les données d'une terrain, l'un ou plusieurs champs : "nom_terrain"
        * url - /resp/terrain/{id}
        * methode - PUT
        * headears - content id_user and API_KEY
        * body - Json : Ne mettre que les champ que l'en veut modifier
        *               {"nom_terrain":"Terrain OUEST" }
        * return - {
        *            "error": false,
        *            "message": null,
        *            "id": 1,
        *           }
        */
        $app->put('/terrain/{id}', function(Request $request, Response $response) use ($app) {
            // récupère les données passée aux forma json
            $json = $request->getBody();
            $data = json_decode($json, true); // transofme en tableau associatif
            $id = $request->getAttribute('id');

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            $db = new DbHandler();
            //$res = $db->isPitchOwner($id_current_user, $id); // Vérifie que l'utilisateur courant est le propriétaire
            // if(!$res){
            //     $resultat['error'] = TRUE;
            //     $resultat['message'] = "400";
            //     $resultat["result"] = "Permission refusée pour votre identifiant, ou id non trouvé !";
            //     return echoRespnse(400, $response, $resultat);
            // }

            // filtre les champs qu'il faut mettre à jour
            $fieldsToCheck = array("nom_terrain");
            $arrayFields = filterRequiredFields($data, $fieldsToCheck);

            $res = $db->updateByID('terrains', $arrayFields, $id);
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

        /* Modifier les données d'un groupe, l'un ou plusieurs champs : "nom_groupe", "id_tournoi"
        * url - /resp/groupe/{id}
        * methode - PUT
        * headears - content id_user and API_KEY
        * body - Json : Ne mettre que les champ que l'en veut modifier
        *               {"nom_groupe":"GrA","id_tournoi":"1"}
        * return - {
        *            "error": false,
        *            "message": null,
        *            "id": 1,
        *           }
        */
        $app->put('/groupe/{id}', function(Request $request, Response $response) use ($app) {
            // récupère les données passée aux forma json
            $json = $request->getBody();
            $data = json_decode($json, true); // transofme en tableau associatif
            $id = $request->getAttribute('id');

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

            $db = new DbHandler();
            $res = $db->isGroupOwner($id_current_user, $id); // Vérifie que l'utilisateur courant est le propriétaire
            if(!$res){
                $resultat['error'] = TRUE;
                $resultat['message'] = "400";
                $resultat["result"] = "Permission refusée pour votre identifiant, ou id non trouvé !";
                return echoRespnse(400, $response, $resultat);
            }
            
            if(isset($data['id_tournoi'])){
                // Vérifie que le tournoi auquel on veut affecter le groupe lui appartient
                $res = $db->isTournamentOwner($id_current_user, $data['id_tournoi']); 
                if(!$res){
                    $resultat['error'] = TRUE;
                    $resultat['message'] = "400";
                    $resultat["result"] = "Veuillez attribuer un tournoi vous appartenant";
                    return echoRespnse(400, $response, $resultat);
                }
            }


            // filtre les champs qu'il faut mettre à jour
            $fieldsToCheck = array("nom_groupe", "id_tournoi");
            $arrayFields = filterRequiredFields($data, $fieldsToCheck);
            $res = $db->updateByID('groupes', $arrayFields, $id);
            return echoRespnse(200, $response, $res);

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


        /* Modifier les données de son account personnel
        * url - /resp/account
        * methode - PUT
        * headears - content id_user and API_KEY
        * body - Json : Ne mettre que les champ que l'en veut modifier
        *               {"nom_groupe":"GrA","id_tournoi":"1"}
        * return - {
        *            "error": false,
        *            "message": null,
        *            "id": 1,
        *           }
        */
        $app->put('/account', function(Request $request, Response $response) use ($app) {
            // récupère les données passée aux forma json
            $json = $request->getBody();
            $data = json_decode($json, true); // transofme en tableau associatif

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];

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
            $res = $db->updateByID('users', $arrayFields, $id_current_user);
            $data=NULL;
            if ($res != NULL) {
                $data["error"] = false;
                $data["message"] = "200";
                $data["result"] = $res;
            } else {
                $data["error"] = true;
                $data["message"] = "400";
                $data["result"] = "Impossible de mettre à jour les données. S'il vous plaît essayer à nouveau !";
                return echoRespnse(400, $response, $data);
            }  

            // echo de la réponse  JSON
            return echoRespnse(200, $response, $data);
        });