<?php

/*
    INTIALISATION DE LA BASE DE DONNEE
    Activez ce fichier ou ouvrez sa page si vous souhaitez reinitialiser la base donnée.
    Ce fichier est configuré par ../assets/config/db_init.js
*/

$global_params = [
    "root"        => "../",
    "root_public" => "",
];

require_once($global_params["root"] . "assets/script/php/constants.php");
require_once($global_params["root"] . "assets/script/php/functions.php");

$INIT_CONFIG = json_decode( file_get_contents($global_params["root"] . "assets/config/db_init.json") , true );

if (!$INIT_CONFIG["init_database_enabled"]) { 
    // PAR DEFAUT ON NE VEUT PAS QUE QUELQU'UN PUISSE LANCER CE FICHIER
    
    // Normalement ce fichier devrait être executé uniqument depuis la commande du serveur,

    // dans notre cas de figure c'est un peu compliqué, donc on l'execute depuis un navigateur
    // et on le place comme une page, 
    // mais pour un vrai site il ne faudrait surtout pas publier cette page à la vue de potentiel utilisateur malveillant, 
    // mais juste s'en servir en local pour configurer la base de donnée

    write("file disabled");
    exit();
}

// Paramètres du fichier

$addFriends     = $INIT_CONFIG["addFriends"     ];
$addBotFriends  = $INIT_CONFIG["addBotFriends"  ];
$addPageLikes   = $INIT_CONFIG["addPageLikes"   ];
$addBotMessages = $INIT_CONFIG["addBotMessages" ];
$nBots          = $INIT_CONFIG["nBots"          ]; // nombre de bots auto générés
$botPassword    = $INIT_CONFIG["botPassword"    ]; // Les mots de passe sont pas aléatoire pour que vous puissiez tester les comptes bots
$users          = $INIT_CONFIG["users"];

//////////////////////////////////////////////
// CREATION DES TABLES

$connexion = mysqli_connect( $DB_URL, $DB_ACCOUNT, $DB_PASSWORD );
if (!$connexion) { echo "data base error"; exit(); }

$table_create = file_get_contents($global_params["root"] . "assets/script/sql/table_create.sql");
$connexion->multi_query($table_create);

$connexion = mysqli_connect ( $DB_URL, $DB_ACCOUNT, $DB_PASSWORD, $DB_NAME );
if (!$connexion) { echo "data base error"; exit(); }

//////////////////////////////////////////////

// REMPLISSAGE DES TABLES

// CREATION DE COMPTES CLASSIQUES (sans page publique par défaut)
foreach($users as &$user) {
    $user = addUser($user);
}

// CREATION DE PAGES PUBLICS
$bots = [];
for ($i = 0; $i < $nBots; $i++) {
    $bots[$i] = addBot($i);
}

// DEMANDE D'AMIS
if ($addFriends)
{
    foreach($users as $user) {
        // faire en sorte que tout les comptes par défaut soient amis
        foreach($users as $user2)
            if (
                    $user["id"]!=$user2["id"] &&
                    $connexion->query("SELECT id FROM friends " . 
                    "WHERE (user_id_0=".$user ["id"]." AND user_id_1=".$user2["id"].") ".
                    "OR    (user_id_0=".$user2["id"]." AND user_id_1=".$user ["id"].")"
                    )->num_rows == 0 
            )
                $connexion->query(
                    "INSERT INTO friends (user_id_0, user_id_1, accepted) VALUES ( ".
                    $user["id"].", ".$user2["id"].", 1".
                    ")"
                );

        // ajouter quelque bots
        if ($addBotFriends)
            foreach($bots as $bot) {
                if (rand(0, 2)==0)
                    if (rand(0, 3)==0)
                        $connexion->query(
                            "INSERT INTO friends (user_id_0, user_id_1, accepted) VALUES ( ".
                            $bot["id"].", ".$user["id"].", 1".
                            ")"
                        );
                    else
                        $connexion->query(
                            "INSERT INTO friends (user_id_0, user_id_1, accepted) VALUES ( ".
                            $bot["id"].", ".$user["id"].", 0".
                            ")"
                        );
            }

    }
}

