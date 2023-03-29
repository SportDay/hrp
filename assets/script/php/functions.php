<?php

    ////////////////////////
    // Fonctions de mise en page

    function write($text){
        echo "<p>$text</p>";
    }

    function separator($length = 15, $char = '-'){
        echo "<p>";
        for ($i = 0; $i < $length; $i++)
            echo $char;
        echo "</p>";
    }

    function padding($length = 10) {
        echo "<p>";
        for ($i = 0; $i < $length; $i++)
            echo "<br>";
        echo "</p>";
    }

    function newline_split($str) {
        // utilise ça si tu dois split par ligne, trop de problème d'encodage sinon
        return preg_split("/\r\n|\n|\r/", $str);
    }

    function newline_for_html($str) {
        return preg_replace("/\r\n|\r|\n/", '<br/>', $str);
    }

    ////////////////////////
    // Debug

    function consoleLog($succes = null, $code = null){
        if($code != null && $succes != null){
            $name = "HRP";
            if($succes === true){
                ?>
                    <script>console.log("<? echo $name . ": $code!";?>");</script>
                <?php
            }else if ($succes === false){
                 ?>
                    <script>console.log("<? echo $name . " ERROR: ". " $code!";?>");</script>
                <?php
            }
        }
    }

    function debug($content) {
        if (isset($GLOBALS["debugging"]) && $GLOBALS["debugging"])
            file_put_contents('debug.txt', ($content).PHP_EOL , FILE_APPEND | LOCK_EX);
    }

    //////////////////
    // Utilitaire

    function makeConnection($type = 0) {
        $connexion = mysqli_connect (
            $GLOBALS["DB_URL"],
            $GLOBALS["DB_ACCOUNT"],
            $GLOBALS["DB_PASSWORD"],
            $GLOBALS["DB_NAME"]
        );

        if (!$connexion) { // error manager
            if ($type == 3) {
                ?>
                <div class="mid_content">
                    <?php write("Base de donnée indisponible."); ?>
                </div>
                <?php
                require($GLOBALS["global_params"]["root"] . "assets/script/php/footer.php");
                exit();
            }

            if ($type == 2) 
                return false;

            if ($type == 1)
                write("Base de donnée indisponible."); 
            if ($type == 0)
                echo json_encode([
                    "success"   =>  false,
                    "error"     =>  "Base de donnée indisponible."
                ]);
            
            exit();
        }

        mysqli_set_charset($connexion, "utf8");

        return $connexion;
    }

    //////////////////
    // Fonctions de sécurité (sans effets de bords)

    function isValideName($str) {
        // longueur et charactère
        $str = trim($str);
        if (strlen($str) > 16 || strlen($str) < 2) 
            return false;
        
        return !(preg_match("/^[\w]*$/", $str) === 0); // alphanumérique et tiret underscore
    }

    function isValidePassword ($str) {
        // longueur et charactères
        if (strlen($str) > 26 || strlen($str) < 6) return false;

        if ($GLOBALS["CONSTANTS_CONFIG"]["simple_password"])
            return !(preg_match("/^[A-z!?\-\*\+\(\)\|\[\]@0-9]*$/" , $str) === 0); // alphanumérique et -_+()[]
        
        return // version qui force l'utilisateur à compliquer le mdp
            !(preg_match("/^[A-z!?\-\*\+\(\)\|\[\]@0-9]*$/" , $str) === 0) &&
            !(preg_match("/[A-z]/"                          , $str) === 0) &&
            !(preg_match("/[0-9]/"                          , $str) === 0) &&
            !(preg_match("/!?\-\*\+\(\)\|\[\]@/"            , $str) === 0) ;
    }

    function randomString($length=32) {
        return substr(base64_encode(random_bytes($length)), 0, $length ) ;
    }

    function getImagePath($image, $specific_root=FALSE, $root_public="", $short=false) {
        if (!$specific_root)
            $root_public = $GLOBALS["global_params"]["root_public"];

        $folder     = $root_public                             . "assets/profile/";
        $folderThis = $GLOBALS["global_params"]["root_public"] . "assets/profile/"; 

        if ($short) $folder = "";

        $where = "";

        if ($image < 36)
            $where = "profile_" . str_pad($image, 4, "0", STR_PAD_LEFT) . ".webp";
        else
            $where = "profile_" . str_pad($image, 4, "0", STR_PAD_LEFT) . ".jpg";        

        if (file_exists($folderThis . $where))
            return $folder . $where;

        return $folder . "default.png";
    }

    function verifyToken() {
        if (
            !isset($_SESSION["connected"]) || !$_SESSION["connected"] ||
            !isset($_POST["token_id"]) || !isset($_SESSION["token_id"]) || !isset($_SESSION["token_expire"]) ||
                   $_POST["token_id"]  !=        $_SESSION["token_id"]  || $_SESSION["token_expire"] < time()
        )
        {
            echo json_encode([
                "success" => false,
                "error"   => "token_error"
            ]); 
            
            exit();
        }
    }

    //////////////////////////////////
    // Fonctions relatives au lore

    function generateRandomPublicData() {
        $roles = json_decode( file_get_contents(
            $GLOBALS["global_params"]["root"] . "assets/rp_data/procedural_role.json"
        , true) );

        /////////////////////////////////////

        $connexion = makeConnection();
        if ($connexion === "false") return json_encode([
            "success"   =>  false,
            "error"     =>  "database is not availible"
        ]);

        /////////////////////////////////////

        // GENERATE NAME
        $firstname   = $roles->{"public_name"}->{"FirstName"}
        [rand(0, count($roles->{"public_name"}->{"FirstName"})-1 )];
        
        $lastname    = $roles->{"public_name"}->{"LastName" }
        [rand(0, count($roles->{"public_name"}->{"LastName" })-1 )];

        $public_name  = $firstname . " " . $lastname;
        
        for ($i = 0; $i < 100; $i++) {
            if ($i == 99)
            {
                mysqli_close($connexion);
                return ["success" => false];
            }

            $idx = str_pad(rand(0, 999), 3, "0", STR_PAD_LEFT);

            if ($connexion->query(
                "SELECT `id` FROM `users` WHERE public_name=\"" . 
                $connexion->real_escape_string($public_name . " " . $idx) . "\";"
                )->num_rows == 0)
            {
                $public_name .= " " . $idx;
                $i = 100;
            }
        }

        mysqli_close($connexion);

        // GENERATE SPECIE
        $specie =      $roles->{"specie"}
        [rand(0, count($roles->{"specie"})-1 )];

        // GENERATE TITLE
        $title = $roles->{"title"}
        [rand(0, count($roles->{"title"})-1 )];

        // GENERATE IMAGE
        $public_image = rand(
            $specie->{"images"}[0][0], 
            $specie->{"images"}[0][1]
        );

        for ($i = 0; $i < 100; $i++) { // euristic
            // GENERATE CLASS
            $class = $roles->{"class"}[ 
                rand(   0, 
                        count( $roles->{"class"}) - 1
                    )
            ];

            if (
                    ($class->{"images"}[0][0] <= $public_image && $public_image <= $class->{"images"}[0][1]) 
                    || $i == 99 
                ) 
            {
                return [
                    "public_name"   => $public_name,
                    "public_image"  => $public_image,
        
                    "specie"        => $specie->{"name"},
                    "class"         => $class->{"name"},
                    "title"         => $title,

                    "success"       => true
                ];
            }
        }
    }

    function inspirate($class="-1")
    {
        $messages = json_decode(file_get_contents(
            $GLOBALS["global_params"]["root"] . "assets/rp_data/procedural_phrase.json"
            , true));

        /////////////////////////////////////

        $connexion = mysqli_connect (
            $GLOBALS["DB_URL"],
            $GLOBALS["DB_ACCOUNT"],
            $GLOBALS["DB_PASSWORD"],
            $GLOBALS["DB_NAME"]
        );
        if (!$connexion) {
            return "Description";
        }

        if ($class==="-1")
            $class = $connexion->query(
                "SELECT class FROM users WHERE username=\"". $connexion->real_escape_string($_SESSION["username"]) . "\";"
            )->fetch_assoc();
        else
            $class = ["class" => $class];

        $message = $messages->{$class["class"]}
        [rand(0, count($messages->{$class["class"]}) - 1)];

        return $message;
    }
?>