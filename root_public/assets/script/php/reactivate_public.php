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

    $connexion = makeConnection();

    ////////////////////////////////////////////////////////////////////

    if ( $_SESSION["banned"] ) {
        echo json_encode([
            "success" => false,
            "error"   => "Vous êtes encore bannis."
        ]); exit();
    }

    if ( !$_SESSION["memory_public"] && !$_SESSION["enable_public"] ) {
        echo json_encode([
            "success" => false,
            "error"   => "Vous n'avez pas de page à reactiver."
        ]); exit();
    }

    ///////////////////////////////////////////////////////////////////

    $public_page = $connexion->query(
        "SELECT * FROM users WHERE id=\"". $_SESSION["id"] . "\";"
        )->fetch_assoc();

    $_SESSION["enable_public"] = true;

    $_SESSION["public_name"  ] = $public_page["public_name" ];
    $_SESSION["public_image" ] = $public_page["public_image"];

    $connexion->query(
        "UPDATE `users` SET " . 
        "`enable_public`=TRUE, " .
        "`memory_public`=FALSE "  . 
        " WHERE `id`=" . $_SESSION["id"] . " ;"
    );

    ////////////////////////////////////////////////////////////////////

    echo json_encode([
        "success"     => true,
        "public_name" => htmlentities($_SESSION["public_name"]) 
    ]);

    mysqli_close($connexion);
    unset($_SESSION["reactivate_public"]);
    exit();
?>