<?php

require_once dirname(__FILE__)."/src/Controller.php";

$api = new Controller("test");
//$api = new Controller("dev");
//$api = new Controller("prod");
$api->processApi();

?>