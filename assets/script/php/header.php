<?php

/*

    HEADER :
    Ce fichier s'execute sur l'ensemble des pages.
    Avant celui ci s'execute aussi le fichier functions.php (fonctions générals)

*/

require_once($global_params["root"] . "assets/script/php/security.php");
require_once($global_params["root"] . "assets/script/php/modules.php");

// IMPERATIF POUR LE FONCTIONNEMENT DU HEADER
if (!isset($global_params["root_public"]) || !isset($global_params["root"]))
{
    echo "ERROR: public root is not set";
    separator();
    exit();
}

/*///////////////////////////////////////////

Listes des paramètres de _SESSION:
  id | username | admin
  enable_public | memory_public | banned | public_name | public_image
  init_time | last_time | inactive_time | max_time
  connected | token_id  | token_expire

Liste des cookies :
  cookie_id | cookie_pass | cookie_expire

/*///////////////////////////////////////////
// CONSTANTES :

require_once($global_params["root"] . "assets/script/php/constants.php");

//////////////////////

session_start();
if (!tryConnect()) // update session
{
    // au moment du déploiement,
    // attention à bien configurer le php.ini / .htaccess,
    // de tel sorte à ce que l'affichage des erreurs soit désactivé
    // sinon l'identifiant de la base de donnée pourrait fuiter
    write("Erreur de connection.");
    exit();
}

/////////////////////

if (isset($global_params["redirect"]) && $global_params["redirect"] === TRUE) // page privé
{
    // si l'utilisateur n'est pas connecté on head vers la page de connection
    // header($global_params["root_public"] + "page/public/login")
    // eventuellement avec un q?= de la page actuel pour pouvoir être redirigé ici ensuite
    redirectHomeConnect();
}

if (isset($global_params["admin_req"]) && $global_params["admin_req"] === TRUE) // page admin
{
    redirectNotAdmin();
}

?>



<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <link rel="icon" href=<?php echo $global_params["root_public"] . "assets/image/logo-rond.ico" ; ?> />

    <title>
        <?php
        if (isset($global_params["title"]))
            echo $global_params["title"];
        else
        {
            echo "unknown page";
            $global_params["title"] = "unknown";
        }
        ?>
    </title>

    <!-- global css -->
    <link rel="stylesheet" type="text/css" href="<?=$global_params["root_public"]."assets/css/all.css"?>">
    <link rel="stylesheet" type="text/css" href="<?=$global_params["root_public"]."assets/css/search.css"?>">
    <!-- additionnal css and style -->
    <?php
    if (!$_SESSION["connected"])
        echo "<link rel=\"stylesheet\" type=\"text/css\" href=" . $global_params["root_public"] . "assets/css/login.css>";
    else
        echo "<link rel=\"stylesheet\" type=\"text/css\" href=" . $global_params["root_public"] . "assets/css/menu.css>";

        
    if (isset($global_params["css_add"]))
        foreach($global_params["css_add"] as $css)
            echo "<link rel=\"stylesheet\" type=\"text/css\" href=" . $global_params["root_public"] . "assets/css/" . $css.">";
    ?>

</head>


<body>
<header> <div id = "up_panel">
<!-- HEADER -->
<!-- ------------------------------------------ -->

        <script>
            // variable globals
            const token_id      = <?=json_encode($_SESSION["connected"] ? $_SESSION["token_id"] : "none")?>;
            const root          = <?=json_encode($global_params["root"])?>;
            const root_public   = <?=json_encode($global_params["root_public"])?>;

            function openPage(relLink) {
                relLink = root_public + "page/" + relLink;

                if (window.event.ctrlKey)
                    window.open(relLink);
                else
                    window.open(relLink, "_self");
            }

            function redirection() { // en reference au GET : q=
                url = GET("q");
                if (url == null)
                    url = window.location.href.split('?')[0];
                else
                    url = decodeURIComponent(url.replace(/\+/g, ' '));
                window.open(url,"_self"); // retour à la page q
            }

            // j'ai trouvé cette version de la fonction GET sur stackoverflow
            // je voulais pas prendre le risque de la faire moi même
            // pour éviter d'introduire des bugs
            function GET (param) {
                let otp = {};

                window.location.href.replace( location.hash, '' ).replace( 
                    /[?&]+([^=&]+)=?([^&]*)?/gi,
                    function( m, key, value ) {
                        otp[key] = value !== undefined ? value : '';
                    }
                );

                if (param) return otp[param] ? otp[param] : null;
                return otp;
            }
        </script>

        <div class="containers_three_left_00">
            <div id="page_logo">
                <input
                        class="image_001" type="image"
                        src=<?php echo $global_params["root_public"] . "assets/image/logo.jpg" ?>
                        alt="Harry Play, Role Potter"
                width="80" height="80"
                onclick="openPage(<?= $_SESSION['connected'] ? '\'private/main.php\'' : '\'public/home_page.php\'' ?>);"
                ></div>

        </div>

        <div class="containers_three_mid_00">
            <h1><?php echo $global_params["title"]; ?></h1>
        </div>

        <div class="containers_three_right_00">
            <?php
            if ($_SESSION["connected"])
                menu_when_connected();
            else
                menu_when_not_connected();
            ?>
        </div>

<!-- ------------------------------------------ -->
</div> </header>

<div id = "mid_panel">
    <div id = "mid_container">