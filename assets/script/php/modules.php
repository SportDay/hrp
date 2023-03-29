<?php 

/*

    MODULES:
    Les modules correspondent à un ensemble de fonctions qui générent des élements html procéduraux.
    Ces fonctions produisent donc effets de bords sur les pages ou elles sont appelés.

*/

////////////////////////////////////////////////
// MENU
function menu_when_not_connected () {
    // bouton de connection et d'enregistrement

    ?>
        <!-- DECLANCHEURS DE POP UP -->
        
        <div class="login_right_button">
            <button class="login_right_button" onclick="
                        document.getElementById('register').style.display='block';
                        document.getElementById('login')   .style.display='none';
                        " 
                    style="width:auto;"
                    >S'inscrire</button>
            <br>
            <button class="login_right_button" onclick="
                        document.getElementById('login')   .style.display='block';
                        document.getElementById('register').style.display='none';
                        " 
                    style="width:auto;"
                    >Se connecter</button>
        </div>

        <!-- POP UPS -->

        <div id="login" class="reg_log_model">
            <div class="modal-content animate">
            <div class="reg_log_form_container">
                <span onclick="document.getElementById('login').style.display='none'" class="close" title="Fermer">&times;</span>
                <label class="popup_form_title" for="pseudo"><b>Pseudo</b></label>
                <input id="login_name" type="text" placeholder="Pseudo" name="pseudo" onkeypress="loginNameHandle(event)">

                <label class="popup_form_title" for="password"><b>Mot de passe</b></label>
                <input id="login_password" type="password" placeholder="Mot de passe" name="password" onkeypress="loginPasswordHandle(event)">

                <p id="login_error" class="popup_text" style="display:none"> ERROR </p>
                <button type="submit" onclick="login();">Se connecter</button>
                
                <label class="popup_text">Se souvenir de moi?
                    <input id="login_remember" type="checkbox" checked="checked" name="remember" >
                </label>

                <br>

                <button type="button" onclick="document.getElementById('login').style.display='none'" class="cancelbtn">Annuler</button>
            </div>
            </div>
        </div>

        <div id="register" class="reg_log_model">
            <div class="modal-content animate" class="reg_log_form_container">
            <div class="reg_log_form_container">
                <span onclick="document.getElementById('register').style.display='none'" class="close" title="Fermer">&times;</span>
                <label class="popup_form_title" for="pseudo"><b>Pseudo</b></label>
                <input id="register_name" type="text" 
                placeholder="Pseudo | 2-16 charactères : A-z et 0-9 et tiret et tiret bas" 
                name="pseudo" onkeypress="registerNameHandle(event)">

                <label class="popup_form_title" for="password"><b>Mot de passe</b></label>
                <input id="register_password" type="password" 
                placeholder="Mot de passe | 6-26 charactères : A-z et 0-9 et _*+-()[]" 
                name="password" onkeypress="registerPasswordHandle(event)">
                
                <p id="register_error" class="popup_text" style="display:none"> ERROR </p>

                <button type="submit" onclick="register();">S'inscrire</button>
                <button type="button" onclick="document.getElementById('register').style.display='none'" class="cancelbtn">Annuler</button>
            </div> 
            </div>
        </div>

        <script>
            window.onclick = function(event) {
                if (event.target == document.getElementById('login')) {
                    document.getElementById('login').style.display = "none";
                }
                if (event.target == document.getElementById('register')) {
                    document.getElementById('register').style.display = "none";
                }
            }

            ////////////////////////

            function loginNameHandle(e) {
                if (e.keyCode==13) document.getElementById("login_password").focus();
            }
            function loginPasswordHandle(e) {
                if (e.keyCode==13) login();
            }
            function login() {
                
                nickname = document.getElementById("login_name");
                password = document.getElementById("login_password");
                remember = document.getElementById("login_remember");
                debug    = document.getElementById("login_error");

                let data = new FormData();
                data.append("token_id", token_id);
                data.append("username", nickname.value);
                data.append("password", password.value);
                data.append("remember", remember.checked);
                //////////

                let xmlhttp = new XMLHttpRequest();
                
                xmlhttp.open('POST', root_public + "assets/script/php/login.php");
                xmlhttp.send( data );

                xmlhttp.onreadystatechange = function () {
                    if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
                    {
                        const feedback = JSON.parse(xmlhttp.responseText);

                        if (feedback["success"]) {
                            debug.innerHTML = "Connection réussie.";
                            debug.style.display = "block";
                            redirection();
                        }
                        else {
                            password.value = "";
                            debug.innerHTML = feedback["error"];
                            debug.style.display = "block";
                            password.focus();
                        }
                    }
                    else
                    {
                        debug.innerHTML = "Erreur de connection serveur: " + xmlhttp.status;
                        debug.style.display = "block";
                    }
                }
            }

            function registerNameHandle(e) {
                if (e.keyCode==13) document.getElementById("register_password").focus();
            }
            function registerPasswordHandle(e) {
                if (e.keyCode==13) register();
            }
            function register() {
                nickname = document.getElementById("register_name");
                password = document.getElementById("register_password");
                debug    = document.getElementById("register_error");

                let data = new FormData();
                data.append("token_id", token_id);
                data.append("username", nickname.value);
                data.append("password", password.value);
                //////////

                let xmlhttp = new XMLHttpRequest();
                
                xmlhttp.open('POST', root_public+"assets/script/php/signup.php");
                xmlhttp.send( data );

                xmlhttp.onreadystatechange = function () {
                    if (xmlhttp.readyState === 4 || xmlhttp.status === 200)
                    {
                        //alert(xmlhttp.responseText);
                        const feedback = JSON.parse(xmlhttp.responseText);

                        if (feedback["success"]) {
                            debug.innerHTML = "Création de compte réussie.";
                            debug.style.display = "block";
                            redirection();
                        }
                        else {
                            password.value = "";
                            debug   .innerHTML = feedback["error"];
                            debug.style.display = "block";
                            password.focus();
                        }
                    }
                    else
                    {
                        debug.innerHTML = "Erreur de connection serveur: " + xmlhttp.status;
                        debug.style.display = "block";
                    }
                }
            }

            ////////////////////////
            // ouvrir la petite fenetre de connection
            <?php if (isset($_GET["to_connect"])){
                ?>document.getElementById('login').style.display='block';<?php
            } ?>

        </script>
    <?php
}