// PAGE LIKED
if ($addPageLikes) {
    foreach($bots as $bot) {
        // like une page classique
        // ce seront des likes par défaut à la creation
        // si l'utilisateur reroll/supprime sa page, ils disparaitrons
        $toLike = $users[array_rand($users)];
        $connexion->query(
            "INSERT INTO pages_liked (user_id, like_id) VALUES (".$bot["id"].", ".$toLike["id"].") ; "
        );
        $connexion->query(
            "UPDATE users SET likes=(likes+1) WHERE id=".$toLike["id"]." ;"
        );
 
        // like un bot
        $toLike = $bots[array_rand($bots)];
        $connexion->query(
            "INSERT INTO pages_liked (user_id, like_id) VALUES (".$bot["id"].", ".$toLike["id"].") ; "
        );
        $connexion->query(
            "UPDATE users SET likes=(likes+1) WHERE id=".$toLike["id"]." ;"
        );
    }
}

// ADD BOTS
if ($addBotMessages) {
    foreach($bots as $bot) {
        // chaque bot doit ajouter des messages
        $botInfos = $connexion->query(
            "SELECT public_image, public_name, class FROM users WHERE id=".$bot["id"]
        )->fetch_assoc();

        $nMsg = rand(0, 5);
        for ($i=0; $i < $nMsg; $i++)
        {
            $connexion->query(
                "INSERT INTO posts (user_id, public_image, public_name, content) VALUES (".
                $bot["id"]. ", ".
                $botInfos["public_image"]. ", ".
                "\"". $connexion->real_escape_string( $botInfos["public_name"]      ) ."\", ".
                "\"". $connexion->real_escape_string( inspirate($botInfos["class"]) ) ."\" ".
                ");"
            );
        }
    }
}

// SUPPRIMER TOUTE LES SESSIONS :
foreach(glob(ini_get("session.save_path") . "/*") as $sessionFile)
    unlink($sessionFile);

mysqli_close($connexion);
write("La base de donnée a été réinitialisé");

////////////////////////////////////////////////////////////////////////////////////////////////////////

function addUser($user) {
    
    $connexion = $GLOBALS["connexion"];
    $connexion->query(
        "INSERT INTO `users` (`username`,    `password`, `admin`) VALUES " .
        "(\"" . $connexion->real_escape_string($user["username"])                                   . "\", " .
         "\"" . $connexion->real_escape_string(password_hash($user["password"], PASSWORD_DEFAULT))  . "\", " .
         "\"" . $connexion->real_escape_string($user["admin"])                                      . "\");"
    );

    $user += 
        [
            "id"=> $connexion->query(
                "SELECT id, class FROM users WHERE username=\"".$connexion->real_escape_string($user["username"])."\""
            )->fetch_assoc()["id"]
        ];

    if ($user["enable_public"])
    {
        $public_page = generateRandomPublicData();

        $connexion->query(
            "UPDATE `users` SET " . 
            "`enable_public`=TRUE, " .
            "`public_name`=\""  . $connexion->real_escape_string($public_page["public_name"]) . "\", " .
            "`public_image`=" . $public_page["public_image"] . ", " .
            "`last_reroll`="  . time() . ", " .
            "`description`=\""  . $connexion->real_escape_string(inspirate($public_page["class"])) . "\", " .

            "`specie`=\"" . $connexion->real_escape_string($public_page["specie"]) . "\", " .
            "`class`=\""  . $connexion->real_escape_string($public_page["class"])  . "\", " .
            "`title`=\""  . $connexion->real_escape_string($public_page["title"])  . "\" " .


            " WHERE `id`=" . $user["id"] . " ;"
        );
    }

    return $user;
}

function addBot($index) {
    
    $username = "Bot_" . str_pad($index, 4, "0", STR_PAD_LEFT);
    $bot = addUser([
        "username"      =>  $username,
        "password"      =>  $GLOBALS["botPassword"], 
        "admin"         =>  FALSE,
        "enable_public" =>  TRUE
    ]);

    return $bot;
}

?>