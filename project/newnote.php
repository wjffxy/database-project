<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<body>
  <?php
    $notename=$showto=$noteradius=$notecontent="";
  ?>
  <div class="header">
  	<h2>New Note</h2>
  </div>

  <form method="post" action="newnote.php">
    <?php include('errors.php'); ?>
  	<div class="input-group">
  	  <label>Note name: </label>
  	  <input type="text" name="notename">
  	</div>
    <div class="input-group">
  	  <label>Address: </label>
  	  <input type="text" name="noteaddress">
  	</div>
    <div class="input-group">
  	  <label>Radius: </label>
  	  <input type="text" name="noteradius">
      <label>miles</label>
  	</div>
    <div class="input-group">
      <label>Schedule: </label><br>
      <label>Start Time: </label>
      <input type="text" name="start">
      <label>End Time: </label>
      <input type="text" name="end">
    </div>
    <div class="radio-group">
        Repeat:
        <input type="radio" name="repeat" value="0" checked="checked">No
        <input type="radio" name="repeat" value="1">Yes<br>
        Repeat On Weekday: <input type="text" name="weekday">
    </div>
    <div class="radio-group">
        Show To:
        <input type="radio" name="showto" value="all" checked="checked">All
        <input type="radio" name="showto" value="friend">Friend
        <input type="radio" name="showto" value="self">Self
    </div>
    <div class="radio-group">
        <label>Current State: </label>
        <input type="text" name="state">
    </div>
    <div class="radio-group">
        <label>Tag: </label>
        <input type="text" name="tag">
    </div>
  	<div class="input-group">
        <label>Content: </label><br>
        <textarea type="content" name="content" rows="10" cols="50"></textarea>
    </div>
  	<div class="input-group">
  	  <button type="submit" class="btn" name="new_note">Done</button>
  	</div>
  </form>
  <form method="post" action="mynote.php">
  	<div class="input-group">
        <button type="back" class="btn" name="back">Back</button>
  	</div>
  </form>
</body>
</html>
