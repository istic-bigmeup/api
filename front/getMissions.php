<?php

include("../connBD.php");

$json = from_table_to_json($missions->find());

echo $json;
?>