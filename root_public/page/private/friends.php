<?php $global_params = [
  "root"        => "../../../",
  "root_public" => "../../",
  "title"       => "Amis",
  "css_add"     => [
      "posts.css", "public_page.css","admin.css",
      "friends.css","login.css"
    ],
  "redirect"    => TRUE
];?>
<!-- ------------------------------------------ -->
<?php require($global_params["root"] . "assets/script/php/functions.php"  ); ?>
<?php require($global_params["root"] . "assets/script/php/header.php"); ?>
<!-- ------------------------------------------ -->

<!-- Ajout d'amis -->
<div class="add_friend_bar border">
        <div class="request_and_searchbtn_friend">
            <input  id="add_friend_input" type="search" class="search_request_friend_bar search_input" autocomplete="off" placeholder="Pseudo à ajouter">
            <button class="send_request_friend_bar btn_button_btn" onclick="addFriend();">Ajouter en amis</button>
        </div>
        <p id="add_friend_debug" style="display:none">Debug</p>
</div>

<!-- Accepte demande d'amis -->
<?php

    $connexion = makeConnection(3);

    $friends = $connexion->query( // enfin ça fonctionne !!
        "select *
        from
        (
            (SELECT user_id_0 as friend FROM `friends` WHERE (user_id_1=".$_SESSION["id"]." AND NOT accepted))
        ) as t1
        inner join users
        on t1.friend=users.id
        
        ORDER BY last_join DESC
        "
    );

    if ($friends->num_rows!=0)
    { ?>
        <div id="friends_request_list" class="mid_sub_content_friend border">
            <h3 style="margin: 0;">Demandes d'amis en attente</h3>
            <?php while($friend=$friends->fetch_assoc()) add_friend_bloc($friend); ?>
        </div>
    <?php }
?>

<!-- Liste d'amis -->

    <div id="friend_blocs_area" >
    <?php
        $friends = $connexion->query( 
            "select *
            from
            (
                (SELECT user_id_1 as friend FROM `friends` WHERE (user_id_0=".$_SESSION["id"]." AND accepted))
                    UNION 
                (SELECT user_id_0 as friend FROM `friends` WHERE (user_id_1=".$_SESSION["id"]." AND accepted))
            ) as t1
            inner join users
            on t1.friend=users.id
            
            ORDER BY last_join DESC
            "
        );

        if ($friends->num_rows==0)
        { ?>
            <div class="mid_content">
                <p>Vous n'avez pas d'amis.</p>
            </div>
        <?php }

        while($friend=$friends->fetch_assoc())
            friend_bloc($friend);

        mysqli_close($connexion);
    ?>
    </div>

<!-- ------------------------------------------ -->
<?php require($global_params["root"] . "assets/script/php/footer.php"); ?>

<script>
        function addFriend() {
            let requestFriend = document.getElementById("add_friend_input");
            let debugHtml     = document.getElementById("add_friend_debug");

            let data = new FormData();
            data.append("username", requestFriend.value);
            data.append("token_id", token_id);

            let xmlhttp = new XMLHttpRequest();
            xmlhttp.open('POST',root_public+"assets/script/php/add_friend.php");
            xmlhttp.send( data );

            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
                {
                    //alert(xmlhttp.responseText);
                    const feedback = JSON.parse(xmlhttp.responseText);
                    
                    requestFriend.value = "";
                    debugHtml.style.display="block";
                    debugHtml.innerHTML = feedback["error"];
                }
            }
        }
</script>
<script type="text/javascript" src="../../assets/script/js/friend_bloc.js"></script>
<script type="text/javascript" src="../../assets/script/js/add_friend_bloc.js"></script>