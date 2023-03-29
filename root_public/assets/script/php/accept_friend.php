<?php

    $global_params = [
        "root"        => "../../../../",
        "root_public" => "../../../../root_public/",
    ];

    require($global_params["root"] . "assets/script/php/constants.php");
    require($global_params["root"] . "assets/script/php/functions.php");
    require($global_params["root"] . "assets/script/php/modules.php");
    
    ////////////////////////////////////////////////////////////////////
    // ETABLISSEMENT DE LA CONNECTION

    session_start();
    verifyToken();

    if (!isset($_POST["username"]) || !isset($_POST["from_root"])) {
        echo json_encode([
            "success" => false,
            "error"   => "Requête incorrecte."
        ]); exit();
    }

    $username = $_POST["username"];
    $from_root = $_POST["from_root"];

    $connexion = makeConnection();

    ///////////////////////////////////////////////////////////////////////////

    $friend = $connexion->query(
        "SELECT * FROM users WHERE username=\"" . $connexion->real_escape_string($username) . "\""
    );

    if ($friend->num_rows == 0) { 
        // data base error
        echo json_encode([
            "success" => false,
            "error"   => "Cet utilisateur n'existe pas."
        ]); exit(); 
    }
    
    $id = ($friend=$friend->fetch_assoc())["id"];

    $connexion->query(
        "UPDATE friends SET " .
        "accepted=1 " .
        "WHERE (user_id_0=".$id.            " AND user_id_1=".$_SESSION["id"].") ".
        "OR    (user_id_0=".$_SESSION["id"]." AND user_id_1=".$id.")"
    );

    ///////////////////////////////////////////////////////////////////////////
    // SEND HTML of friend

    ob_start();
    
    friend_bloc($friend, true, $from_root);

    $html = ob_get_clean();
    //$html = htmlentities(stripslashes(utf8_encode($html)), ENT_QUOTES);

    ////////////////////////////

    echo json_encode([
        "success"   => true,
        "error"     => "",
        "html"      => $html
    ]);

    mysqli_close($connexion);
    exit();
?>