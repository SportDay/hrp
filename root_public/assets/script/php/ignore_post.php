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

if (!isset($_SESSION["admin"]) || !$_SESSION["admin"]) // intrusion
    exit();

if (!isset($_POST["post_id"])) {
    echo json_encode([
        "success" => false,
        "error"   => "Requête incorrecte."
    ]); exit();
}

$connexion = makeConnection();

////////////////////////////////////////////////////////////////////

$connexion->query(
    "UPDATE posts set reportnum=\"0\" WHERE id=\"" . $connexion->real_escape_string($_POST["post_id"]) . "\";"
);

$connexion->query(
    "DELETE FROM reports WHERE (message_id=\"".$_POST["post_id"]."\");"
);

////////////////////////////////////////////////////////////////////

echo json_encode([
    "success" => true
]);

mysqli_close($connexion);
exit();
?>