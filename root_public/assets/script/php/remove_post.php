<?php

$global_params = [
    "root"        => "../../../../",
    "root_public" => "../../../../root_public/",
];

require_once($global_params["root"] . "assets/script/php/constants.php");
require_once($global_params["root"] . "assets/script/php/functions.php");

////////////////////////////////////////////////////////////////////
// ETABLISSEMENT DE LA CONNECTION

session_start();
verifyToken();

if (!isset($_POST["post_id"])) {
    echo json_encode([
        "success" => false,
        "error"   => "Requête incorrecte."
    ]); exit();
}

//// SQL
$connexion = makeConnection();

////////////////////////////////////////////////////////////////////

$post = $connexion->query(
    "SELECT * FROM posts WHERE id=\"" . $connexion->real_escape_string($_POST["post_id"]) . "\";"
)->fetch_assoc();

if($post["user_id"] !== $_SESSION["id"] && !(isset($_SESSION["admin"]) && $_SESSION["admin"])){
    echo json_encode([
        "success" => false,
        "error"   => "Base de donnée hors d'accès."
    ]); exit();
}


// remove the post
$connexion->query( // avec les foreign en cascade ça peut être supprimé normalement
    "DELETE FROM `reports` WHERE `message_id`=" . $post["id"] . " ;"
);
$connexion->query( // avec les foreign en cascade ça peut être supprimé normalement
    "DELETE FROM `likes` WHERE `message_id`=" . $post["id"] . " ;"
);
$connexion->query(
    "DELETE FROM posts WHERE (id=".$post["id"].");"
);

////////////////////////////////////////////////////////////////////

echo json_encode([
    "success" => true
]);

mysqli_close($connexion);
exit();
?>