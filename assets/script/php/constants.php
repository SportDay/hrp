<?php

  /*

      CONSTANTS :
      Ce fichier contient un ensemble de constantes utiles à chaque endroit du site.

  */

  $CONSTANTS_CONFIG = parse_ini_file($global_params["root"] . "assets/config/config.ini", false);

  date_default_timezone_set($CONSTANTS_CONFIG["DEFAULT_TIMEZONE"]);

  $DB_URL       = $CONSTANTS_CONFIG["DB_URL"];
  $DB_ACCOUNT   = $CONSTANTS_CONFIG["DB_ACCOUNT"];
  $DB_NAME      = $CONSTANTS_CONFIG["DB_NAME"];
  $DB_PASSWORD  = $CONSTANTS_CONFIG["DB_PASSWORD"];
  $db_conf      = [ // de base je stockais ça dans un json, et c'était cette variable qui en sortait
    "DB_URL"      => $DB_URL,
    "DB_ACCOUNT"  => $DB_ACCOUNT,
    "DB_NAME"     => $DB_NAME,
    "DB_PASSWORD" => $DB_PASSWORD
  ];

  $TIME_SESS_END        = $CONSTANTS_CONFIG["TIME_SESSION_END"];
  $TIME_SESS_INACTIVE   = $CONSTANTS_CONFIG["TIME_SESSION_INACTIVE"];
  $TIME_COOKIE_CONNECT  = $CONSTANTS_CONFIG["TIME_COOKIE_CONNECT"];

  $COOKIE_PATH = $CONSTANTS_CONFIG["COOKIE_PATH"]; 
  $TIME_REROLL = $CONSTANTS_CONFIG["TIME_REROLL"];

?>