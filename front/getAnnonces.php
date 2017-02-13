<?php

include("../connBD.php");

$json = from_table_to_json($annonces->find());

echo $json;

?>