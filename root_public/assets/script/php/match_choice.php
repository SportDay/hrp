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

    $connexion = makeConnection();

    ////////////////////////////////////////////////////////////////////

    if ( // faire un like/dislike
        isset($_POST["like_token_1"]) && isset($_SESSION["like_token_1"]) 
        && ($_POST["like_token_1"]==$_SESSION["like_token_1"])
        && isset($_POST["isLike"])
    )
    {
        if ($_POST["isLike"] == "true") {
            
            if ($connexion->query(
                    "SELECT id FROM pages_liked WHERE user_id=".$_SESSION["id"]." AND like_id=".$_SESSION["like_cache_id"]
            )->num_rows == 0)
            {
                $connexion->query(
                    "INSERT INTO pages_liked (user_id, like_id) VALUES (".$_SESSION["id"].", ".$_SESSION["like_cache_id"].")"
                );
                $connexion->query(
                    "UPDATE users SET likes=(likes+1) WHERE id=".$_SESSION["like_cache_id"]." ;"
                );
            }

        }
        else {
            $connexion->query(
                "UPDATE pages_liked SET priority=FALSE WHERE user_id=".$_SESSION["like_cache_id"]." AND like_id=".$_SESSION["id"]
            );
        }
    }

    // trouver une nouvelle personne à like
    $new_user = $connexion->query( // essayer avec un profile qui nous a liké
        "
        SELECT *
        FROM
        (
            SELECT user_id FROM pages_liked WHERE priority AND like_id=".$_SESSION["id"]." AND user_id NOT IN (
                SELECT like_id FROM pages_liked WHERE (user_id=".$_SESSION["id"].")
            )
        ) as t1
        inner join users
        on t1.user_id=users.id

        ORDER BY RAND()
        LIMIT 1
        "
    );

    if ($new_user->num_rows == 0 || rand(0,3)!=0) // mélangé avec des profiles classique
        $new_user = $connexion->query(
            "SELECT * FROM users WHERE (".
            "    enable_public AND (NOT id=".$_SESSION["id"].") AND id NOT IN (".
            "        SELECT like_id FROM pages_liked WHERE (user_id=".$_SESSION["id"].")".
            "    )".
            ")".
            "ORDER BY RAND()".
            "LIMIT 1"
        );
    

    if ($new_user->num_rows != 0) // si on a réussi à trouver un profile
    {
        $new_user = $new_user->fetch_assoc();

        $new_user += [
            "like_token_1" => ($_SESSION["like_token_1"] = randomString())
        ];
        $_SESSION["like_cache_id"] = $new_user["id"];
    }
    else // si il n'y a personne à liker
        $new_user = [
            "public_name"   => "",
            "title"         => "",
            "specie"        => "",
            "class"         => "",
            "public_image"  => -1,//getImagePath(0, false, "", true),
            "description"   => "Personne à l'horizon. Veuillez réessayer plus tard.",
            "like_token_1"  => "none"
        ];
    
    ////////////////////////////////////////////////////////////////////

    echo json_encode([
        "success"       => true,
        "name"          => $new_user["public_name"],
        "title"         => $new_user["title"],
        "specie"        => $new_user["specie"],
        "class"         => $new_user["class"],
        "image"         => getImagePath($new_user["public_image"], false, "", true),
        "desc"          => htmlentities($new_user["description"]),
        "like_token_1"  => $new_user["like_token_1"]
    ]);
    
    mysqli_close($connexion);
    exit();
?>