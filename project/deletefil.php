<?php
    session_start();
    $db = mysqli_connect('localhost', 'root', '', 'pro1');
    $filid = $_GET['filid'];
    $query = "DELETE FROM filter
              WHERE filid = '$filid'";
    mysqli_query($db, $query);
    header('location: myfilter.php');
 ?>