function menu_when_connected () {
    // SOMMAIRE des choses à faire
    // 
    // main_page | my_profile | params | likes | match | disconnect

    ?> 
        <div id="menu" class="menu_close">
            <div class="menu_contain_button">
                <input
                    id="open_menu" type="image" 
                    src="<?= getImagePath( $_SESSION["enable_public"] ? $_SESSION["public_image"] : "none")  ?>" width="60"
                    name ="menu" alt  ="menu" onclick="toggleMenu();"
                >
            </div>
            <br>
            <div class="menu_contain" id="menu_contain" style="display:none;">
                
                <div id=is_public_menu>
                <p>- publique -</p>

                <button class="menu_button" id="btn_home"       onclick="openPage('private/main.php')"
                        >Mon Fil</button> <br>
                <button class="menu_button" id="btn_profile"    onclick="openProfile();"
                        >Mon Profile</button> <br>
                <button class="menu_button" id="btn_likes"      onclick="openPage('private/like.php');"
                        >Nouvelles Rencontres</button> <br>
                <button class="menu_button" id="btn_matchs"     onclick="openPage('private/match.php');"
                        >Mes Rencontres</button> <br>

                <p>- privé -</p>
                </div>

                <button class="menu_button" id="btn_friends"   onclick="openPage('private/friends.php');"
                        >Amis</button> <br>
                <div id=btn_admin_div>
                <button class="menu_button" id="btn_admin"     onclick="openPage('admin/admin.php');"
                        >Admin</button> <br> </div>

                <button class="menu_button" id="btn_params"     onclick="openPage('private/params.php');"
                        >Paramètres</button> <br>
                <button class="menu_button" id="btn_disconnect" onclick="disconnect();"
                        >Deconnection</button> <br>

            </div>
        </div>

        <script>
            <?php if (!$_SESSION["enable_public"]) { ?>
                document.getElementById('is_public_menu').style.display='none';
            <?php } ?>
            <?php if (!$_SESSION["admin"]) { ?>
                document.getElementById('btn_admin_div').style.display='none';
            <?php } ?>

            /////////

            function toggleMenu() {
                let menu      = document.getElementById('menu');
                let open_menu = document.getElementById('menu_contain');

                if (open_menu.style.display=='block') {
                    open_menu.style.display='none';
                    menu     .className = "menu_close";
                } else { 
                    open_menu.style.display='block';
                    menu     .className = "menu_open";
                }
            }

            function openProfile(profile_name="<?= htmlentities( $_SESSION["public_name"] ) ?>") {
                openPage('public/public_page.php?user=' + profile_name);
            }

            function disconnect() {
                let data = new FormData();
                data.append("token_id", token_id);
                let xmlhttp = new XMLHttpRequest();
                
                xmlhttp.open('POST', root_public+"assets/script/php/disconnect.php");
                xmlhttp.send( data );

                xmlhttp.onreadystatechange = function () {
                    if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
                    {
                        const feedback = JSON.parse(xmlhttp.responseText);

                        if (feedback["success"]) {
                            if (<?php echo $GLOBALS["global_params"]["redirect"] ? "true" : "false" ; ?>)
                                openPage('public/home_page.php?to_connect&q=' + encodeURIComponent(window.location.href)); // SI PAGE PRIVE
                            else
                            { // SI PAGE PUBLIC
                                let url = window.location.href.split('?');

                                if (url.length == 1)
                                    window.open(url[0] + '?' +           'to_connect&q=' + encodeURIComponent(window.location.href)
                                    , "_self");
                                else
                                    window.open(url[0] + '?' + url[1] + '&to_connect&q=' + encodeURIComponent(window.location.href)
                                    , "_self");
                            }
                        }
                    }
                }
            }
        </script>

    <?php
}

