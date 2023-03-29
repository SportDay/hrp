<?php

$global_params = [
    "root"        => "../../../../",
    "root_public" => "../../../../root_public/",
];

require($global_params["root"] . "assets/script/php/constants.php");
require($global_params["root"] . "assets/script/php/functions.php");

////////////////////////////////////////////////////////////////////
// ETABLISSEMENT DE LA CONNECTION

session_start();

if (!isset($_SESSION["connected"]) || !$_SESSION["connected"] || !$_SESSION["enable_public"]) {
    echo json_encode([
        "success" => false,
        "error"   => "Requête incorrecte."
    ]); exit();
}

////////////////////////////////////////////////////////////////////

echo json_encode([
    "success" => true,
    "message" =>inspirate()
]);

exit();
?>