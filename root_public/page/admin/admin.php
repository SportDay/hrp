<?php $global_params = [
  "root"        => "../../../",
  "root_public" => "../../",
  "title"       => "Admin Panel",
  "css_add"     => ["public_page.css","posts.css","admin.css"],
  "redirect"    => TRUE,
  "admin_req"   => TRUE
];?>
<!-- ------------------------------------------ -->
<?php require($global_params["root"] . "assets/script/php/functions.php"  ); ?>
<?php require($global_params["root"] . "assets/script/php/header.php"); ?>
<!-- ------------------------------------------ -->
<?php

$connexion = makeConnection(3);

$reported_post = $connexion->query("SELECT * FROM posts WHERE reportnum>0 ORDER BY reportnum DESC LIMIT 60");

if ($reported_post->num_rows==0) { ?>
    <div class="mid_content">
        <p>Aucun signalement en attente.</p>
    </div>
<?php }

while($report_post=$reported_post->fetch_assoc())
    post_bloc($report_post, true);

mysqli_close($connexion);

?>

<!-- ------------------------------------------ -->
    <div id="tmp_ban" class="tmp_ban_model">
        <div class="modal-content animate">
            <div class="tmp_ban_form_container">
                <span onclick="hideTempBanBlock();" class="close" title="Fermer">&times;</span>
                <label class="form_title"><b>Ban temporaire</b></label>
                <input id="time_input" type="number" placeholder="Durée de la punition" name="ban_time" min="0" autocomplete="off">
                <div class="ban_radio">
                    <input label="Minute" type="radio" id="male" name="time" value="min">
                    <input label="Heure" type="radio" id="female" name="time" value="hour" checked>
                    <input label="Jour" type="radio" id="other" name="time" value="day">
                    <input label="Mois" type="radio" id="other" name="time" value="month">
                </div>
                <button id="ban_btn" class="ban_user_temp" onclick="">Bannir</button>
                <button id="cancel_ban" class="ban_user_temp" onclick="hideTempBanBlock();" class="cancelbtn">Annuler</button>
            </div>
        </div>
    </div>
<!-- ------------------------------------------ -->
<?php require($global_params["root"] . "assets/script/php/footer.php"); ?>

<script type="text/javascript" src="../../assets/script/js/post_reported_bloc.js"></script>