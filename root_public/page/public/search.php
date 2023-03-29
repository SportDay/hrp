<?php $global_params = [
    "root"        => "../../../",
    "root_public" => "../../",
    "title"       => "Coeur de poudlard",
    "css_add"     => ["public_page.css"],
    "redirect"    => FALSE
];?>
<!-- ------------------------------------------ -->
<?php require($global_params["root"] . "assets/script/php/functions.php"  ); ?>
<?php require($global_params["root"] . "assets/script/php/header.php"); ?>
<!-- ------------------------------------------ -->
<?php 
    
    search_bar();

    /////////////////////

    $connexion = makeConnection(3);

    if(!isset($_GET["search"]))
        $_GET["search"] = "Voldemort";

    $search_profiles = $_SESSION["connected"] ? 
        $connexion->query("select * from ( ".
                " (SELECT id as poster FROM `users` WHERE (( public_name LIKE \"%".$connexion->real_escape_string($_GET["search"])."%\" ) OR ( description LIKE \"%".$connexion->real_escape_string($_GET["search"])."%\" )))".
                " EXCEPT (SELECT user_id_1 as poster FROM `friends` WHERE (user_id_0=".$connexion->real_escape_string($_SESSION["id"])." AND accepted))".
                " EXCEPT (SELECT user_id_0 as poster FROM `friends` WHERE (user_id_1=".$connexion->real_escape_string($_SESSION["id"])." AND accepted))".
                " EXCEPT (SELECT id as poster FROM `users` WHERE (id=".$connexion->real_escape_string($_SESSION["id"])."))".
                " EXCEPT (SELECT user_id as poster FROM `pages_liked` WHERE (user_id=".$connexion->real_escape_string($_SESSION["id"]).")) )".
                " as t1 inner join users on (t1.poster=users.id)".
                " ORDER BY likes DESC LIMIT 30;"
    ) : $connexion->query(
        "select * from ( ".
        " SELECT id as poster FROM `users` WHERE (".
            "( public_name LIKE \"%".$connexion->real_escape_string($_GET["search"])."%\" )".
            " OR ".
            "( description LIKE \"%".$connexion->real_escape_string($_GET["search"])."%\" )".
            ")".
        ")".
        " as t1 inner join users on (t1.poster=users.id)".
        " ORDER BY likes DESC LIMIT 30;"
    );

    if ($search_profiles->num_rows==0)
    { ?>
        <div class="mid_content">
            <p>Aucun resultat.</p>
        </div>
    <?php }

    while($search_profile=$search_profiles->fetch_assoc()) {
        search_profil($search_profile);
    }

    mysqli_close($connexion);

?>
<!-- ------------------------------------------ -->
<?php require($global_params["root"] . "assets/script/php/footer.php"); ?>