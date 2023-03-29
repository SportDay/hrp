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

if (!isset($_POST["type"]) || !isset($_POST["user_id"])) {
    echo json_encode([
        "success" => false,
        "error"   => "Requête incorrecte."
    ]); exit();
}

if (!isset($_POST["time"])) {
    echo json_encode([
        "success" => false,
        "error"   => "Le champs ne peut etre vide."
    ]); exit();
}

$connexion = makeConnection();

////////////////////////////////////////////////////////////////////

if($_POST["type"] === "min"){
    $ban_to = $_POST["time"]*60;
}
if($_POST["type"] === "hour"){
    $ban_to = $_POST["time"]*3600;
}
if($_POST["type"] === "day"){
    $ban_to = $_POST["time"]*86400;
}
if($_POST["type"] === "month"){
    $ban_to = $_POST["time"]*2628000;
}

$id = $connexion->query("SELECT id FROM users WHERE id=\"".$connexion->real_escape_string($_POST["user_id"])."\"");
if ($id->num_rows == 0) {
    echo json_encode([
        "success" => false,
        "error"   => "Utilisateur incorrect."
    ]); exit();
} $id = $id->fetch_assoc()["id"];

removePublicPage(false, $id);

$connexion->query(
    "UPDATE users set banned_to =\"". $connexion->real_escape_string(time()+$ban_to) . "\" WHERE id=".$id.";"
);

////////////////////////////////////////////////////////////////////

echo json_encode([
    "success" => true
]);

mysqli_close($connexion);
exit();
?>