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

$connexion = makeConnection();

//////////////////////////////////////////////////////////////////////


// trouver le post
$post = $connexion->query(
    "SELECT * FROM posts WHERE id=\"" . $connexion->real_escape_string($_POST["post_id"]) . "\";"
);
if ($post->num_rows==0)
{
    echo json_encode([
        "success" => false,
        "error"   => "Base de donnée hors d'accès."
    ]); exit();
} $post = $post->fetch_assoc();


$liked = $connexion->query(
    "SELECT id FROM likes WHERE message_id=" . $post["id"] . " AND user_id=" . $_SESSION["id"]
)->num_rows != 0;


if($liked) { /////////// SI IL A ETE LIKE => LE DISLIKE

    $connexion->query( // decremente
        "UPDATE posts set like_num=(like_num-1) WHERE id=" . $post["id"] . ";"
    );
    $connexion->query( // supprimer le like
        "DELETE FROM likes WHERE (message_id=".$post["id"].") AND (user_id=".$_SESSION["id"].");"
    );

    $post["like_num"]--;

} else {    /////////// SI IL N'A PAS ETE LIKE => LE LIKE

    $connexion->query( // incremente
        "UPDATE posts set like_num=(like_num+1) WHERE id=".$post["id"]
    );
    $connexion->query( // ajouter like
        "INSERT INTO `likes` (`message_id`,`user_id`) VALUES (".$post["id"].", ".$_SESSION["id"].");"
    );

    $post["like_num"]++;
}

////////////////////////////////////////////////////////////////////

echo json_encode([
    "success" => true,
    "nbr_like" => $post["like_num"],
    "liked" => !$liked
]);

mysqli_close($connexion);
exit();
?>