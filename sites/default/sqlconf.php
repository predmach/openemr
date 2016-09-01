<?php
//  OpenEMR
//  MySQL Config

$host	= 'localhost';
$port	= '3306';
$login	= 'root';
$pass	= 'root';
$dbase	= 'openemr';
//echo "**********************DATABASE CONFIG***********\n";
$envVars = [
  "DB_PORT_3306_TCP_PORT",
  "DB_PORT_3306_TCP_ADDR",
  "DB_ENV_MYSQL_USER",
  "DB_ENV_MYSQL_DATABASE",
  "DB_ENV_MYSQL_PASSWORD",
  "DB_ENV_MYSQL_ROOT_PASSWORD",
  "MYSQL_ROOT_PASSWORD",
];

/*foreach ($envVars as $envVar) {
  $envValue = getenv($envVar);
  echo nl2br("<b>{$envVar}</b>: {$envValue}\n");
}*/
if(!empty(getenv("DB_PORT_3306_TCP_ADDR"))){
  $host = getenv("DB_PORT_3306_TCP_ADDR");
}
//echo $host."\n";
//echo $login."\n";
//echo $pass."\n";
//echo $dbase."\n";
//echo "**********************DATABASE CONFIG***********\n";
//Added ability to disable
//utf8 encoding - bm 05-2009
global $disable_utf8_flag;
$disable_utf8_flag = false;

$sqlconf = array();
global $sqlconf;
$sqlconf["host"]= $host;
$sqlconf["port"] = $port;
$sqlconf["login"] = $login;
$sqlconf["pass"] = $pass;
$sqlconf["dbase"] = $dbase;
//////////////////////////
//////////////////////////
//////////////////////////
//////DO NOT TOUCH THIS///
$config = 1; /////////////
//////////////////////////
//////////////////////////
//////////////////////////
?>
