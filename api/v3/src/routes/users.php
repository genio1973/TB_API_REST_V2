<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


// Get All users
$app->get('/users', function (Request $request, Response $response) {
    $sql = "SELECT * FROM user";

    try{
        // Get DB
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->query($sql);
        $users = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($users);

    } catch(PDOException $e){
        echo '{"error":{"texte": '.$e->getMessage().'}';
    }

});

// Get one user
$app->get('/user/{id}', function (Request $request, Response $response) {

    $id = $request->getAttribute('id');

    $sql = "SELECT id, email, id_role FROM user WHERE id = $id";

    try{
        // Get DB
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->query($sql);
        $user = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;
        if($user){
             echo json_encode($user);
        }
        else{
             echo '{"error":{"texte": false}';
        }

    } catch(PDOException $e){
        echo '{"error":{"texte": '.$e->getMessage().'}';
    }

});

// Add user
$app->post('/user/add', function (Request $request, Response $response) {

    $email = $request->getParam('email');
    $mot_de_passe = $request->getParam('mot_de_passe');
    $id_role = $request->getParam('id_role');

    $sql = "INSERT INTO user (email, mot_de_passe, id_role) VALUES
             (:email,:mot_de_passe,:id_role)";

    try{
        // Get DB
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mot_de_passe', $mot_de_passe);
        $stmt->bindParam(':id_role', $id_droit);

        $stmt->execute();

        echo '{"notice": "User ajouté"}';
        

    } catch(PDOException $e){
        echo '{"error":{"texte": '.$e->getMessage().'}';
    }

});


// Update user
$app->put('/user/update/{id}', function (Request $request, Response $response) {

    $id = $request->getAttribute('id');

    $email = $request->getParam('email');
    $mot_de_passe = $request->getParam('mot_de_passe');
    $id_droit = $request->getParam('id_role');

    $sql = "UPDATE user SET
        email = :email,
        mot_de_passe = :mot_de_passe,
        id_role = :id_role
        WHERE id=$id";

    try{
        // Get DB Objecgt
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mot_de_passe', $mot_de_passe);
        $stmt->bindParam(':id_role', $id_role);

        $stmt->execute();

        echo '{"notice": "User mis à jour"}';
        

    } catch(PDOException $e){
        echo '{"error":{"texte": '.$e->getMessage().'}';
    }

});


// Delete  user
$app->delete('/user/delete/{id}', function (Request $request, Response $response) {

    $id = $request->getAttribute('id');

    $sql = "DELETE FROM user WHERE id = $id";

    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo '{"notice": "User supprimé"}';
    } catch(PDOException $e){
        echo '{"error":{"texte": '.$e->getMessage().'}';
    }

});