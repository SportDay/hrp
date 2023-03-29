function ignoreReport(post_id) {
    let postBlock = document.getElementById("post_id_"+post_id);

    let data = new FormData();
    data.append("post_id", post_id);
    data.append("token_id", token_id);

    let xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST',root_public+"assets/script/php/ignore_post.php");
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

function removeReportedPost(post_id) {
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

function banDefinitif(user_id) {

    let data = new FormData();
    data.append("user_id", user_id);
    data.append("token_id", token_id);

    let xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST',root_public+"assets/script/php/ban_def_user.php");
    xmlhttp.send( data );

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
        {
            //alert(xmlhttp.responseText);
            let feedback = JSON.parse(xmlhttp.responseText);

            if (feedback["success"])
                document.location.reload();

        }
    }
}

function showTempBanBlock(user_id){
    document.getElementById('tmp_ban').style.display='block';
    document.getElementById('ban_btn').setAttribute( "onClick", "banTemporaire("+user_id+");");
}

function hideTempBanBlock(){
    document.getElementById('tmp_ban').style.display='none'
    document.getElementById("ban_btn").removeAttribute("onClick");
}

function banTemporaire(user_id) {

    let checked;
    let radios = document.getElementsByName('time');

    for (let i = 0; i < radios.length; i++) {
        if (radios[i].checked) {
            checked = radios[i].value;
            break;
        }
    }

    let data = new FormData();
    data.append("user_id", user_id);
    data.append("time", document.getElementById("time_input").value);
    data.append("type", checked);
    data.append("token_id", token_id);

    let xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST',root_public+"assets/script/php/ban_tmp_user.php");
    xmlhttp.send( data );
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
        {
            //alert(xmlhttp.responseText);
            let feedback = JSON.parse(xmlhttp.responseText);

            if (feedback["success"])
                document.location.reload();
        }
    }
}

window.onclick = function(event) {
    if (event.target == document.getElementById('tmp_ban')) {
        document.getElementById('tmp_ban').style.display = "none";
        document.getElementById("ban_btn").removeAttribute("onClick");
    }
}