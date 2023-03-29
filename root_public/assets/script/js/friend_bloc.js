function removeFriend(username) {
    let friendBloc = document.getElementById("friend_bloc_"+username);

    let data = new FormData();
    data.append("username", username);
    data.append("token_id", token_id);

    let xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST',root_public+"assets/script/php/remove_friend.php");
    xmlhttp.send( data );

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
        {
            //alert(xmlhttp.responseText);
            let feedback = JSON.parse(xmlhttp.responseText);

            if (feedback["success"])
                friendBloc.parentNode.removeChild(friendBloc);

        }
    }
}