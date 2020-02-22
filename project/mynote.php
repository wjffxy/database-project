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
  	<h2>My Note</h2>
  </div>
  <div class="content">
      <!-- logged in user information -->
      <?php  if (isset($_SESSION['username'])) :
          $username = $_SESSION['username'];
          $query = "SELECT note.nname, note.nid
                    FROM user,note,writes
                    WHERE user.uusername='$username' AND
                          user.uid=writes.uid AND
                          note.nid=writes.nid";
          $result = mysqli_query($db, $query);
          if ($result->num_rows > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $address = "note.php?nid=";
                $address .= $row['nid'];
                echo '<a href="'.$address.'")">'. $row['nname']."</a>&nbsp|&nbsp";
                $address = "deletenote.php?nid=";
                $address .= $row['nid'];
                echo '<a href="'.$address.'")">'."Delete</a><br>";
            }
          }
      ?>
      <?php endif ?>
  </div>
  <form method="post" action="newnote.php">
  	<div class="input-group">
  		<button type="newnote" class="btn" name="newnote">New Note</button>
  	</div>
  </form>
  <form method="post" action="index.php">
  	<div class="input-group">
        <button type="back" class="btn" name="back">Back</button>
  	</div>
  </form>
</body>
</html>
