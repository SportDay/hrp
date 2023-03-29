<?php $global_params = [
  "root"        => "../../../",
  "root_public" => "../../",
  "title"       => "Accueil",
  "css_add"     => ["posts.css"],
  "redirect"    => FALSE
];?>
<!-- ------------------------------------------ -->
<?php require($global_params["root"] . "assets/script/php/functions.php"  ); ?>
<?php require($global_params["root"] . "assets/script/php/header.php"); ?>
<!-- ------------------------------------------ -->
<?php search_bar(); ?>

<div class = "mid_content">

  <section>
    <h1>Harry Potter | Role Play</h1>
    <?php separator(); ?>
    <p>
        Bienvenue sur la communauté n°1 en france de jeux de role autour de l'univers d'Harry Potter. <br>
        Enfilez votre cap et votre chapeau, attrapez une baguette. Une grande aventure en temps réel vous attend. <br>
    </p>
  </section>
  <?php separator(); ?>
  <section id="section_fonctionnement">
    <h2>Fonctionnement du site.</h2>
    <p>
        Chaque compte est constitué d'une partie privé. <br>
        Depuis la partie privé vous pouvez ajouter des amis privés et échanger des messages privés avec eux. <br>
        
        Mais il est aussi possible d'activer une page publique et de commencer une aventure. <br>
        Cette page publique sera généré aléatoirement. <br>
        Un rôle vous sera assigné et vous devrez vous y conformer. <br>
        Chaque page publique est munie d'une génération personnalisé de posts. <br>
        
        Des comptes pour interargir vous seront proposés. (Nouvelles Rencontres) <br>
        Si deux comptes acceptent mutuellements d'interargir, <br>
        alors ils peuvent à s'echanger des messages dans un espace prévu à cette effet. <br>
        
        Remarque: Si votre page publique vous pose problème, il est possible de la fermer et d'en générer une. <br>
    </p>
    <?php separator(); ?>
    <h2>Propriétés imposées de votre page publique.</h2>
    <p>Avatar, Nom, Espèce, Classe, Titre</p>
    
    <div class="centered_ul"> <ul>
      <li>Espèce : Sorcier, Moldu, Elf, Esprit</li>
      <li>Classe : Elève, Professeur, Les Forces du Mal, L'ordre du Phénix, ...</li>
      <li>Titre  : « Gryffondor », « Poufsouffle », « Serdaigle », « Serpentard », ..</li>
    </ul> </div>
  </section>


  
  
</div>
<!-- ------------------------------------------ -->
<?php require($global_params["root"] . "assets/script/php/footer.php"); ?>