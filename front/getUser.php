<?php

include("../connBD.php");

if(isset($_GET["id"])) {
    $id = $_GET["id"];
    $query = array('_id' => new MongoId($id));

    echo from_table_to_json($users->find($query));
}

?>