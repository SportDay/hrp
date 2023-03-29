<?php

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

    if (!isset($_POST["username"])) {
        echo json_encode([
            "success" => false,
            "error"   => "Requête incorrecte."
        ]); exit();
    }

    $connexion = makeConnection();

    ////////////////////////////////////////////////////////////////////

    $fid = $connexion->query(
        "SELECT id FROM users WHERE username=\"" . $connexion->real_escape_string($_POST["username"]) . "\""
    )->fetch_assoc()["id"];

    $connexion->query(
        "DELETE FROM friends WHERE (user_id_0=".$_SESSION["id"]." AND user_id_1=".$fid.") OR (user_id_0=".$fid." AND user_id_1=".$_SESSION["id"].")"
    );

    ////////////////////////////////////////////////////////////////////

    echo json_encode([
        "success" => true 
    ]);

    mysqli_close($connexion);
    exit();
?>