////////////////////////////////////////////////
// BARRE DE RECHERCHE

function search_bar(){
    ?>

    <div id="search_container">
        <form action=<?= $GLOBALS["global_params"]["root_public"]."page/public/search.php"?> method="get">
            <div class="search_grid">
                <input class="search_input" type="search" autocomplete="off" placeholder="Recherche" name="search" minlength="4" required >
                <button id="btn_search" class="btn_search btn_button_btn" onclick="">
                    <img id="img_search" class="img_search" width="32" height="32" src="<?= $GLOBALS["global_params"]["root_public"]."/assets/image/search.png"?>">
                </button>
            </div>
        </form>
    </div>

    <?php 
}

function search_profil($profile){
    ?>
    
    <div class = "mid_content" style="text-align: initial;">
        <div id = "profile">
            <a href= "<?= $GLOBALS['global_params']['root_public'] ?>page/public/public_page.php?user=<?= urlencode($profile["public_name"]) ?>">
                <img class="profile_img_profile" src="<?= getImagePath( $profile["public_image"])  ?>">
            </a>
            <div class="info_profile">
                <span class="profile_nickname" >Nom: <?= htmlentities($profile["public_name"])?></span>
                <span class="profile_titre"    >Titre: <?= htmlentities($profile["title"])?></span>
                <span class="profile_espece"   >Espece: <?= htmlentities($profile["specie"])?></span>
                <span class="profile_classe"   >Classe: <?= htmlentities($profile["class"])?></span>
                <span class="profile_nlikes"   >Likes: <?= htmlentities($profile["likes"])?></span>
            </div>
        </div>

        <?php if (strlen($profile["description"]) > 0) { ?>
            <div class="container_desc border" style="border-radius: 15px">
                <p style="color: white; font-size: 18px; margin-top: 0px; margin-bottom: 0px;"><?= htmlentities(trim($profile["description"]))?></p>
            </div>
        <?php } ?>

    </div>
    <?php
}

////////////////////////////////////////////////
// FRIENDS

