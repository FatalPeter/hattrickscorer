<?php
//dati di configurazione del database
//produzione
$db = mysqli_connect("localhost", "scorer", "6SgTBg8ypsZ2LRjvsOul", "hattrickscorer");


if (!$db) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}

?>
