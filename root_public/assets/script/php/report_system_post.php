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
verifyToken();

if (!isset($_POST["post_id"])) {
    echo json_encode([
        "success" => false,
        "error"   => "Requête incorrecte."
    ]); exit();
}

$connexion = makeConnection();

////////////////////////////////////////////////////////////////////

// on recupere le post
$post = $connexion->query(
    "SELECT * FROM posts WHERE id=\"".$connexion->real_escape_string($_POST["post_id"])."\";"
);
if ($post->num_rows==0)
{
    echo json_encode([
        "success" => false,
        "error"   => "Base de donnée hors d'accès."
    ]); exit();
} $post = $post->fetch_assoc();

// on verifie si on on l'a déjà report le post
$reported = $connexion->query(
    "SELECT id FROM reports WHERE message_id=" . $post["id"] . " AND user_id=" . $_SESSION["id"] . ";"
)->num_rows != 0;

if($reported){  ////////// SI LE POST a été report on annule le report

    $connexion->query( // on decremente le reportnum
        "UPDATE posts set reportnum=(reportnum-1) WHERE id=" . $post["id"]
    );
    $connexion->query( // et on enleve le report
        "DELETE FROM reports WHERE (message_id=".$post["id"].") AND (user_id=".$_SESSION["id"].");"
    );

} else {        ////////// SI LE POST n'a pas été report on le report
    
    $connexion->query( // on incremente le reportnum
        "UPDATE posts set reportnum=reportnum+1,  last_report=unix_timestamp(CURRENT_TIMESTAMP) WHERE id=".$post["id"]
    );
    $connexion->query( // et on ajoute un report
        "INSERT INTO `reports` (`message_id`,`user_id`) VALUES (".$post["id"].", ".$_SESSION["id"].");"
    );
}


////////////////////////////////////////////////////////////////////

echo json_encode([
    "success"   => true,
    "report"    => !$reported // le nouvel état (toggle)
]);

mysqli_close($connexion);
exit();
?>