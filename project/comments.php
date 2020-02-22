<?php
session_start();

$db = mysqli_connect('localhost', 'root', '', 'pro1');

if( $db-> connect_errno <> 0 ){
	echo "connection failed.";
    echo $db->connect_error;
    exit;
}

$username = $_SESSION['username'];
$nid = $_GET['nid'];
$addr = "comments.php?nid=".$nid;
if (!isset($_SESSION['username'])) {
  $_SESSION['msg'] = "You must log in first";
  header('location: login.php');
}
if (isset($_POST['comment'])){
    $content = mysqli_real_escape_string($db, $_POST['content']);
    function post($content){
	   if($content == ''){
		  return false;
	  } else {
          return true;
      }
    }

    $is = post($content);
    if( $is == false ){
	   die("comment's data is empty");
    }

    $query = "SELECT uid FROM user where uusername='$username'";
    $result = mysqli_query($db, $query);
    $row = mysqli_fetch_assoc($result);
    $uid = $row['uid'];
    date_default_timezone_set('America/New_York');
    $now = date("Y-m-d H:i:s", time());
    $sql = "INSERT INTO commentsofnote (nid,uid,ctime,comment) values ('$nid','$uid','$now','$content')";
    mysqli_query($db, $sql);
}
$query = "select * from user,commentsofnote,note
          where commentsofnote.nid='$nid' and
                note.nid = commentsofnote.nid and
                user.uid=commentsofnote.uid;";
$result = mysqli_query($db, $query);
$rows = [];
while( $row = mysqli_fetch_assoc($result)){
	 $rows[] = $row;
}
?>
<!DOCTYPE html>
<html>
    <head>
    	<meta charset="UTF-8"/>
    	<title>comments</title>
    	<style>
    		.wrap{
    			width:600px;
    			margin:0px auto;
    		}
    		.add{overflow: hidden;}
    		.add .content{
    			width:598px;
    			margin:0;
                padding:0;
    		}
    		.add .user{
    			float:left;
    		}
    		.add .btn{
    			float:right;
    		}
            .back .btn{
    			float:right;
    		}
    		.msg{margin:20px 0px;background: #ccc;padding:5px;}
            .msg .info{overflow: hidden;}
            .msg .user{float:left;color:blue;}
            .msg .time{float:right;color:#999;}
            .msg .content{width:100%;}
       	</style>
    </head>
    <body>
    	<div class='wrap'>
    		<!--- comment --->
    		<div class='add'>
    		<form action=<?php echo $addr ?> method="post">
    			<textarea name='content' class='content' cols='50' rows='5'></textarea>
                <button type="submit" class="btn" name="comment">Comment</button>
            </form>
            <div class='back'>
            <?php $addr = "note.php?nid=".$nid; ?>
            <form method="post" action=<?php echo $addr ?>>
            	<div class="input-group">
                  <button type="back" class="btn" name="back">Back</button>
            	</div>
            </form>
            </div>

            <?php
            foreach ( $rows as $row) {
            ?>
                <!-- look up comment -->
                <div class='msg'>
                	<div class='info'>
                		<span class='user'><?php echo $row['uusername'];?></span>
                		<span class='time'><?php echo date("Y-m-d H:i:s", strtotime($row['ctime']));?></span>
                	</div>
                	<div class='content'>
                		<?php echo $row['comment'];?>
                	</div>
                </div>
            <?php
            }
            ?>
        </div>
    </body>
</html>
