<?php $global_params = [
  "root"        => "../../../",
  "root_public" => "../../",
  "title"       => "Rencontres",
  "css_add"     => ["admin.css","friends.css","like.css"],
  "redirect"    => TRUE
];?>
<!-- ------------------------------------------ -->
<?php require($global_params["root"] . "assets/script/php/functions.php"  ); ?>
<?php require($global_params["root"] . "assets/script/php/header.php"); ?>
<!-- ------------------------------------------ -->

<?php
    if (!$_SESSION["enable_public"]) {
        ?> <div class="mid_content" > <?php
        write("You don't have a public profile");
        ?> </div> <?php
        require($global_params["root"] . "assets/script/php/footer.php");
        exit();
    }
?>

<div class = "mid_content">
    <div class="img_btn_like">
        <button class="like btn_button_btn" onclick="matchChoice(true)" 
        >Suivre</button>
        
        <a href=""> <img
            id = "image_profile"
            class="img_profile border" 
            width="128" height="128" 
            src="<?= $global_params["root_public"] . "assets/profile/default.png"?>" 
            > </a>
        
        <button class="dislike btn_button_btn" onclick="matchChoice(false)" 
        >Ne pas suivre</button>
    
    </div>

    <div class="info_profile border">
        <span id = "name"   class="profile_nickname">Nom: </span>
        <span id = "title"  class="profile_titre"   >Titre: </span>
        <span id = "specie" class="profile_espece"  >Espece: </span>
        <span id = "class"  class="profile_classe"  >Classe: </span>
    </div>
    
    <div id="desc" class="border">
        Description
    </div>
</div>

<script>

    // init
    var likeToken   = "none";

    var otp_image   = document.getElementById("image_profile");
    var otp_name    = document.getElementById("name");
    var otp_title   = document.getElementById("title");
    var otp_specie  = document.getElementById("specie");
    var otp_class   = document.getElementById("class");
    var otp_desc    = document.getElementById("desc");

    matchChoice(false);
    
    //////////////////////////

    function matchChoice(isLike) {
        let data = new FormData();
        data.append("token_id",     token_id);
        data.append("like_token_1", likeToken);
        data.append("isLike",       isLike ? "true" : "false");

        let xmlhttp = new XMLHttpRequest();
        xmlhttp.open('POST',root_public+"assets/script/php/match_choice.php");
        xmlhttp.send( data );

        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState === 4)
                if (xmlhttp.status === 200)
                {
                    //alert(xmlhttp.responseText);
                    const feedback = JSON.parse(xmlhttp.responseText);
                    
                    if (feedback["success"])
                    {
                        otp_image           .src  = "<?=$global_params["root_public"] . "assets/profile/"?>" + feedback["image"];
                        otp_image.parentNode.href = "<?=$global_params["root_public"] . "page/public/public_page.php?user="?>" + encodeURI(feedback["name"]).replaceAll("%20", "+");

                        otp_name    .innerHTML  = "Nom: "    + feedback["name"];
                        otp_title   .innerHTML  = "Titre: "  + feedback["title"];
                        otp_specie  .innerHTML  = "Espece: " + feedback["specie"];
                        otp_class   .innerHTML  = "Classe: " + feedback["class"];
                        otp_desc    .innerHTML  = feedback["desc"].length > 0 ? feedback["desc"] : "Sans description.";

                        likeToken = feedback["like_token_1"];
                    }
                    else
                    {
                        if (feedback["error"] == "token_error")
                            window.open(window.location.href, "_self");
                    }
                }
        }
    }

</script>

<!-- ------------------------------------------ -->
<?php require($global_params["root"] . "assets/script/php/footer.php"); ?>