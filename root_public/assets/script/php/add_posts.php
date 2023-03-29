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

if ((!isset($_POST["post_content"])) || $_POST["post_content"] === "" || strlen($_POST["post_content"]) > 735) {
    echo json_encode([
        "success" => false,
        "error"   => "Requête incorrecte."
    ]); exit();
}

$connexion = makeConnection();

////////////////////////////////////////////////////////////////////

if(isset($_SESSION["banned_to"]) && $_SESSION["banned_to"] > time()){
    echo json_encode([
        "success" => false,
        "error"   => "Vous etes bannis."
    ]); exit();
}

$connexion->query(
    "INSERT INTO `posts` (`user_id`, `public_image`, `public_name`, `creation_date`, `content`) VALUES " .
    "(\"" . $connexion->real_escape_string($_SESSION["id"]) . "\", \"" . $connexion->real_escape_string($_SESSION["public_image"]) . "\", \"" . $connexion->real_escape_string($_SESSION["public_name"]) . "\", \"" . $connexion->real_escape_string(time()) . "\", \"" . $connexion->real_escape_string($_POST["post_content"]) . "\");"
);


////////////////////////////////////////////////////////////////////

echo json_encode([
    "success" => true
]);

mysqli_close($connexion);
exit();
?>