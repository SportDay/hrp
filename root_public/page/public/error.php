<?php $global_params = [
    "root"        => "../../../",
    "root_public" => "../../",
    "title"       => "Vous êtes perdu?",
    "redirect"    => FALSE
];?>
<!-- ------------------------------------------ -->
<?php require($global_params["root"] . "assets/script/php/functions.php"  ); ?>
<?php require($global_params["root"] . "assets/script/php/header.php"); ?>
<!-- ------------------------------------------ -->
<div class = "mid_content">
    <?php separator();?>
    <p>Revenez dans quelques instants ou revenez à la page d'accueil.</p>
    <button class="btn_button_btn" onclick="openPage('public/home_page.php');">Revenir a l'accueil</button>
    <?php separator();?>
</div>
<!-- ------------------------------------------ -->
<?php require($global_params["root"] . "assets/script/php/footer.php"); ?>