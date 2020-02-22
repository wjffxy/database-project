<?php
session_start();

// initializing variables
$username = "";
$user ="";
$email    = "";
$errors = array();

// connect to the database
$db = mysqli_connect('localhost', 'root', '', 'pro1');

// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receive all input values from the form
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);
  $fname = mysqli_real_escape_string($db, $_POST['firstname']);
  $lname = mysqli_real_escape_string($db, $_POST['lastname']);

  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
  if (empty($username)) { array_push($errors, "Username is required"); }
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (empty($password_1)) { array_push($errors, "Password is required"); }
  if ($password_1 != $password_2) {
	array_push($errors, "The two passwords do not match");
  }
  if (empty($fname)) { array_push($errors, "First name is required"); }
  if (empty($lname)) { array_push($errors, "Last name is required"); }

  // first check the database to make sure
  // a user does not already exist with the same username and/or email
  $user_check_query = "SELECT * FROM user WHERE uusername='$username' OR uemail='$email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  if ($result) {
      $user = mysqli_fetch_assoc($result);
      if ($user) { // if user exists
        if ($user['uusername'] === $username) {
          array_push($errors, "Username already exists");
        }

        if ($user['uemail'] === $email) {
          array_push($errors, "email already exists");
        }
      }
  }

  // Finally, register user if there are no errors in the form
  if (count($errors) == 0) {
  	$password = md5($password_1);//encrypt the password before saving in the database

  	$query = "INSERT INTO user (uusername, uemail, upwd, ufname, ulname)
  			  VALUES('$username', '$email', '$password', '$fname', '$lname')";
  	mysqli_query($db, $query);
  	$_SESSION['username'] = $username;
  	$_SESSION['success'] = "You are now logged in";
  	header('location: index.php');
  }
}

if (isset($_POST['login_user'])) {
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  if (empty($username)) {
  	array_push($errors, "Username is required");
  }
  if (empty($password)) {
  	array_push($errors, "Password is required");
  }

  if (count($errors) == 0) {
  	$password = md5($password);
  	$query = "SELECT * FROM user WHERE uusername='$username' AND upwd='$password'";
  	$results = mysqli_query($db, $query);
  	if (mysqli_num_rows($results) == 1) {
  	  $_SESSION['username'] = $username;
  	  //$_SESSION['success'] = "You are now logged in";
  	  header('location: index.php');
  	}else {
  		array_push($errors, "Wrong username/password combination");
  	}
  }
}

if (isset($_POST['new_note'])) {
    $notename = mysqli_real_escape_string($db, $_POST['notename']);
    $address = mysqli_real_escape_string($db, $_POST['noteaddress']);
    $radius = mysqli_real_escape_string($db, $_POST['noteradius']);
    $showto = mysqli_real_escape_string($db, $_POST['showto']);
    $content = mysqli_real_escape_string($db, $_POST['content']);
    $starttime = mysqli_real_escape_string($db, $_POST['start']);
    $endtime = mysqli_real_escape_string($db, $_POST['end']);
    $on = mysqli_real_escape_string($db, $_POST['weekday']);
    $repeat = mysqli_real_escape_string($db, $_POST['repeat']);
    $state = mysqli_real_escape_string($db, $_POST['state']);
    $tag = mysqli_real_escape_string($db, $_POST['tag']);

    if (empty($notename)) { array_push($errors, "Notename is required"); }
    if (empty($address)) { array_push($errors, "Address is required"); }
    if (empty($radius)) { array_push($errors, "Radius is required"); }
    if (empty($showto)) { array_push($errors, "Show-to is required"); }
    if (empty($content)) { array_push($errors, "Content is required"); }
    if (empty($starttime)) { array_push($errors, "Start time is required"); }
    if (count($errors) == 0) {
        $query = "INSERT INTO schedule (sstart,send,sweekday,srepeat) values
                        (STR_TO_DATE('$starttime', '%m/%d/%Y'),
                        STR_TO_DATE('$endtime', '%m/%d/%Y'),
                        '$on', '$repeat');";
        $result = mysqli_query($db, $query);
        $query = "SELECT LAST_INSERT_ID();";
        $result = mysqli_query($db, $query);
        if ($result->num_rows > 0) {
            $row = mysqli_fetch_assoc($result);
        }
        $sid = $row['LAST_INSERT_ID()'];

        // url encode the address
        $address = urlencode($address);
        // google map geocode api url
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=AIzaSyBZEQbAGhnlroLsHMpTX_v6KaAmTeMQzsg";
        // get the json response
        $resp_json = file_get_contents($url);
        // decode the json
        $resp = json_decode($resp_json, true);
        // response status will be 'OK', if able to geocode given address
        if($resp['status']=='OK'){
            $lati = isset($resp['results'][0]['geometry']['location']['lat']) ? $resp['results'][0]['geometry']['location']['lat'] : "";
            $longi = isset($resp['results'][0]['geometry']['location']['lng']) ? $resp['results'][0]['geometry']['location']['lng'] : "";
        }
        $query = "INSERT INTO note (sid,nname,nlat,nlng,nradius,ncontent,showto)
                    values ('$sid','$notename','$lati','$longi','$radius','$content','$showto');";
        mysqli_query($db, $query);
        $query = "SELECT LAST_INSERT_ID();";
        $result = mysqli_query($db, $query);
        $row = mysqli_fetch_assoc($result);
        $nid = $row['LAST_INSERT_ID()'];

        $username = $_SESSION['username'];
        $query = "SELECT uid FROM user where uusername='$username'";
        $result = mysqli_query($db, $query);
        $row = mysqli_fetch_assoc($result);
        $uid = $row['uid'];

        $query = "INSERT INTO writes values ('$uid','$nid','$state');";
        mysqli_query($db, $query);

        $tagarray = explode("#", $tag);
        for ($i = 1; $i < sizeof($tagarray); $i++){
            $tsharp = "#".$tagarray[$i];
            $query = "INSERT INTO tag (nid,tname) values ('$nid','$tsharp');";
            mysqli_query($db, $query);
        }

      	header('location: mynote.php');
    }
}

