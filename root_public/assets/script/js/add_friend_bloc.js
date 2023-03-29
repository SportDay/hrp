function acceptFriend(friend) {
    let friendBlocs = document.getElementById("friend_blocs_area");
    let requestBloc = document.getElementById("friend_bloc_" + friend);

    let data = new FormData();
    data.append("username", friend);
    data.append("from_root", root_public);
    data.append("token_id", token_id);

    let xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST',root_public+"assets/script/php/accept_friend.php");
    xmlhttp.send( data );

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200) 
        {
            //alert(xmlhttp.responseText);
            const feedback = JSON.parse(xmlhttp.responseText);
            
            if (feedback["success"])
            {
                parentBloc = requestBloc.parentNode;

                if (parentBloc.childElementCount < 3)
                    parentBloc.parentNode.removeChild(parentBloc);
                else
                    parentBloc.removeChild(requestBloc);

                friendBlocs.innerHTML = feedback["html"] + friendBlocs.innerHTML;
            }
        }
    }
}

function refuseFriend(friend) {
    let requestBloc = document.getElementById("friend_bloc_" + friend);

    let data = new FormData();
    data.append("username", $friend);
    data.append("token_id", token_id);

    let xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST',root_public+"assets/script/php/refuse_friend.php");
    xmlhttp.send( data );

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
        {
            //alert(xmlhttp.responseText);
            let feedback = JSON.parse(xmlhttp.responseText);
            
            if (feedback["success"])
            {
                requestBloc.parentNode.removeChild(requestBloc);
                let elements_list = document.getElementById("friends_request_list");
                if(elements_list.children.length === 1){
                    elements_list.parentNode.removeChild(elements_list);;
                }
            }
        }
    }
}