<?php

    $global_params = [
        "root"        => "../../../../",
        "root_public" => "../../../../root_public/",
    ];

    require($global_params["root"] . "assets/script/php/constants.php");
    require($global_params["root"] . "assets/script/php/functions.php");
    
    ////////////////////////////////////////////////////////////////////
    // ETABLISSEMENT DE LA CONNECTION

    session_start();
    verifyToken();

    if (!isset($_POST["username"])) {
        echo json_encode([
            "success" => false,
            "error"   => "Requête incorrecte."
        ]); exit();
    }

    $username = $_POST["username"];

    $connexion = makeConnection();

    ///////////////////////////////////////////////////////////////////////////

    // recupe l'id
    $id = $connexion->query(
        "SELECT id FROM users WHERE username=\"" . $connexion->real_escape_string($username) . "\""
    );

    if ($id->num_rows == 0) { 
        // data base error
        echo json_encode([
            "success" => false,
            "error"   => "Cet utilisateur n'existe pas."
        ]); exit(); 
    }

    $id = $id->fetch_assoc()["id"];

    if (isset($_SESSION["id"]) && $id == $_SESSION["id"]) {
        echo json_encode([
        "success" => false,
        "error"   => "Vous pouvez pas vous demander en ami."
        ]); exit();
    }

    // verifier que l'id n'est pas en amis
    if ($connexion->query("SELECT id FROM friends " . 
            "WHERE (user_id_0=".$id.            " AND user_id_1=".$_SESSION["id"].") ".
            "OR    (user_id_0=".$_SESSION["id"]." AND user_id_1=".$id.")"
            )->num_rows != 0
    ) { 
        // data base error
        echo json_encode([
            "success" => false,
            "error"   => "Demande déjà envoyé / cet utilisateur vous a déjà demandé."
        ]); exit(); 
    }

    // ajouter en amis
    $connexion->query("INSERT INTO friends (user_id_0, user_id_1) VALUES (".$_SESSION["id"].", ".$id.")");



    ///////////////////////////////////////////////////////////////////////////

    echo json_encode([
        "success" => true,
        "error"   => "Demande envoyée."
    ]);

    mysqli_close($connexion);
    exit();
?>