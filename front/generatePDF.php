<?php

if(isset($_GET["action"])) {
    echo "cd ../../pdf_generator;java -jar pdf_generator.jar " . $_GET["action"];
    echo "<br/>";

    echo system("touch ../../pdf_generator/tamer");
    echo system("cd ../../pdf_generator;java -jar pdf_generator.jar " . $_GET["action"]);

    echo "<br/>";
    echo "ok";
} else {
    echo "NO";
}

?>