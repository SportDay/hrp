<?php

    /*

    SECURITY:
    Ce fichier repertorie des fonction de verification relative à la gestion des compte et la connexion.
    Ces fonctions ont donc naturellement des effets de bords sur la base de donnée réseau et les cookies/session.

    */

    /////////////////////////////////////////////////////////////////////////////////
    // ACTUALISATION DE LA SESSION (s'execute avant l'execution de chaque page)

    function sessionDisconnect() {
        session_destroy();
        session_unset();
        session_start();
        $_SESSION["connected"] = false;
        $_SESSION["admin"]     = false;    
    }

    // Cette fonction sert à actualiser l'état de la session (que l'utilisateur soit connecté ou non)
    function tryConnect () { // return false en cas d'erreur
        $connexion = makeConnection(1);
        if (!$connexion) return false;

        //////////////////////////////////////
        // TRY TO CONNECT USING SESSION
        if (isset($_SESSION["connected"]) && $_SESSION["connected"]) {
            if (isset($_SESSION["inactive_time"]) && $_SESSION["inactive_time"] > time() )
            {
                $result = $connexion->query(
                    "SELECT * FROM users WHERE id=". $_SESSION["id"] . " ;"
                )->fetch_assoc();

                // CONNECT
                $_SESSION["inactive_time"] = min(
                    $_SESSION["max_time"], 
                    (time() + $GLOBALS["TIME_SESS_INACTIVE"])
                );

                $_SESSION["last_time"]      = time();
                $_SESSION["banned"]         = $result["banned_to"] > time();
                $_SESSION["banned_to"]      = $result["banned_to"];

                mysqli_query($connexion, 
                    "UPDATE users SET " .
                    "last_join=" . $_SESSION["last_time"] . ", " .
                    "banned=" . $_SESSION["banned"]       . " "  .
                    "WHERE `id`=" . $_SESSION["id"]       . " ;"
                );

                $_SESSION["memory_public"] = $result["memory_public"];
                
                mysqli_close($connexion);
                return true;
            }

        }

        //////////////////////////////////////
        // TRY TO CONNECT USING COOKIES
        
        if (!isset($_COOKIE["cookie_id"])  || !isset($_COOKIE["cookie_pass"])) {
            sessionDisconnect();
            mysqli_close($connexion); return true;
        }
        
        $result = $connexion->query(
            "SELECT * FROM users WHERE cookie_id=\"". $connexion->real_escape_string($_COOKIE["cookie_id"]) . "\" ;"
        );

        if (
            $result->num_rows == 0 ||
            !($result = $result->fetch_assoc())["cookie_enabled"] ||
            $_COOKIE["cookie_pass"] != $result["cookie_pass"] ||
            $_COOKIE["cookie_expire"] < time()            
            ) { 
                sessionDisconnect();
                mysqli_close($connexion); return true;
            }

        //////////////////////////////////////////////
        // NOW CONNECT

        $_SESSION["id"]             = $result["id"];
        $_SESSION["username"]       = $result["username"];
        $_SESSION["admin"]          = $result["admin"];

        $_SESSION["enable_public"]  = $result["enable_public"];
        $_SESSION["memory_public"]  = $result["memory_public"];
        $_SESSION["public_name"]    = $result["public_name"];
        $_SESSION["public_image"]   = $result["public_image"];
        $_SESSION["banned"]         = $result["banned_to"] > time();
        $_SESSION["banned_to"]      = $result["banned_to"];

        $_SESSION["init_time"]      = time();
        $_SESSION["last_time"]      = time();
        $_SESSION["inactive_time"]  = time() + $GLOBALS["TIME_SESS_INACTIVE"];
        $_SESSION["max_time"]       = time() + $GLOBALS["TIME_SESS_END"];

        $_SESSION["connected"]      = true;

        $_SESSION["token_id"]       = $result["token_id"];
        $_SESSION["token_expire"]   = $result["token_expire"];
        
        $connexion->query( 
            "UPDATE users SET " .
            "last_join="    . $_SESSION["last_time"] . ", " .
            "banned="       . $_SESSION["banned"]    . " "  .
            "WHERE `id`="   . $_SESSION["id"]        . " ;"
        );
        
        mysqli_close($connexion);
        return true;
    }

    /////////////////////////////////////////////////////////////////////////////////
    // SUPPRESSION DES DONNEES

    function removeAccount($currentAccount=TRUE, $id=0) {
        if ($currentAccount) $id = $_SESSION["id"];
        
        //////////////
        $connexion = makeConnection(2);
        if (!$connexion) return false;

        // supprimer le compte: (users)
        $connexion->query(
            "DELETE FROM `users` WHERE `id`=" . $id . " ;"
        );

        if ($currentAccount)
        {
            // mettre fin aux cookies/session
            setcookie("cookie_id",      "", time() - 3600, $GLOBALS["COOKIE_PATH"]);
            setcookie("cookie_pass",    "", time() - 3600, $GLOBALS["COOKIE_PATH"]);
            setcookie("cookie_expire",  "", time() - 3600, $GLOBALS["COOKIE_PATH"]);
            setcookie("token_id",       "", time() - 3600, $GLOBALS["COOKIE_PATH"]);

            session_unset();
            session_destroy();
            session_start();

        }

        mysqli_close($connexion);
        return false;
    }

    function removePublicPage($currentAccount=TRUE, $id=0) {
        if ($currentAccount) $id = $_SESSION["id"];

        //////////////
        $connexion = makeConnection(2);
        if (!$connexion) return false;

        $userData = $connexion->query("SELECT * FROM `users` WHERE id=" . $id . " ;")->fetch_assoc();

        if (!$userData["enable_public"]) {
            mysqli_close($connexion);
            return false;
        }

        /////////////
        // supprimer: reports, posts, pages_liked likes direct_messages
        // attention, il y a un ordre de suppression

        // pages_liked | parents : | enfants : reports et likes
        $olds_pages_liked = $connexion->query("SELECT like_id as id FROM pages_liked WHERE user_id=".$id);
        while ($old = $olds_pages_liked->fetch_assoc()) 
            $connexion->query("UPDATE users SET likes=(likes-1) WHERE id=".$old["id"]." ;");
        $connexion->query(
            "DELETE FROM `pages_liked` WHERE (".
            "`user_id`=" . $id .
            " OR " .
            "`like_id`=" . $id .
            " );"
        );

        // posts | parents : | enfants : reports et likes
        
        // supprimer les reports venant du compte
            $olds_reports = $connexion->query("SELECT message_id as id FROM reports WHERE user_id=".$id);
            while ($old = $olds_reports->fetch_assoc()) 
                $connexion->query("UPDATE posts SET reportnum=(reportnum-1) WHERE id=".$old["id"]." ;");
            $connexion->query(
                "DELETE FROM `reports` WHERE `user_id`=" . $id . " ;"
            );

        // supprimer les likes venant du compte
            $olds_likes = $connexion->query("SELECT message_id as id FROM likes WHERE user_id=".$id);
            while ($old = $olds_likes->fetch_assoc()) 
                $connexion->query("UPDATE posts SET like_num=(like_num-1) WHERE id=".$old["id"]." ;");
            $connexion->query(
                "DELETE FROM `likes` WHERE `user_id`=" . $id . " ;"
            );

        // supprimer les posts venant du compte
            $olds_response = $connexion->query(
                "SELECT id FROM posts WHERE response_id IN (SELECT id FROM posts WHERE user_id=".$id.")"
            );
            while ($old = $olds_reports->fetch_assoc()) 
                $connexion->query("UPDATE posts SET responses_id=NULL WHERE id=".$old["id"]." ;");
            $connexion->query(
                "DELETE FROM `posts` WHERE `user_id`=" . $id . " ;"
            );

        // direct_messages
        $connexion->query(
            "DELETE FROM `direct_messages` WHERE `(from_id`=" . $id . " OR `to_id`=" . $id . ") AND NOT private;"
        );

        /////////////
        // desactiver la page publique (dans users)
        $connexion->query(
            "UPDATE users SET " .
            "likes=0," .
            "enable_public=FALSE, " .
            "memory_public=TRUE  " .
            // etant donné qu'on ne peut pas reroll n'importe quand,
            // un utilisateur de réactiver sa page supprimé
            // du coup je ne supprime pas
            // les données rudimentaires d'une page publique

            " WHERE id=" . $id . " ;"
        );

        /////////////
        // update la session
        if ($currentAccount) {
            $_SESSION["enable_public"] = FALSE;
            $_SESSION["public_name" ] = "";
            $_SESSION["public_image"] = -1;
        }

        mysqli_close($connexion);
        return false;
    }

    /////////////////////////////////////////////////////////////////////////////////
    // UTILITIES

    function redirectHomeConnect () {
        if ( !$_SESSION["connected"] ) {

            if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')   
                $url = "https://";   
            else  
                $url = "http://";   
            
            $url = urlencode($url . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);

            header("Location: " . 
                $GLOBALS["global_params"]["root_public"] ."page/public/home_page.php?to_connect&q=" . $url
            );

            exit();
        }
    }

    function redirectNotAdmin() {
        if (!isset($_SESSION["admin"]) || !$_SESSION["admin"])
        {
            header("Location: " . $GLOBALS["global_params"]["root_public"] . "page/public/home_page.php");
            exit();
        }
    }
    
?>