<?php
  session_start();
  $errors = array();
  if (!isset($_SESSION['username'])) {
  	$_SESSION['msg'] = "You must log in first";
  	header('location: login.php');
  }
  $username = $_SESSION['username'];
  if (isset($_GET['logout'])) {
  	session_destroy();
  	unset($_SESSION['username']);
  	header("location: login.php");
  }
  if (isset($_POST['go'])){
    $db = mysqli_connect('localhost', 'root', '', 'pro1');
    $date = mysqli_real_escape_string($db, $_POST['currentdate']);
    $lati = mysqli_real_escape_string($db, $_POST['lati']);
    $longi = mysqli_real_escape_string($db, $_POST['longi']);
    if (empty($date)) { array_push($errors, "Date is required"); }
    if (empty($lati)) { array_push($errors, "Latitude is required"); }
    if (empty($longi)) { array_push($errors, "Longitude is required"); }

    $day = date('w', strtotime($date));
    $day = (int)$day;
    $lati = (float)$lati;
    $longi = (float)$longi;
    $query = "CREATE VIEW filters (fillat,fillng,tag,ustate,fromwhom) AS
              SELECT filter.fillat,filter.fillng,filter.tag,filter.ustate,filter.fromwhom
              FROM filter, user, schedule
              where user.uusername='$username' AND
              filter.sid = schedule.sid AND
              filter.uid = user.uid AND
              (schedule.sweekday like '%$day%' OR
                  schedule.sweekday='') AND
              DATEDIFF(STR_TO_DATE('$date', '%m/%d/%Y'), schedule.sstart)>=0 AND
              (DATEDIFF(STR_TO_DATE('$date', '%m/%d/%Y'), schedule.send)<=0 OR
               YEAR(schedule.send)=0);";
    mysqli_query($db, $query);
    $query = "SELECT * from filters;";
    $result = mysqli_query($db, $query);
    if ($result->num_rows > 0) {
        $filter = "SELECT DISTINCT *
                   FROM note, schedule, filters, tag, writes
                   WHERE note.sid = schedule.sid AND
                   (writes.ustate = filters.ustate OR
                       filters.ustate = '') AND
                   writes.nid = note.nid AND
                   tag.nid = note.nid AND
                   (tag.tname = filters.tag OR
                       filters.tag='') AND
                   filters.fromwhom = note.showto AND
                   (schedule.sweekday LIKE '%$day%' OR
                       schedule.sweekday='') AND
                   DATEDIFF(STR_TO_DATE('$date', '%m/%d/%Y'), schedule.sstart)>=0 AND
                   (DATEDIFF(STR_TO_DATE('$date', '%m/%d/%Y'), schedule.send)<=0 OR
                   YEAR(schedule.send)=0) AND
                   (69*DEGREES(ACOS(LEAST(COS(RADIANS(note.nlat))
                   * COS(RADIANS(filters.fillat))
                   * COS(RADIANS(note.nlng - filters.fillng))
                   + SIN(RADIANS(note.nlat))
                   * SIN(RADIANS(filters.fillat)), 1.0)))<=note.nradius)";
    } else {
        $filter = "SELECT DISTINCT *
                   FROM note, schedule
                   WHERE note.sid = schedule.sid AND
                   (schedule.sweekday like '%$day%' OR
                    schedule.sweekday='') AND
                   DATEDIFF(STR_TO_DATE('$date', '%m/%d/%Y'), schedule.sstart)>=0 AND
                   (DATEDIFF(STR_TO_DATE('$date', '%m/%d/%Y'), schedule.send)<=0 OR
                    YEAR(schedule.send)=0) AND
                   (69*DEGREES(ACOS(LEAST(COS(RADIANS(note.nlat))
                    * COS(RADIANS('$lati'))
                    * COS(RADIANS(note.nlng - '$longi'))
                    + SIN(RADIANS(note.nlat))
                    * SIN(RADIANS('$lati')), 1.0)))<=note.nradius)";
    }
  }
?>
<!DOCTYPE html>
<html>
<head>
	<title>Home</title>
