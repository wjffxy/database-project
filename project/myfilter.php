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
  	<h2>My Filter</h2>
  </div>
  <div class="content">
      <!-- logged in user information -->
      <?php  if (isset($_SESSION['username'])) :
          $username = $_SESSION['username'];
          $query = "SELECT *
                    FROM filter, user, schedule
                    WHERE user.uusername='$username' AND
                          user.uid=filter.uid AND
                          filter.sid=schedule.sid;";
          $result = mysqli_query($db, $query);
          if ($result->num_rows > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                echo "Tag: ".$row['tag']."&nbsp|&nbsp";
                echo "State: ".$row['ustate']."&nbsp|&nbsp";
                echo "FromWhom: ".$row['fromwhom']."&nbsp|&nbsp";
                echo "Start: ".date("j F Y", strtotime($row['sstart']))."&nbsp|&nbsp";
                $end = strtotime($row['send']);
                if (date("Y", $end)>0){
                    echo "End: ".date("j F Y", $end)."&nbsp|&nbsp";
                }
                if ($row['srepeat']==1){
                    echo "Repeat On: ".$row['sweekday']."&nbsp|&nbsp";
                }
                $address = "deletefil.php?filid=";
                $address .= $row['filid'];
                echo '<a href="'.$address.'")">'."Delete</a><br>";
            }
          } else {
            echo "No filter";
          }
      ?>
      <?php endif ?>
  </div>
  <form method="post" action="newfilter.php">
  	<div class="input-group">
  		<button type="newfilter" class="btn" name="newfilter">New Filter</button>
  	</div>
  </form>
  <form method="post" action="index.php">
  	<div class="input-group">
        <button type="back" class="btn" name="back">Back</button>
  	</div>
  </form>
</body>
</html>