function add_friend_bloc($friend) {

    $public_page = $friend["enable_public"] ? $friend["public_name"] : "";
    $public_page = $GLOBALS["global_params"]["root_public"] . "page/public/public_page.php?user=" . urlencode ($public_page);

    ?>
        <div class="request_friend_list" id="friend_bloc_<?=htmlentities($friend["username"])?>">

            <?php if ( $friend["enable_public"] ) { ?>
                <a href="<?= $public_page ?>">
                <img class="request_profile_img" src="<?= getImagePath( $friend["public_image"])  ?>">
                </a>
            <?php } else { ?>
                <img class="request_profile_img" src="<?= getImagePath("") ?>">
            <?php } ?>

            <div class="request_profile_content border" >
                <p class="nick_name_friend"><?= htmlentities($friend["username"])." vous a ajouté!" ?></p>
                <button class="btn_button_btn acceptbtn_low_size btn_accept_friend_btn" onclick='acceptFriend("<?=htmlentities($friend["username"])?>")'
                >Accepter</button>
                <button class="btn_button_btn cancelbtn_low_size btn_reject_friend_btn" onclick='removeFriend("<?=htmlentities($friend["username"])?>")'
                >Refuser</button>
            </div>

        </div>
    <?php
}

function friend_bloc($friend, $specific_root=FALSE, $root_public="") {

    if (!$specific_root)
        $root_public = $GLOBALS["global_params"]["root_public"];

    ?>

        <div id="friend_bloc_<?=htmlentities($friend["username"])?>" class="mid_content" style="text-align: initial;">
            <div id = "profile">


                <?php if($friend["enable_public"]) { ?>
                <a href="<?=$root_public?>page/public/public_page.php?user=<?= str_replace("%", "+", urlencode($friend["public_name"])) ?>">
                    <img class="profile_img_profile" src="<?= getImagePath( $friend["enable_public"] ? $friend["public_image"] : "none", true, $root_public)  ?>">
                </a>
                <?php } else { ?>
                    <img class="profile_img_profile" src="<?= getImagePath( $friend["enable_public"] ? $friend["public_image"] : "none", true, $root_public)  ?>">
                <?php } ?>


                <div class="info_profile">
                    
                    <span class="profile_private_name">Pseudo: <?=htmlentities($friend["username"])?></span>
                    <?php if($friend["enable_public"]) { ?>
                        <span class="profile_public_name" >Nom: <?=   htmlentities($friend["public_name"])?></span>
                        <span class="profile_title"       >Titre: <?= htmlentities($friend["title"])?></span>
                        <span class="profile_specie"      >Espece: <?=htmlentities($friend["specie"])?></span>
                        <span class="profile_class"       >Classe: <?=htmlentities($friend["class"])?></span>
                    <?php } else { ?>
                        <span></span> <span></span> <span></span> <span></span>
                    <?php } ?>

                    <div class="user_menu">
                        <button class="btn_menu_user">&#8226;&#8226;&#8226;</button>
                        <div class="user_menu_content border">
                            <button class="btn_ignr_user" class="btn_ignr_user" onclick='removeFriend(<?=json_encode($friend["username"])?>);'>Supprimer</button>
                        </div>
                    </div>
                    <div class="friend_porfile_espace"></div>
                    <a href="dm.php?private=true&user=<?=urlencode($friend["username"])?>">
                        <img class="msg_img" width="32" height="32" src="<?=$root_public?>assets/image/msg.png">
                    </a>
                </div>


            </div>
        </div>
    <?php

}

////////////////////////////////////////////////
// MATCHS

function match_bloc($friend, $specific_root=FALSE, $root_public="") {

    if (!$specific_root)
        $root_public = $GLOBALS["global_params"]["root_public"];

    ?>
        <div id="friend_bloc_<?=htmlentities($friend["public_name"])?>" class="mid_content" style="text-align: initial;">
            <div id = "profile">

                <a href="<?=$root_public?>page/public/public_page.php?user=<?=str_replace("%", "+", urlencode($friend["public_name"]))?>">
                    <img class="profile_img_profile" src="<?= getImagePath( $friend["enable_public"] ? $friend["public_image"] : "none", true, $root_public)  ?>">
                </a>

                <div class="info_profile">
                    
                    <span class="profile_private_name">Nom: <?=   htmlentities($friend["public_name"])?></span>
                    <span class="profile_public_name" >Titre: <?= htmlentities($friend["title"])?></span>
                    <span class="profile_specie"      >Espece: <?=htmlentities($friend["specie"])?></span>
                    <span class="profile_class"       >Classe: <?=htmlentities($friend["class"])?></span>
                    <span></span>

                    <div class="user_menu">
                        <button class="btn_menu_user">&#8226;&#8226;&#8226;</button>
                        <div class="user_menu_content border">
                            <button class="btn_ignr_user" class="btn_ignr_user" onclick='removeMatch(<?=json_encode($friend["public_name"])?>);'>Supprimer</button>
                        </div>
                    </div>
                    <div class="friend_porfile_espace"></div>
                    <a href="dm.php?private=false&user=<?=urlencode($friend["public_name"])?>">
                        <img class="msg_img" width="32" height="32" src="<?=$root_public?>assets/image/msg.png">
                    </a>
                </div>


            </div>
        </div>
    <?php

}

