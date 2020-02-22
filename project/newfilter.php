<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<body>
  <div class="header">
  	<h2>New Filter</h2>
  </div>

  <form method="post" action="newfilter.php">
    <?php include('errors.php'); ?>
  	<div class="input-group">
  	  <label>Filter Address: </label>
  	  <input type="text" name="filteraddress">
  	</div>
    <div class="input-group">
  	  <label>Tag: </label>
  	  <input type="text" name="filtertag">
  	</div>
    <div class="input-group">
  	  <label>State: </label>
  	  <input type="text" name="filterstate">
  	</div>
    <div class="radio-group">
        From Whom:
        <input type="radio" name="fromwhom" value="all" checked="checked">All
        <input type="radio" name="fromwhom" value="friend">Friend
        <input type="radio" name="fromwhom" value="self">Self
    </div>
    <div class="input-group">
      <label>Effective Schedule: </label><br>
      <label>Start Time: </label>
      <input type="text" name="start">
      <label>End Time: </label>
      <input type="text" name="end">
    </div>
    <div class="radio-group">
        Repeat:
        <input type="radio" name="repeat" value="1">Yes
        <input type="radio" name="repeat" value="0" checked="checked">No<br>
        Repeat On Weekday: <input type="text" name="weekday">
    </div>
  	<div class="input-group">
  	  <button type="submit" class="btn" name="new_filter">Done</button>
  	</div>
  </form>
  <form method="post" action="myfilter.php">
  	<div class="input-group">
        <button type="back" class="btn" name="back">Back</button>
  	</div>
  </form>
</body>
</html>
