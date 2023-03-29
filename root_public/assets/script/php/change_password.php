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

    if (
        !isset($_POST["old_password"]) || 
        !isset($_POST["new_password"]) || 
        !isset($_POST["new_password2"])
        )
    {
        echo json_encode([
            "success" => false,
            "error"   => "Requête incorrecte."
        ]); exit();
    }

    $old_password  = $_POST["old_password"];
    $new_password  = $_POST["new_password"];
    $new_password2 = $_POST["new_password2"];

    $connexion = makeConnection();

    ////////////////////////////////////////////////////////////////////
    // CHECKUP DE SECURITEE

    if ($new_password != $new_password2) {
        echo json_encode([
            "success" => false,
            "error"   => "Les deux mots de passent ne correspondent pas."
        ]); exit();
    }

    if (!isValidePassword($new_password)) {
        echo json_encode([
            "success" => false,
            "error"   => "Problème de formattage."
        ]); exit();
    }


    $result = $connexion->query( 
        "SELECT * FROM users WHERE id=\"". $connexion->real_escape_string($_SESSION["id"]) . "\";" 
        )->fetch_assoc();

    if (!password_verify($old_password, $result["password"])) {
        echo json_encode([
            "success" => false,
            "error"   => "Entrées incorrectes."
        ]); exit(); 
    }

    ////////////////////////////////////////////////////////////////////
    // CHANGEMENT MOT DE PASSE

    $connexion->query(
            "UPDATE users SET " . 
            "password=\""   . $connexion->real_escape_string(password_hash($new_password, PASSWORD_DEFAULT)) . "\" " .
            "WHERE `id`=\"" . $result["id"] . "\" ;"
        );

    echo json_encode([
        "success" => true,
        "error"   => ""
    ]);

    mysqli_close($connexion);
    exit();
?>