////////////////////////////////////////////////
// PROFILES BLOC
function profile_bloc($profile){
    $page_liked = $_SESSION["connected"] ?
                    (
                        $GLOBALS["connexion"]->query(
                        "SELECT id FROM pages_liked WHERE user_id=".$_SESSION["id"]." AND like_id=".$profile["id"]
                    )->num_rows == 1) : false;

    ?>
        <div class = "mid_content" style="text-align: initial;">
        <div id = "profile">
            <a href= "<?= $GLOBALS['global_params']['root_public'] ?>page/public/public_page.php?user=<?= str_replace("%", "+", urlencode($profile["public_name"])) ?>">
            <img class="profile_img_profile" src="<?= getImagePath( $profile["public_image"])  ?>">
            </a>
            <div class="info_profile">
                <span                       class="profile_nickname" >Nom: <?=      htmlentities($profile["public_name"])?></span>
                <span                       class="profile_titre"    >Titre: <?=    htmlentities($profile["title"])?></span>
                <span                       class="profile_espece"   >Espece: <?=   htmlentities($profile["specie"])?></span>
                <span                       class="profile_classe"   >Classe: <?=   htmlentities($profile["class"])?></span>
                <span id="profile_likes"    class="profile_nlikes"   >Likes: <?=    htmlentities($profile["likes"])?></span>

                <?php if ($_SESSION["connected"] && $profile["id"] != $_SESSION["id"]) { ?>
                    <button id="friend_add_btn" class="btn_button_btn btn_friend_profile_add <?= $page_liked ? "btn_friend_profile_add_dislike" : "btn_friend_profile_add_like" ?>" 
                    onclick="togglePageLike();"><?= $page_liked ? "Ne plus suivre" : "Suivre" ?></button>
                <?php } ?>

            </div>
        </div>
            <?php
            if($_SESSION["connected"] && $_SESSION["id"] === $profile["id"]){
                ?>
                    <div class="desc_container">
                        <textarea id="description" class="post_add" name="desc" style="font-size: 18px;" placeholder="<?= trim(htmlentities($profile["description"]))?>" rows="2" maxlength="50"></textarea><br>
                        <button class="submit_add" onclick='updateDesc();'>Changer</button>
                    </div>
                    <div id="container_add">
                        <textarea id="post_content" class="post_add" name="post_content" placeholder="Quel sera votre nouveau post?" rows="5" maxlength="735"></textarea><br>
                        <button class="submit_add" onclick="postAdd();">Poster</button>
                        <button id="inspirate" onclick="inspiration();">Inspiration</button>
                    </div>
            <?php
            } else if (strlen($profile["description"]) > 0){
            ?>
                <div class="container_desc border" style="border-radius: 15px">
                    <p style="color: white; font-size: 18px; margin-top: 0px; margin-bottom: 0px;"><?= htmlentities(trim($profile["description"]))?></p>
                </div>
            <?php
            }
        ?>

    </div>
        <?php

}

////////////////////////////////////////////////
// POSTS

function post_add(){
    ?>
    <div class = "mid_content" style="text-align: initial;">
        <div id="container_add">
            <textarea id="post_content" class="post_add" name="post_content" placeholder="Quel serait votre nouveau post?" rows="5" maxlength="735"></textarea><br>
            <button class="submit_add" onclick="postAdd();">Poster</button>
            <button id="inspirate" onclick="inspiration();">Inspiration</button>
        </div>
    </div>
    <?php
}

