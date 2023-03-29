<?php 
    
    $global_params = [
        "root"        => "../../../../",
        "root_public" => "../../../../root_public/",
    ];

    require($global_params["root"] . "assets/script/php/constants.php");
    require($global_params["root"] . "assets/script/php/functions.php");
    session_start();
    
    if (
        !isset($_POST["username"]) || 
        !isset($_POST["password"]) || 
        !isset($_POST["remember"])
        )
    {
        echo json_encode([
            "success" => false,
            "error"   => "Requête incorrecte."
        ]); exit();
    }

    // GET LES INFOS DE CONNECTION
    $username = $_POST["username"];
    $password = $_POST["password"];
    $remember = $_POST["remember"];

    // PARSE
    if (!isValideName($username) || !isValidePassword($password)) {
        // invalide name / password
        echo json_encode([
            "success" => false,
            "error"   => "Problème de formattage."
        ]); exit();
    }

    // CONNECTION A LA BASE DE DONNEE
    $connexion = makeConnection();

    $result = $connexion->query(
        "SELECT * FROM users WHERE username=\"". $connexion->real_escape_string(trim($username)) . "\";"
    );



    if ($result->num_rows == 0)
    {
        // no match error
        // ne pas indiquer que le pseudo existe ou n'existe pas.
        echo json_encode([
            "success" => false,
            "error"   => "Couple pseudo/mot de passe innexistant."
        ]); exit();
    }

    $result = $result->fetch_assoc();

    // COMPARER A LA BASE DE DONNEE

    if ($result["last_try"] - time() > 2) // X seconds entre chaques tentives
    {
        echo json_encode([
            "success" => false,
            "error"   => "Veuillez réessayer (moins rapidement)."
        ]); exit();
    }

    if (!password_verify($password, $result["password"])) {
        // wrong password error
        echo json_encode([
            "success" => false,
            "error"   => "Couple pseudo/mot de passe innexistant."
        ]); exit();
    }

    // SIGN IN
    $_SESSION["id"]             = $result["id"];
    $_SESSION["username"]       = $result["username"];
    $_SESSION["admin"]          = $result["admin"];

    $_SESSION["enable_public"]  = $result["enable_public"];
    $_SESSION["public_name"]    = $result["public_name"];
    $_SESSION["public_image"]   = $result["public_image"];

    $_SESSION["init_time"]      = time();
    $_SESSION["last_time"]      = time();
    $_SESSION["inactive_time"]  = time() + $TIME_SESS_INACTIVE;
    $_SESSION["max_time"]       = time() + $TIME_SESS_END;

    $_SESSION["connected"]      = true;

    $cookie_expire = time() + $TIME_COOKIE_CONNECT;

    $_SESSION["token_expire"]   = $cookie_expire;
    $_SESSION["token_id"]       = randomString();

    $connexion->query( 
        "UPDATE users SET ".
        "last_join="    .$_SESSION["last_time"]                                 .", ".
        "token_id=\""   .$connexion->real_escape_string($_SESSION["token_id"])  ."\", ".
        "token_expire=" .$cookie_expire                                         ." ".
        "WHERE id="     .$_SESSION["id"] ." ;"
    );

    // AJOUT DU COOKIE SI REMEMBER
    if ($remember) {

        // remove old cookie
        setcookie("cookie_id",      "", time() - 3600, $COOKIE_PATH);
        setcookie("cookie_pass",    "", time() - 3600, $COOKIE_PATH);
        setcookie("cookie_expire",  "", time() - 3600, $COOKIE_PATH);

        // try to generate unused cookie id
        $cookie_id       = randomString();
        $cookie_password = randomString();

        for ($i = 0; $i < 101; $i++) { // une pour eviter une boucle infinie
            if ($i==100) {
                echo json_encode([
                    "success" => true,
                    "error"   => "Erreur de cookie."
                ]);
                mysqli_close($connexion);
                exit();
            }
            
            if ( $connexion->query(
                    "SELECT * FROM users WHERE cookie_id=\"". $connexion->real_escape_string($cookie_id) . "\";"
                    )->num_rows != 0
                ) 
            {
                $cookie_id = randomString();
                continue;
            }

            break;
        }

        // store cookie_id and pass

        setcookie("cookie_id",      $cookie_id,       $cookie_expire, $COOKIE_PATH);
        setcookie("cookie_pass",    $cookie_password, $cookie_expire, $COOKIE_PATH);
        setcookie("cookie_expire",  $cookie_expire,   $cookie_expire, $COOKIE_PATH);

        $connexion->query( 
            "UPDATE users SET " . 
            
            " cookie_id=\""       . $cookie_id . "\"," .
            " cookie_enabled="   . "TRUE, " .
            " cookie_pass=\"" . $cookie_password . "\"," .
            " cookie_expire="     . $cookie_expire .
        
            " WHERE `id`=\"" . $_SESSION["id"] . "\" ;"
        );
    }

    echo json_encode([
        "success" => true,
        "error"   => ""
    ]);

    mysqli_close($connexion);

?>