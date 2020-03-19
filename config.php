<?php
// db configurations ...
/*
 * notes :
 * avoid using root user directly
 * avoid easy passwords
 * this config file must be out of document root and with read permission only
 */

define("HOST","127.0.0.1");
define ("USER","api");
define ("PASSWORD","{your password}");
define ("DATABASE" , "users");
define ("PORT","3306");


// only for facility declaring a CONFIG variable as an array having all the configurations
define("DB_CONFIG",array("host"=>HOST , "user"=>USER , "password"=>PASSWORD ,
    "database"=>DATABASE , "port"=>PORT));
