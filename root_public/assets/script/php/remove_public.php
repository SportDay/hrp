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

    $connexion = makeConnection();

    ///////////////////////////////////////////////////////////////////////////

    removePublicPage();

    ///////////////////////////////////////////////////////////////////////////

    echo json_encode([
        "success" => true,
        "error"   => ""
    ]);

    unset($_SESSION["remove_public"]);
    mysqli_close($connexion);
    exit();
?>