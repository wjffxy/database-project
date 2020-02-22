<?php
  session_start();

  if (!isset($_SESSION['username'])) {
  	$_SESSION['msg'] = "You must log in first";
  	header('location: login.php');
  }

  // connect to the database
  $db = mysqli_connect('localhost', 'root', '', 'pro1');
?>
<!DOCTYPE html>
<html>
<body>
  <div class="header">
  	<h2>My Friend</h2>
  </div>
  <div class="content">
      <!-- logged in user information -->
      <?php  if (isset($_SESSION['username'])) :
          $username = $_SESSION['username'];
          $query = "SELECT uid
                    FROM user
                    WHERE user.uusername='$username'";
          $result = mysqli_query($db, $query);
          if ($result->num_rows > 0) {
              $row = mysqli_fetch_assoc($result);
              $myuid = $row['uid'];
          }
          if (isset($_POST['newfriend'])){
              $inputuname = mysqli_real_escape_string($db, $_POST['username']);
              if ($inputuname != $username) {
                  $query = "SELECT uid
                            FROM user
                            WHERE user.uusername='$inputuname'";
                  $result = mysqli_query($db, $query);
                  if ($result->num_rows > 0) {
                      $row = mysqli_fetch_assoc($result);
                      $inputuid = $row['uid'];
                      $query = "INSERT INTO friendship VALUES
                                    ('$myuid', '$inputuid', 'request')";
                      mysqli_query($db, $query);
                  }
              } else {
                  echo "Can't add yourself!";
              }
          }

          $query = "SELECT friendship.uid, user.uusername, user.ufname, user.ulname
                    FROM friendship, user
                    WHERE friendship.other='$myuid' AND
                          friendship.fstate='friend' AND
                          friendship.uid = user.uid";
          $result = mysqli_query($db, $query);
          echo "Friend list: <br>";
          if ($result->num_rows > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                echo "username: ".$row['uusername']." | name: ".$row['ufname']." ".$row['ulname'];
                $address = "block.php?uid=";
                $address .= $row['uid'];
                echo ' | <a href="'.$address.'")">'."Block</a><br>";
            }
          }
          $query = "SELECT friendship.other, user.uusername, user.ufname, user.ulname
                    FROM friendship, user
                    WHERE friendship.uid='$myuid' AND
                          friendship.fstate='friend' AND
                          friendship.other = user.uid";
          $result = mysqli_query($db, $query);
          if ($result->num_rows > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                echo "username: ".$row['uusername']." | name: ".$row['ufname']." ".$row['ulname'];
                $address = "block.php?uid=";
                $address .= $row['other'];
                echo ' | <a href="'.$address.'")">'."Block</a><br>";
            }
          }

          echo "<br>Friend request list: <br>";
          $query = "SELECT friendship.uid, friendship.other, user.uusername, user.ufname, user.ulname
                    FROM friendship, user
                    WHERE friendship.other='$myuid' AND
                          friendship.fstate='request' AND
                          friendship.uid = user.uid";
          $result = mysqli_query($db, $query);
          if ($result->num_rows > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                echo "username: ".$row['uusername']." | name: ".$row['ufname']." ".$row['ulname']." | ";
                $address = "accept.php?uid=";
                $address .= $row['uid'];
                echo '<a href="'.$address.'")">'."Accept</a> | ";
                $address = "decline.php?uid=";
                $address .= $row['uid'];
                echo '<a href="'.$address.'")">'."Decline</a><br>";
            }
          }
          echo "<br>";
      ?>
      <?php endif ?>
  </div>
  <form method="post" action="myfriend.php">
  	<div class="input-group">
        <input type="text" name="username" placeholder="user name">
  		<button type="newfriend" class="btn" name="newfriend">Send friend request</button>
  	</div>
  </form>
  <form method="post" action="index.php">
  	<div class="input-group">
        <button type="back" class="btn" name="back">Back</button>
  	</div>
  </form>
</body>
</html>