function post_bloc($post, $admin_option = false){
    $connexion = $GLOBALS["connexion"];

    $like = false;
    $reported = false;
    if ($_SESSION["connected"])
    {
        $like_query = "SELECT * FROM likes WHERE user_id=".$_SESSION["id"]." AND message_id=".$post["id"].";";
        if ($connexion->query($like_query)->num_rows != 0) {
            $like = true;
        }

        $report_query = "SELECT * FROM reports WHERE user_id=".$_SESSION["id"]." AND message_id=".$post["id"].";";
        if ($connexion->query($report_query)->num_rows != 0) {
            $reported = true;
        }
    }

    ?>
    
    <div id = "post_id_<?= $post["id"] ?>" class="mid_content" style="text-align: initial;">
    <div class="posts">

        <!-- USER -->
        <a href="<?= $GLOBALS['global_params']['root_public'] ?>page/public/public_page.php?user=<?= urlencode($post["public_name"]) ?>">

            <img class="profile_img_posts" src="<?= getImagePath( $post["public_image"])  ?>">
        
        </a>

        <div class="info_containt border" style="border-radius: 15px; padding: 10px 10px;">
            <a href="<?= $GLOBALS['global_params']['root_public'] ?>page/public/public_page.php?user=<?= urlencode($post["public_name"]) ?>">
                <span class="post_auteur" style="color: white; font-size: 20px"><?= htmlentities($post["public_name"]) ?></span><br>
                <span class="post_date" style="color: lightgray; font-size: 14px"><?= htmlentities(date('d/m/Y H:i', $post["creation_date"])); ?></span>
            </a>
            
            <?php
            if($_SESSION["connected"] && ($_SESSION["id"] === $post["user_id"] || $admin_option)) {
            ?>
                <div class="post_menu">
                    <button class="btn_menu_post">&#8226;&#8226;&#8226;</button>
                    <div class="supp_post border">
                    <?php if ($admin_option) { ?>
                        <button class="btn_ignr_post" onclick="ignoreReport('<?= htmlentities($post['id']) ?>');">Ignorer</button>
                        <button class="btn_sup_post" onclick="removeReportedPost('<?= htmlentities($post['id']) ?>');">Supprimer</button>
                        <button class="btn_def_ban_user" onclick="banDefinitif('<?= htmlentities($post['user_id']) ?>');">Ban définitif</button>
                        <button class="btn_tmp_ban_user" onclick="showTempBanBlock('<?= htmlentities($post['user_id']) ?>');">Ban temporaire</button>
                    <?php } else  { ?>
                        <button class="btn_sup_post" onclick="removePost('<?= $post['id'] ?>');">Supprimer</button>
                    <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>

        <!-- CONTENT -->
        <div class="post_content border">
            <p style="color: white; font-size: 18px"><?= htmlentities($post["content"]) ?></p>
        </div>

        <!-- INTERACT -->
        <button id="btn_like_id_<?= $post["id"]?>" 
            class="btn_like btn_button_btn" 
            <?= $_SESSION["connected"] ? "onclick=\"likeSystemPost('".$post['id']."');\"" : ""?> 
            >

            <img 
                id="img_like_<?= $post["id"]?>" 
                class="like_img" width="32" height="32" 
                src="<?= $GLOBALS["global_params"]["root_public"]."assets/image/". ($like ? "liked": "like") .".png"?>"
                >
            <span id="like_id_<?= $post["id"] ?>" class="like_num"><?= $post["like_num"] ?></span>
        </button>

        <div class="post_btn_espace" style="grid-area: post_btn_espace;"></div>

        <dfn title="<?= $reported ? "Vous avez deja signaler" : "Voulez-vous signaler?" ?>">
            <div class="btn_report">
            <button 
                id="btn_report_id_<?= $post["id"]?>" class="report_ref btn_button_btn"
                <?= $_SESSION["connected"] ? "onclick=\"reportSystemPost('".$post['id']."');\"" : ""?>
                >
                
                <img 
                    id="img_report_like_<?= $post["id"]?>" class="report_img" width="32" height="32" 
                    src="<?= $GLOBALS["global_params"]["root_public"]."assets/image/". ($reported ? "reported": "report") .".png"?>"
                    >
            </button>
            </div>
        </dfn>
            
    </div>
    </div>

    <?php
}

?>