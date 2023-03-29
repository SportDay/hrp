<?php $global_params = [
  "root"        => "../../../",
  "root_public" => "../../",
  "title"       => "Paramètres",
  "css_add"     => ["params.css"],
  "redirect"    => TRUE
];?>
<!-- ------------------------------------------ -->
<?php require($global_params["root"] . "assets/script/php/functions.php"  ); ?>
<?php require($global_params["root"] . "assets/script/php/header.php"); ?>
<!-- ------------------------------------------ -->
<?php // FUNCTIONS (specific à cette page)

?>
<!-- ------------------------------------------ -->

<!-- Gestion de page publique -->

<div class = "mid_content">
    <h1 class="settings_title">Publique</h1>
    <div class = "mid_sub_content" class="posts_and_user">
        
        <div class="settings_title"><p>Page publique<?= 
            ($_SESSION["enable_public"]) ? (" | " . $_SESSION["public_name"]) : ""
        ?></p></div>
        
        <?php 
            if ($_SESSION["banned"]) { ?>
                <p>Vous êtes bannie de la partie publique du site jusqu'aux: <?= date('H:i d/m/Y', htmlentities(trim($_SESSION["banned_to"]))); ?></p>
            <?php } else {

                if ($_SESSION["enable_public"]) { ?>
                    <button class="btn_valide"      onclick="makePublic();"
                    >Reroll</button>
                    <button class="btn_remove"      onclick="removePublic();"
                    >Supprimer</button>
                <?php } else { ?>
                    <button class="btn_valide"      onclick="makePublic();"
                    >Créer</button>
                    <button class="btn_valide"      onclick="reactivatePublic();" 
                    style="display:<?= $_SESSION["memory_public"] ? "inline" : "none"?>"
                    >Réactiver</button>
                <?php }

            }
        ?>

        <p id="error_public_page" style="display:none">Error</p>
    
    </div>
</div>

<!-- Gestion Compte privé -->

<div class = "mid_content"> <!-- Passwords -->
    <h1 class="settings_title">Privé | <?= $_SESSION["username"] ?> </h1>
    
    <div class = "mid_sub_content">
        <div class="settings_title"><p>Changement du mot de passe</p></div>
        
        <div class="input_title">
            <input type="password" id="change_password_old"  placeholder="Ancien mot de passe"> <br>
            <input type="password" id="change_password_new"  placeholder="Nouveau mot de passe"><br>
            <input type="password" id="change_password_new2" placeholder="Repetez le mot de passe"><br>
        </div>
        <br>
        <p id="error_change_password" style="display:none">Error</p>
        <button class="btn_chg_pass btn_button_btn" onclick="changePassword();"
            >Changer le mot de passe</button>
    </div>

    <div class = "mid_sub_content"> <!-- Remove Account -->
        
        <div class="settings_title"><p>Supprimer le compte</p></div>
        
        <div class="input_title">
            <input type="text"     id="remove_account_username"  placeholder="Pseudo"> <br>
            <input type="password" id="remove_account_password"  placeholder="Mot de passe"><br>
            <input type="password" id="remove_account_password2" placeholder="Repetez le mot de passe"><br>
        </div>
        <br>
        <p id="error_remove_account" style="display:none">Error</p>
        <button class="btn_remove" onclick="removeAccount();" 
                >Supprimer</button>

    </div>

</div>


<!-- ------------------------------------------ -->
<?php require($global_params["root"] . "assets/script/php/footer.php"); ?>

