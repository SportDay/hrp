function removePost(post_id) {
    let postBlock = document.getElementById("post_id_"+post_id);

    let data = new FormData();
    data.append("post_id", post_id);
    data.append("token_id", token_id);

    let xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST',root_public+"assets/script/php/remove_post.php");
    xmlhttp.send( data );

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
        {
            //alert(xmlhttp.responseText);
            let feedback = JSON.parse(xmlhttp.responseText);

            if (feedback["success"])
                postBlock.parentNode.removeChild(postBlock);

        }
    }
}

function likeSystemPost(post_id) {
    let data = new FormData();
    data.append("post_id", post_id);
    data.append("token_id", token_id);

    let xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST',root_public+"assets/script/php/like_system_post.php");
    xmlhttp.send( data );

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
        {
            //alert(xmlhttp.responseText);
            let feedback = JSON.parse(xmlhttp.responseText);

            if (feedback["success"]) {
                if(feedback["liked"])
                    document.getElementById("img_like_" + post_id).src = root_public+"assets/image/liked.png";
                else
                    document.getElementById("img_like_" + post_id).src = root_public+"assets/image/like.png";
                document.getElementById("like_id_" + post_id).textContent  = feedback["nbr_like"];
            }
        }
    }
}

function reportSystemPost(post_id) {
    let data = new FormData();
    data.append("post_id", post_id);
    data.append("token_id", token_id);

    let xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST',root_public+"assets/script/php/report_system_post.php");
    xmlhttp.send( data );

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
        {
            //alert(xmlhttp.responseText);
            let feedback = JSON.parse(xmlhttp.responseText);

            if (feedback["success"]) {
                if(feedback["report"]) {
                    document.getElementById("img_report_like_" + post_id).src = root_public+"assets/image/reported.png";
                }else{
                    document.getElementById("img_report_like_" + post_id).src = root_public+"assets/image/report.png";
                }
            }

        }
    }
}