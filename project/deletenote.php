<?php
    session_start();
    $db = mysqli_connect('localhost', 'root', '', 'pro1');
    $nid = $_GET['nid'];
    $query = "DELETE FROM schedule
              WHERE sid in (SELECT sid
              FROM note
              WHERE nid='$nid')";
    $query = "DELETE FROM tag
              WHERE nid = '$nid'";
    mysqli_query($db, $query);
    $query = "DELETE FROM writes
              where nid = '$nid'";
    mysqli_query($db, $query);
    $query = "DELETE FROM note
              where nid = '$nid'";
    mysqli_query($db, $query);
    header('location: mynote.php');
 ?>
