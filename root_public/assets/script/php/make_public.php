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

    if ( $connexion->query( "SELECT * FROM users WHERE id=\"". $_SESSION["id"] . "\";")
        ->fetch_assoc() ["last_reroll"] + $TIME_REROLL > time()
    ) {
        echo json_encode([
            "success" => false,
            "error"   => "Vous ne pouvez reroll qu'une seul fois toute les 24 heures."
        ]); exit();
    }

    // generation d'un nouvelle
    $public_page = generateRandomPublicData();
    if ($public_page["success"] === FALSE)
    {
        echo json_encode([
            "success" => false,
            "error"   => "Une est survenue dans la génération des données, veuillez réessayer."
        ]); exit();
    }

    // Suppression de l'ancienne page
    if ($_SESSION["enable_public"])
        removePublicPage();

    // application de la nouvelle
    $_SESSION["enable_public"] = true;
    $_SESSION["public_name"  ] = $public_page["public_name" ];
    $_SESSION["public_image" ] = $public_page["public_image"];
    $connexion->query(
        "UPDATE `users` SET " . 
        "`enable_public`=TRUE, " .
        "`public_name`=\""  . $connexion->real_escape_string($public_page["public_name"]) . "\", " .
        "`public_image`=" . $public_page["public_image"] . ", " .
        "`last_reroll`="  . time() . ", " .
        "`description`=\""  . $connexion->real_escape_string(inspirate($public_page["class"])) . "\", " .

        "`specie`=\"" . $connexion->real_escape_string($public_page["specie"]) . "\", " .
        "`class`=\""  . $connexion->real_escape_string($public_page["class"])  . "\", " .
        "`title`=\""  . $connexion->real_escape_string($public_page["title"])  . "\" " .

        " WHERE `id`=" . $_SESSION["id"] . " ;"
    );

    ////////////////////////////////////////////////////////////////////

    echo json_encode([
        "success"     => true,
        "public_name" => htmlentities($_SESSION["public_name"]) 
    ]);
    
    unset($_SESSION["make_public"]);
    mysqli_close($connexion);
    exit();
?>