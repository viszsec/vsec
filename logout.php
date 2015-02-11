<?php
    include_once __DIR__ .'js/csrfprotector.php';

    session_start();

    session_destroy();

    echo '<meta http-equiv="refresh" content="0;url=index.php">';

?>
