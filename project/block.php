<?php
  session_start();

  if (!isset($_SESSION['username'])) {
  	$_SESSION['msg'] = "You must log in first";
  	header('location: login.php');
  }

  // connect to the database
  $db = mysqli_connect('localhost', 'root', '', 'pro1');
  if (isset($_SESSION['username'])) {
      $username = $_SESSION['username'];
      $query = "SELECT uid
                FROM user
                WHERE user.uusername='$username'";
      $result = mysqli_query($db, $query);
      if ($result->num_rows > 0) {
          $row = mysqli_fetch_assoc($result);
          $myuid = $row['uid'];
      }
      $frienduid = $_GET['uid'];

      $query = "UPDATE friendship SET fstate='block'
                where uid='$frienduid' AND other='$myuid'";
      mysqli_query($db, $query);
      $query = "UPDATE friendship SET fstate='block'
                where uid='$myuid' AND other='$frienduid'";
      mysqli_query($db, $query);
      header("location: myfriend.php");
  }
?>