<script>

    // PUBLIC
    
    function makePublic() {
        let error = document.getElementById("error_public_page");

        let data = new FormData();
        data.append("token_id", token_id);
        // je pourrai aussi rajouter un checkup de securité sur la création de ce code
        //////////

        let xmlhttp = new XMLHttpRequest();
        
        xmlhttp.open('POST',root_public+"assets/script/php/make_public.php");
        xmlhttp.send( data );

        xmlhttp.onreadystatechange = function () {
            
            if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
            {
                //alert(xmlhttp.responseText);
                const feedback = JSON.parse(xmlhttp.responseText);

                if (feedback["success"])
                {
                    openProfile(feedback["public_name"]);
                    //window.open( feedback["newPage"], "_self");
                }
                else {
                    error.innerHTML = feedback["error"];
                    error.style.display = "block";
                }
            }
            else
            {
                error.innerHTML = "Erreur de connection serveur: " + xmlhttp.status;
                error.style.display = "block";
            }
        }
    }

    function removePublic() {
        let error = document.getElementById("error_public_page");

        let data = new FormData();
        data.append("token_id", token_id);
        //////////

        let xmlhttp = new XMLHttpRequest();
        
        xmlhttp.open('POST',root_public+"assets/script/php/remove_public.php");
        xmlhttp.send( data );

        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
            {
                //alert(xmlhttp.responseText);
                const feedback = JSON.parse(xmlhttp.responseText);

                if (feedback["success"])
                    window.open(window.location.href.split('?')[0], "_self");
                else {
                    error.innerHTML = feedback["error"];
                    error.style.display = "block";
                }
            }
            else
            {
                error.innerHTML = "Erreur de connection serveur: " + xmlhttp.status;
                error.style.display = "block";
            }
        }
    }

    function reactivatePublic() {
        let error = document.getElementById("error_public_page");

        let data = new FormData();
        data.append("token_id", token_id);
        //////////

        let xmlhttp = new XMLHttpRequest();
        xmlhttp.open('POST',root_public+"assets/script/php/reactivate_public.php");
        xmlhttp.send( data );

        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
            {
                //alert(xmlhttp.responseText);
                const feedback = JSON.parse(xmlhttp.responseText);

                if (feedback["success"])
                    openProfile(feedback["public_name"]);
                else {
                    error.innerHTML = feedback["error"];
                    error.style.display = "block";
                }
            }
            else
            {
                error.innerHTML = "Erreur de connection serveur: " + xmlhttp.status;
                error.style.display = "block";
            }
        }
    }

    // PRIVATE

    function changePassword() {
        let error         = document.getElementById("error_change_password");
        let old_password  = document.getElementById("change_password_old");
        let new_password  = document.getElementById("change_password_new");
        let new_password2 = document.getElementById("change_password_new2");

        let data = new FormData();
        data.append("token_id", token_id);
        data.append("old_password",  old_password.value);
        data.append("new_password",  new_password.value);
        data.append("new_password2", new_password2.value);
        //////////

        let xmlhttp = new XMLHttpRequest();

        xmlhttp.open('POST',root_public+"assets/script/php/change_password.php");
        xmlhttp.send( data );


        xmlhttp.onreadystatechange = function () {
            let DONE = 4; // readyState 4 means the request is done.
            let OK = 200; // status 200 is a successful return.
            
            if (xmlhttp.readyState === DONE)
                if (xmlhttp.status === OK)
                {
                    old_password .value = "";
                    new_password .value = "";
                    new_password2.value = "";
                    
                    const feedback = JSON.parse(xmlhttp.responseText);
                    
                    if (feedback["success"]) {
                        error.innerHTML = "Le mot de passe a été mis à jour."
                        error.style.display = "block";
                        //window.open( windd , "_self" );
                    }
                    else {

                        error.innerHTML = feedback["error"];
                        error.style.display = "block";
                    }
                }
                else
                {
                    error.innerHTML = "Erreur de connection serveur: " + xmlhttp.status;
                    error.style.display = "block";
                }
        }
    }

    function removeAccount() {
        let error     = document.getElementById("error_remove_account");
        let username  = document.getElementById("remove_account_username");
        let password  = document.getElementById("remove_account_password");
        let password2 = document.getElementById("remove_account_password2");

        let data = new FormData();
        data.append("token_id", token_id);
        data.append("username",  username.value);
        data.append("password",  password.value);
        data.append("password2", password2.value);
        //////////

        let xmlhttp = new XMLHttpRequest();

        xmlhttp.open('POST',root_public+"assets/script/php/remove_account.php");
        xmlhttp.send( data );

        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
            {
                username .value = "";
                password .value = "";
                password2.value = "";

                //alert(xmlhttp.responseText);
                const feedback = JSON.parse(xmlhttp.responseText);

                if (feedback["success"])
                    openPage('public/home_page.php');
                else {
                    error.innerHTML = feedback["error"];
                    error.style.display = "block";
                }
            }
            else
            {
                error.innerHTML = "Erreur de connection serveur: " + xmlhttp.status;
                error.style.display = "block";
            }
        }
    }

</script>