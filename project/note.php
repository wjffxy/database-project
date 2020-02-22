<?php
  session_start();

  $username=$notename='';

  if (!isset($_SESSION['username'])) {
  	$_SESSION['msg'] = "You must log in first";
  	header('location: login.php');
  }

  // connect to the database
  $db = mysqli_connect('localhost', 'root', '', 'pro1');
  $nid = $_GET['nid'];
  if (isset($_SESSION['username'])){
      $username = $_SESSION['username'];
      $query = "SELECT *
                FROM user,note,writes
                WHERE user.uid=writes.uid AND
                      note.nid=writes.nid AND
                      note.nid=$nid";
      $result = mysqli_query($db, $query);
      if ($result->num_rows > 0) {
        $row = mysqli_fetch_assoc($result);
        echo 'Note name: '.$row['nname'].'<br>';
        echo 'Note content: '.$row['ncontent'].'<br>';
        echo 'State: '.$row['ustate'].'<br>';
        $tagquery = "SELECT *
                  FROM tag
                  WHERE nid=$nid";
        $tagresult = mysqli_query($db, $tagquery);
        if ($tagresult->num_rows > 0) {
            while ($tag = mysqli_fetch_assoc($tagresult)) {
                echo $tag['tname'];
            }
            echo "<br>";
        }
      } else {
        echo 'No Access!';
      }
  }
?>
<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
  <meta charset="utf-8">
  <title>note</title>
  <style>
    /* Always set the map height explicitly to define the size of the div
     * element that contains the map. */
    #map {
      height: 90%;
      width: 80%;
    }
    /* Optional: Makes the sample page fill the window. */
    html, body {
      height: 90%;
      margin: 0;
      padding: 0;
    }
  </style>
</head>
<body>
  <?php
  $address = "comments.php";
  $address .= "?nid=". $nid;
  echo '<a href="'.$address.'")">'."Comments</a><br>"
  ?>

  <div id="map"></div>
  <script>
    function initMap() {
      var note = {lat: <?php echo $row['nlat'];?>, lng: <?php echo $row['nlng'];?>};
      var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: note
      });

      var contentString = '<div id="content">'+
          '<div id="siteNotice">'+
          '</div>'+
          '<h1 id="firstHeading" class="firstHeading"><?php echo $row['nname'] ?></h1>'+
          '<div id="bodyContent">'+
          '<p><?php echo $row['ncontent'] ?></p>'+
          '</div>'+
          '</div>';

      var infowindow = new google.maps.InfoWindow({
        content: contentString,
        maxWidth: 100
      });
      var marker = new google.maps.Marker({
        position: note,
        map: map,
        title: '<?php echo $row['nname'] ?>'
      });
      marker.addListener('click', function() {
        infowindow.open(map, marker);
      });
    }
  </script>
  <script async defer
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBZEQbAGhnlroLsHMpTX_v6KaAmTeMQzsg&callback=initMap">
  </script>
  <form method="post" action="mynote.php">
  	<div class="input-group">
        <button type="back" class="btn" name="back">Back</button>
  	</div>
  </form>
</body>
</html>
