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

    if (
        !isset($_POST["private"]) || !isset($_POST["last"]) || !isset($_POST["friend"]) || is_int($_POST["last"])
    )
    {
        echo json_encode([
            "success" => false,
            "error"   => "Requète invalide"
        ]); exit();
    }

    $private     = $_POST["private"] == "true";
    $last        = $_POST["last"];
    $friend      = $_POST["friend"];

    $connexion = makeConnection();

    ////////////////////////////////////////////////////////////////////

    // check up de sécurité (on cherche l'id de l'amis demandé)
    $friend = $private ?
        $connexion->query(
            "SELECT id FROM users WHERE username=\"".$connexion->real_escape_string($friend)."\";"
        ) :
        $connexion->query(
            "SELECT id FROM users WHERE public_name=\"".$connexion->real_escape_string($friend)."\";"
        ) ;

    if ($friend->num_rows == 0) { 
        echo json_encode([
            "success" => false,
            "error"   => "Incorrect user."
        ]); exit(); 
    }

    $friend = $friend->fetch_assoc();

    if ( // verifie que les 2 sont bien amis/match
            $private ?
            $connexion->query(
                "
                (SELECT user_id_1 as friend FROM `friends` WHERE (user_id_0=".$_SESSION["id"]." AND user_id_1=".$friend["id"]." AND accepted))
                    UNION 
                (SELECT user_id_0 as friend FROM `friends` WHERE (user_id_1=".$_SESSION["id"]." AND user_id_0=".$friend["id"]." AND accepted))
                "           
            )->num_rows == 0 :
            $connexion->query(
                "
                (SELECT id FROM `pages_liked` WHERE (user_id=".$_SESSION["id"]." AND like_id=".$friend["id"]." ))
                    UNION 
                (SELECT id FROM `pages_liked` WHERE (like_id=".$_SESSION["id"]." AND user_id=".$friend["id"]." ))
                "
            )->num_rows != 2
    ) {
        echo json_encode([
            "success" => false,
            "error"   => "Incorrect user."
        ]); exit(); 
    }


    // on recherche tout les messages entre les 2
    $new_messages = $connexion->query(
        "SELECT t1.creation_date, t1.content, username, public_name " .
        "FROM ( ".
            " SELECT from_id, creation_date, content FROM `direct_messages` WHERE ".
            "(" .
                "(from_id=".$friend["id"]." AND to_id=".$_SESSION["id"].") OR (from_id=".$_SESSION["id"]." AND to_id=".$friend["id"].")".
            ") AND creation_date>".$last." AND private=".($private ? "true":"false"). " ".
        ") as t1 ".
        "inner join users ".
        "on t1.from_id=users.id ".

        "ORDER BY t1.creation_date DESC ".
        "LIMIT 21 " 
        // On prends pas les messages trop vieux
        // D'ailleurs on pourrait aussi supprimer les trop vieux du coup
    );

    $newLast = time();
    $html = "";

    while ($message = $new_messages->fetch_assoc()) {
        ob_start();
        ?>

            <div class="message_container">
                <p class="date"><?= htmlentities($private ? $message['username'] : $message['public_name']) ?><br><span style="font-size: 12px;"><?= htmlentities(date('H:i d/m/Y', $message['creation_date'])) ?></span></p>
                <p class="message"><?=newline_for_html(htmlentities($message["content"]))?></p>
            </div>

        <?php
        $html = ob_get_clean() . $html;
    }

    ////////////////////////////////////////////////////////////////////

    echo json_encode([
        "success"     => true,
        "html"        => $html,
        "last"        => $newLast
    ]);
    
    mysqli_close($connexion);
    exit();
?>