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

    $connexion = makeConnection();

    ///////////////////////////////////////////////////////////////////////////

    $id = $connexion->query(
        "SELECT id FROM users WHERE username=\"" . $connexion->real_escape_string($username) . "\""
    );

    if ($id->num_rows = 0) { 
        // data base error
        echo json_encode([
            "success" => false,
            "error"   => "Cet utilisateur n'existe pas."
        ]); exit(); 
    }
    
    $id = $id->fetch_assoc()["id"];

    $connexion->query(
        "DELETE friends " .
        "WHERE (user_id_0=".$id." AND user_id_1=".$_SESSION["id"].") OR (".$_SESSION["id"]." AND".$id.")"
    );

    ///////////////////////////////////////////////////////////////////////////

    echo json_encode([
        "success" => true,
        "error"   => ""
    ]);

    mysqli_close($connexion);
    exit();
?>