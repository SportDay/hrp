<?php $global_params = [
  "root"        => "../../../",
  "root_public" => "../../",
  "title"       => "Page Public",
  "css_add"     => ["public_page.css", "posts.css"],
  "redirect"    => FALSE // J'hésite à mettre ça en true
];?>

<?php // verification de l'existence de la page

    require_once($global_params["root"] . "assets/script/php/constants.php");
    require_once($global_params["root"] . "assets/script/php/functions.php");

    $connexion = makeConnection(1);

    //////////////////////////////////////////////////////////
    // generation page en fonction du get

    if(!isset($_GET["user"])){
        header("Location: " . $global_params["root_public"] ."page/public/home_page.php");
        exit();
    }

    $user_query = "SELECT * FROM users WHERE public_name=\"". $connexion->real_escape_string($_GET["user"]) . "\" AND enable_public;";
    $page_user = $connexion->query($user_query);

    if($page_user->num_rows == 0)
    {
        header("Location: " . $global_params["root_public"] ."page/public/home_page.php");
        exit();
    }

    require($global_params["root"] . "assets/script/php/header.php");
?>
<!-- ------------------------------------------ -->
<?php search_bar(); ?>
<!-- ------------------------------------------ -->
<?php

    //////////////////
    // generation du bloc profile 
    profile_bloc($page_user = $page_user->fetch_assoc());

    //////////////////
    // ajout des posts

    $posts = $connexion->query(
        "SELECT * FROM posts WHERE user_id=".$page_user["id"]." ORDER BY creation_date DESC;"
    );

    if ($posts->num_rows==0)
    { ?>
        <div class="mid_content">
            <p>Aucun post.</p>
        </div>
    <?php }

    while($post=$posts->fetch_assoc())
        post_bloc($post);
    
    /////////////////////////////
    // fonctions en javascript

    mysqli_close($connexion);
    if($_SESSION["connected"]){ ?>
        <script type="text/javascript" src="../../assets/script/js/profile_bloc.js"></script>
        <script type="text/javascript" src="../../assets/script/js/post_bloc.js"></script>
        <script type="text/javascript" src="../../assets/script/js/post_add.js"></script>
    <?php }

?>
<!-- ------------------------------------------ -->
<?php require($global_params["root"] . "assets/script/php/footer.php"); ?>