function postAdd() {
    let textZone = document.getElementById("post_content");
    if(textZone.value === "") return;
    let data = new FormData();
    data.append("post_content", textZone.value);
    data.append("token_id", token_id);

    let xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST',root_public+"assets/script/php/add_posts.php");
    xmlhttp.send( data );

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
        {
            let feedback = JSON.parse(xmlhttp.responseText);

            if (feedback["success"]) {
                document.location.reload();
            }else{
                textZone.value = feedback["error"];
            }

        }
    }
}
function inspiration() {
    let textZone = document.getElementById("post_content");
    let xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST',root_public+"assets/script/php/inspiration.php");
    xmlhttp.send();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
        {
            //alert(xmlhttp.responseText);
            let feedback = JSON.parse(xmlhttp.responseText);

            if (feedback["success"]) {
                textZone.value = feedback["message"].trim();
            }
        }
    }
}