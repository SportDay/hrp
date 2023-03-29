function updateDesc() {
    let textZone = document.getElementById("description");

    let data = new FormData();
    data.append("new_desc", textZone.value);
    data.append("token_id", token_id);

    let xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST',root_public+"assets/script/php/change_desc.php");
    xmlhttp.send( data );

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
        {
            //alert(xmlhttp.responseText);
            let feedback = JSON.parse(xmlhttp.responseText);

            if (feedback["success"])
                document.location.reload();

        }
    };
}

function togglePageLike() {
    let button  = document.getElementById("friend_add_btn");
    let nLikes  = document.getElementById("profile_likes");

    let data = new FormData();
    data.append("public_name", GET("user").replaceAll("+", " "));
    data.append("token_id", token_id);
    
    let xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST',root_public+"assets/script/php/toggle_page_like.php");
    xmlhttp.send( data );
    
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
        {
            //alert(xmlhttp.responseText);
            let feedback = JSON.parse(xmlhttp.responseText);

            if (feedback["success"])
            {
                if (feedback["isLiked"]) {
                    button.classList.add   ("btn_friend_profile_add_dislike");
                    button.classList.remove("btn_friend_profile_add_like");
                    button.innerHTML="Ne plus suivre";
                } else {
                    button.classList.remove("btn_friend_profile_add_dislike");
                    button.classList.add   ("btn_friend_profile_add_like");
                    button.innerHTML="Suivre";
                }

                nLikes.innerHTML = "Likes: " + feedback["nLikes"];
            }

        }
    };
}