</head>
<body>
    <div class="header">
    	<h2>Home Page</h2>
    </div>
    <div class="content">
        <!-- logged in user information -->
        <?php if (isset($_SESSION['username'])): ?>
        	<p>Welcome <strong><?php echo $_SESSION['username']; ?></strong></p>
        	<p> <a href="index.php?logout='1'" style="color: red;">logout</a> </p>
            <p> <a href="mynote.php">My Note</a> </p>
            <p> <a href="myfilter.php">My Filter</a> </p>
            <p> <a href="myfriend.php">My Friend</a> </p>
        <?php endif ?>
        <form method="post" action="index.php">
            <?php include('errors.php'); ?>
            <div class="input-group">
          		<label>Latitude: </label>
          		<input type="text" name="lati" id="lati">&nbsp
          		<label>Longitude: </label>
          		<input type="text" name="longi" id="longi">&nbsp
                <label>Current Date: </label>
          		<input type="text" name="currentdate">&nbsp
                <button type="submit" class="btn" name="go">GO!</button>
          	</div>
        </form>
    </div>
    <?php
    $locations=array();
    if (isset($_POST['go'])){
        $result = mysqli_query($db, $filter);
        if ($result->num_rows > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $nid = $row['nid'];
                $notename = $row['nname'];
                $nlati = $row['nlat'];
                $nlng = $row['nlng'];
                $notecontent = $row['ncontent'];
                $locations[]=array("name"=>$notename, "nid"=>$nid, "content"=>$notecontent, "lat"=>$nlati, "lng"=>$nlng);
                $address = "note.php?nid=";
                $address .= $row['nid'];
            }
        } else {echo "No available note.";}
        $drop = "DROP VIEW filters;";
        mysqli_query($db, $drop);
    }
    ?>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBZEQbAGhnlroLsHMpTX_v6KaAmTeMQzsg&sensor=false" type="text/javascript"></script>
    <script type="text/javascript">

    $(document).ready(function() {
        // map options
        var options = {
            zoom: 12,
            center: new google.maps.LatLng(40.7127753, -74.0059728), // centered US
            mapTypeId: google.maps.MapTypeId.TERRAIN,
            mapTypeControl: false
        };
        // init map
        var map = new google.maps.Map(document.getElementById('map_canvas'), options);
        // execute
        (function() {

            var json = [{"name":"currentlocation", "lat":40.7127753, "lng":-74.0059728}];
            // set multiple marker
            var data = json[0];
            var latLng = new google.maps.LatLng(data.lat, data.lng);
            // init markers
            var initmarker = new google.maps.Marker({
                position: latLng,
                map: map,
                draggable: true
            });
                // process multiple info windows
            (function(marker) {
                var infowindow = new google.maps.InfoWindow({
                    maxWidth: 200,
                    content: "current location"
                });
                marker.addListener('click', function() {
                     infowindow.open(map, marker);
                });
                marker.addListener('dragend', function(evt) {
                    document.getElementById("lati").value= evt.latLng.lat();
                    document.getElementById("longi").value= evt.latLng.lng();
                })
                marker.addListener('dragstart', function(evt) {
                })
            })(initmarker);

            var json = <?php echo json_encode($locations) ?>;
            // set multiple marker
            for (var i = 0, length = json.length; i < length; i++) {
                var data = json[i];
                var latLng = new google.maps.LatLng(data.lat, data.lng);
                // init markers
                var marker = new google.maps.Marker({
                    position: latLng,
                    map: map,
                    title: data.name
                    //draggable: true
                });
                // process multiple info windows
                (function(marker, i) {
                    var contentString = '<div id="content">'+
                        '<div id="siteNotice">'+
                        '</div>'+
                        '<h1 id="firstHeading" class="firstHeading">'+data.name+'</h1>'+
                        '<div id="bodyContent">'+
                        '<p>'+data.content+'</p>'+
                        '<a href="note.php?nid='+data.nid+'" target="_blank">'+
                          'go to note</a>' +
                        '</div>'+
                        '</div>';
                    var infowindow = new google.maps.InfoWindow({
                      content: contentString,
                      maxWidth: 200
                    });
                    marker.addListener('click', function() {
                      infowindow.open(map, marker);
                    });
                })(marker, i);
            }
        }
        )();
        /*
        (function() {
            var json = JSON.parse(<?php echo json_encode($locations) ?>);
            // set multiple marker
            for (var i = 0, length = json.length; i < length; i++) {
                var data = json[i];
                var latLng = new google.maps.LatLng(data.lat, data.lng);
                // init markers
                var marker = new google.maps.Marker({
                    position: latLng,
                    map: map,
                    title: data.name
                    //draggable: true
                });
                // process multiple info windows
                (function(marker, i) {
                    var contentString = '<div id="content">'+
                        '<div id="siteNotice">'+
                        '</div>'+
                        '<h1 id="firstHeading" class="firstHeading">'+data.name+'</h1>'+
                        '<div id="bodyContent">'+
                        '<p>'+data.content+'</p>'+
                        '<a href="note.php?nid='+data.nid+'" target="_blank">'+
                          'go to note</a>' +
                        '</div>'+
                        '</div>';
                    var infowindow = new google.maps.InfoWindow({
                      content: contentString,
                      maxWidth: 200
                    });
                    marker.addListener('click', function() {
                      infowindow.open(map, marker);
                    });
                })(marker, i);

            }
        })(); */
    });
        /*
        $(document).ready(function() {
            // map options
            var options = {
                zoom: 12,
                center: new google.maps.LatLng(40.7127753, -74.0059728), // centered US
                mapTypeId: google.maps.MapTypeId.TERRAIN,
                mapTypeControl: false
            };
            // init map
            var map = new google.maps.Map(document.getElementById('map_canvas'), options);
            // execute
            (function() {

                var json = ;
                // set multiple marker
                for (var i = 0, length = json.length; i < length; i++) {
                    var data = json[i];
				    var latLng = new google.maps.LatLng(data.lat, data.lng);
                    // init markers
                    var marker = new google.maps.Marker({
                        position: latLng,
                        map: map,
                        title: data.name
                        //draggable: true
                    });
                    // process multiple info windows
                    (function(marker, i) {
                        var contentString = '<div id="content">'+
                            '<div id="siteNotice">'+
                            '</div>'+
                            '<h1 id="firstHeading" class="firstHeading">'+data.name+'</h1>'+
                            '<div id="bodyContent">'+
                            '<p>'+data.content+'</p>'+
                            '<a href="note.php?nid='+data.nid+'" target="_blank">'+
                              'go to note</a>' +
                            '</div>'+
                            '</div>';
                        var infowindow = new google.maps.InfoWindow({
                          content: contentString,
                          maxWidth: 200
                        });
                        marker.addListener('click', function() {
                          infowindow.open(map, marker);
                        });
                    })(marker, i);

                }
            })();
        });
        */
    </script>
    <div id="map_canvas" style="width: 800px; height:500px;"></div>
</body>
</html>