if (isset($_POST['new_filter'])) {
    $filaddr = mysqli_real_escape_string($db, $_POST['filteraddress']);
    $filtag = mysqli_real_escape_string($db, $_POST['filtertag']);
    $filstate = mysqli_real_escape_string($db, $_POST['filterstate']);
    $fromwhom = mysqli_real_escape_string($db, $_POST['fromwhom']);
    $starttime = mysqli_real_escape_string($db, $_POST['start']);
    $endtime = mysqli_real_escape_string($db, $_POST['end']);
    $on = mysqli_real_escape_string($db, $_POST['weekday']);
    $repeat = mysqli_real_escape_string($db, $_POST['repeat']);

    if (empty($filaddr)) { array_push($errors, "Address is required"); }
    if (empty($fromwhom)) { array_push($errors, "Show-to is required"); }
    if (empty($starttime)) { array_push($errors, "Start time is required"); }
    if (count($errors) == 0) {
        $query = "INSERT INTO schedule (sstart,send,sweekday,srepeat) values
                        (STR_TO_DATE('$starttime', '%m/%d/%Y'),
                        STR_TO_DATE('$endtime', '%m/%d/%Y'),
                        '$on', '$repeat');";
        $result = mysqli_query($db, $query);
        $query = "SELECT LAST_INSERT_ID();";
        $result = mysqli_query($db, $query);
        if ($result->num_rows > 0) {
            $row = mysqli_fetch_assoc($result);
        }
        $sid = $row['LAST_INSERT_ID()'];

        // url encode the address
        $address = urlencode($filaddr);
        // google map geocode api url
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=AIzaSyBZEQbAGhnlroLsHMpTX_v6KaAmTeMQzsg";
        // get the json response
        $resp_json = file_get_contents($url);
        // decode the json
        $resp = json_decode($resp_json, true);
        // response status will be 'OK', if able to geocode given address
        if($resp['status']=='OK'){
            $lati = isset($resp['results'][0]['geometry']['location']['lat']) ? $resp['results'][0]['geometry']['location']['lat'] : "";
            $longi = isset($resp['results'][0]['geometry']['location']['lng']) ? $resp['results'][0]['geometry']['location']['lng'] : "";
        }

        $username = $_SESSION['username'];
        $query = "SELECT uid FROM user where uusername='$username'";
        $result = mysqli_query($db, $query);
        $row = mysqli_fetch_assoc($result);
        $uid = $row['uid'];

        $query = "INSERT INTO filter (sid,uid,fillat,fillng,tag,ustate,fromwhom) values
                    ('$sid','$uid','$lati','$longi','$filtag','$filstate','$fromwhom');";
        mysqli_query($db, $query);

      	header('location: myfilter.php');
    }
}

?>
