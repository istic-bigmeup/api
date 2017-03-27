<?php

include("../connBD.php");

echo from_table_to_json($documents->find());