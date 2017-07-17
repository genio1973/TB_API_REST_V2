<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require 'src/include/config.php';

/************************************************************************************
API responsable
Routes par défauts : vx/resp/route
*************************************************************************************/

        /* Liste des tournois créés par l'utilisateur en cours, selon son id dans son entête
        * url - /resp/tournaments
        * headears - content id_user and API_KEY
        * methode - GET
        */
        $app->get('/tournaments', function (Request $request, Response $response)  {
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
        $app->get('/tournament/{id_tournoi}/equipes', function (Request $request, Response $response) {
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
        $app->get('/groupe/{id_groupe}/equipes', function (Request $request, Response $response) {
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
        $app->get('/tournament/{id_tournoi}/equipes/groupe/{id_groupe}', function (Request $request, Response $response) {
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
        $app->post('/tournoi', function(Request $request, Response $response) use ($app) {
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
        $app->post('/personnes', function(Request $request, Response $response) use ($app) {
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
        $app->post('/equipes', function(Request $request, Response $response) use ($app) {
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
        $app->post('/groupes', function(Request $request, Response $response) use ($app) {
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

        $app->post('/terrains', function(Request $request, Response $response) use ($app) {
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
        $app->post('/matchs', function(Request $request, Response $response) use ($app) {
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
        $app->post('/sets', function(Request $request, Response $response) use ($app) {
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
        $app->delete('/tournoi/{id}', function(Request $request, Response $response) use ($app) {
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
        $app->delete('/equipe/{id}', function(Request $request, Response $response) use ($app) {
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
        $app->delete('/groupe/{id}', function(Request $request, Response $response) use ($app) {
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

       /* Suppression d'un match 
        * url - /resp/match/{id}
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
        $app->delete('/match/{id}', function(Request $request, Response $response) use ($app) {
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
            $res = $db->deleteByID('matchs', $id);
            
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $res);
        });


       /* Suppression d'une personne 
        * url - /resp/personne/{id}
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
        $app->delete('/personne/{id}', function(Request $request, Response $response) use ($app) {
            $resultat['error'] = FALSE;
            $resultat['error_mgs'] = "";

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];
            $id = $request->getAttribute('id');

            $db = new DbHandler();
            $res = $db->isPeopleOwner($id_current_user, $id); // Vérifie que l'utilisateur courant est le propriétaire
            if(!$res){
                $resultat['error'] = TRUE;
                $resultat['error_mgs'] = "Permission refusée pour votre identifiant ou id non trouvé !";

                return echoRespnse(201, $response, $resultat);
            }
                       
            // suppression de l'équipe en cascade avec ses enfants
            $res = $db->deleteByID('personnes', $id);
            
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $res);
        });



       /* Suppression d'un terrain 
        * url - /resp/tournament/{id_tournament}/terrain/{id}
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
        $app->delete('/tournament/{id_tournament}/terrain/{id}', function(Request $request, Response $response) use ($app) {
            $resultat['error'] = FALSE;
            $resultat['error_mgs'] = "";

            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];
            $id = $request->getAttribute('id');
            $id_tournament = $request->getAttribute('id_tournament');

            $db = new DbHandler();
            $res = $db->isTournamentOwner($id_current_user, $id_tournament); // Vérifie que l'utilisateur courant est le propriétaire
            if(!$res){
                $resultat['error'] = TRUE;
                $resultat['error_mgs'] = "Permission refusée pour votre identifiant ou id non trouvé !";

                return echoRespnse(201, $response, $resultat);
            }
                       
            // suppression de l'équipe en cascade avec ses enfants
            $res = $db->deleteByID('terrains', $id);
            
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $res);
        });


       /* Suppression des sets d'un match
        * url - /resp/score/match/{id}
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
        $app->delete('/score/match/{id}', function(Request $request, Response $response) use ($app) {
            // récupère l'id du responsable en cours
            $headers = $request->getHeaders();
            $id_current_user = $headers['HTTP_USERID'][0];
            $id = $request->getAttribute('id');

            $db = new DbHandler();
            $res = $db->isMatchOwner($id_current_user, $id); // Vérifie que l'utilisateur courant est le propriétaire
            if(!$res){
                $resultat['error'] = TRUE;
                $resultat['error_mgs'] = "Permission refusée pour votre identifiant ou id non trouvé !";

                return echoRespnse(200, $response, $resultat);
            }
                       
            // suppression de l'équipe en cascade avec ses enfants
            $res = $db->deleteScoreByMatchID($id);
            
            // echo de la réponse  JSON
            return echoRespnse(200, $response, $res);
        });


