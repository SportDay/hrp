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

    !isset($_POST["private"]) || !isset($_POST["friend"]) || !isset($_POST["message"]) || !strlen(trim($_POST["message"]))

)
{
    echo json_encode([
        "success" => false,
        "error"   => "Requète invalide"
    ]); exit();
}

$private     = $_POST["private"] == "true";
$friend      = $_POST["friend"];
$message     = trim($_POST["message"]);


$connexion = makeConnection();

////////////////////////////////////////////////////////////////////

// check up de sécurité (on cherche l'id de l'amis demandé)
$friend = $private ?
$connexion->query(
    "SELECT id FROM users WHERE username=\""   .$connexion->real_escape_string($friend)."\";"
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

// send
$connexion->query(
    "INSERT INTO direct_messages (from_id, to_id, content, private) VALUES " .
    "(".    $_SESSION["id"] .", ".
            $friend["id"]   .", ".
    "\"".$connexion->real_escape_string($message)."\",".
    " ". ($private ? "TRUE" : "FALSE") . ")" 
);

////////////////////////////////////////////////////////////////////

echo json_encode([
    "success"     => true,
]);

mysqli_close($connexion);
exit();
?>