<?php

    // ATTENTION
    // LE FICHIER QUI ACTIONNE CELUI CI SE TROUVE DANS : root_public/assets/script/php/
    $global_params = [
        "root"        => "../../../../",
        "root_public" => "../../../../root_public/",
    ];

    require($global_params["root"] . "assets/script/php/constants.php");
    require($global_params["root"] . "assets/script/php/functions.php");
    require($global_params["root"] . "assets/script/php/security.php");
    
    ////////////////////////////////////////////////////////////////////
    // ETABLISSEMENT DE LA CONNECTION

    session_start();
    verifyToken();

    if (
        !isset($_POST["username"]) || 
        !isset($_POST["password"]) || 
        !isset($_POST["password2"])
        )
    {
        echo json_encode([
            "success" => false,
            "error"   => "Requête incorrecte."
        ]); exit();
    }

    $username  = $_POST["username"];
    $password  = $_POST["password"];
    $password2 = $_POST["password2"];

    $connexion = makeConnection();

    ////////////////////////////////////////////////////////////////////
    // CHECKUP DE SECURITEE

    if ($password != $password2) {
        echo json_encode([
            "success" => false,
            "error"   => "Les deux mots de passent ne correspondent pas."
        ]); exit();
    }

    $result = $connexion->query( 
        "SELECT * FROM users WHERE id=\"". $connexion->real_escape_string($_SESSION["id"]) . "\";" 
        )->fetch_assoc();

    if (!password_verify($password, $result["password"]) || trim($username) != $_SESSION["username"]) {
        echo json_encode([
            "success" => false,
            "error"   => "Entrées incorrectes."
        ]); exit(); 
    }

    ///////////////////////////////////////////////////////////////////
    // SUPPRESSION DU COMPTE

    removeAccount();

    ///////////////////////////////////////////////////////////////////

    echo json_encode([
        "success" => true,
        "error"   => ""
    ]); exit(); 

    session_unset();
    session_destroy();
    mysqli_close($connexion);
    exit();
?>