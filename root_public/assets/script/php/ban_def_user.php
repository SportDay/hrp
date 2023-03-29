<?php

$global_params = [
    "root"        => "../../../../",
    "root_public" => "../../../../root_public/",
];

require_once($global_params["root"] . "assets/script/php/constants.php");
require_once($global_params["root"] . "assets/script/php/functions.php");
require_once($global_params["root"] . "assets/script/php/security.php");

////////////////////////////////////////////////////////////////////
// ETABLISSEMENT DE LA CONNECTION

session_start();
verifyToken();

if (!isset($_SESSION["admin"]) || !$_SESSION["admin"]) // intrusion
    exit();

if (!isset($_POST["user_id"])) {
    echo json_encode([
        "success" => false,
        "error"   => "Requête incorrecte."
    ]); exit();
}

$connexion = makeConnection();

////////////////////////////////////////////////////////////////////

$id = $connexion->query("SELECT id FROM users WHERE id=\"".$connexion->real_escape_string($_POST["user_id"])."\"");

if ($id->num_rows == 0) {
    echo json_encode([
        "success" => false,
        "error"   => "Utilisateur incorrect."
    ]); exit();
}

removeAccount(false, $id->fetch_assoc()["id"]);

////////////////////////////////////////////////////////////////////

echo json_encode([
    "success" => true
]);

mysqli_close($connexion);
exit();
?>