function removeMatch(username) {
            
    let friendBloc = document.getElementById("friend_bloc_"+username);

    let data = new FormData();
    data.append("public_name", username);
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
                if (!feedback["isLiked"]) friendBloc.parentNode.removeChild(friendBloc);

        }
    };
}