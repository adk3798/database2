<!DOCTYPE html>
<html lang="en">
<head>
    <title>Database 2</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
  <?php
    session_start();
  ?>
  <?php
    if (isset($_POST['submit'])) { // submit being set means mentee
      $mysqli = new mysqli('localhost', 'root', '', 'db2_project'); //The Blank string is the password

      $sid = $_SESSION['sid'];
      $meet_id = $_POST['submit'];

      $query = "SELECT * FROM meetings WHERE meet_id='$meet_id'";
      $result = $mysqli->query($query);
      $row = mysqli_fetch_array($result);
      $meet_name = $row['meet_name'];

      $query = "SELECT * FROM enroll WHERE mentee_id='$sid' AND meet_id='$meet_id'";
      $result = $mysqli->query($query);
      if($result->num_rows !== 0) {
        echo 'removed as mentee';
        $query = "DELETE FROM enroll WHERE mentee_id='$sid' AND meet_id='$meet_id'";
        $result = $mysqli->query($query);

        $query = "SELECT * FROM enroll WHERE mentee_id='$sid'";
        $result = $mysqli->query($query);

        if($result->num_rows === 0) {
          $query = "DELETE FROM mentees WHERE mentee_id='$sid'";
          $result = $mysqli->query($query);
        }
        echo "<h1>You have left meeting $meet_name as a mentee</h1>";
      }
      else {
        echo 'Added as mentee';
        $query = "SELECT * FROM mentees WHERE mentee_id='$sid'";
        $result = $mysqli->query($query);

        if($result->num_rows === 0) {
          $query = "INSERT INTO mentees (mentee_id) VALUES ({$sid})";
          $result = $mysqli->query($query);
        }

        $query = "INSERT INTO enroll (meet_id, mentee_id) VALUES ({$meet_id}, {$sid})";
        $result = $mysqli->query($query);
        echo "<h1>You have joined meeting $meet_name as a mentee</h1>";
      }
      $mysqli->close();
      header('meeting_signup.php');
    }
    else if(isset($_POST['submit2'])) { // submit2 means mentor
      $mysqli = new mysqli('localhost', 'root', '', 'db2_project'); //The Blank string is the password

      $sid = $_SESSION['sid'];
      $meet_id = $_POST['submit2'];

      $query = "SELECT * FROM meetings WHERE meet_id='$meet_id'";
      $result = $mysqli->query($query);
      $row = mysqli_fetch_array($result);
      $meet_name = $row['meet_name'];

      $query = "SELECT * FROM enroll2 WHERE mentor_id='$sid' AND meet_id='$meet_id'";
      $result = $mysqli->query($query);
      if($result->num_rows !== 0) {
        echo 'removed as mentor';
        $query = "DELETE FROM enroll2 WHERE mentor_id='$sid' AND meet_id='$meet_id'";
        $result = $mysqli->query($query);

        $query = "SELECT * FROM enroll2 WHERE mentor_id='$sid'";
        $result = $mysqli->query($query);

        if($result->num_rows === 0) {
          $query = "DELETE FROM mentors WHERE mentor_id='$sid'";
          $result = $mysqli->query($query);
        }
        echo "<h1>You have left meeting $meet_name as a mentor</h1>";
      }
      else {
        echo 'Added as mentor';
        $query = "SELECT * FROM mentors WHERE mentor_id='$sid'";
        $result = $mysqli->query($query);

        if($result->num_rows === 0) {
          $query = "INSERT INTO mentors (mentor_id) VALUES ({$sid})";
          $result = $mysqli->query($query);
        }

        $query = "INSERT INTO enroll2 (meet_id, mentor_id) VALUES ({$meet_id}, {$sid})";
        $result = $mysqli->query($query);
        echo "<h1>You have joined meeting $meet_name as a mentor</h1>";
      }
      $mysqli->close();
    }
  ?>

  <form action="meeting_signup.php">
    Return to meeting signup page:
    <input type="submit" value="Signup for Meetings"></input><br><br>
  </form>

  <form action="landing.php">
    Return to main page:
    <input type="submit" value="Main Page"></input><br><br>
  </form>

  <?php

    $mysqli = new mysqli('localhost', 'root', '', 'db2_project'); //The Blank string is the password

    $sid = $_SESSION['sid'];
    $meet_id = -1;
    $display_info = false;

    if(isset($_POST['submit2'])) {
      $meet_id = $_POST['submit2'];

      $query = "SELECT * FROM enroll2 WHERE mentor_id='$sid' AND meet_id='$meet_id'";
      $result = $mysqli->query($query);
      if($result->num_rows !== 0) {
        $query = "SELECT * FROM enroll2 WHERE meet_id='$meet_id'";
        $result = $mysqli->query($query);
        echo "<table>";
        echo "<h1>Mentors in Meeting</h1>";
        echo "<tr><td>" . "name" . "</td><td>" . "email" . "</td></tr>";
        while($row = mysqli_fetch_array($result)) {
          $mentor_id = $row['mentor_id'];
          $mquery = "SELECT * FROM users WHERE id='$mentor_id'";
          $mresult = $mysqli->query($mquery);
          $mrow = mysqli_fetch_array($mresult);
          echo "<tr><td>" . $mrow['name'] . "</td><td>" . $mrow['email'] . "</td></tr>";
        }
      }

      $query = "SELECT * FROM enroll2 WHERE mentor_id='$sid' AND meet_id='$meet_id'";
      $result = $mysqli->query($query);
      if($result->num_rows !== 0) {
        $query = "SELECT * FROM enroll WHERE meet_id='$meet_id'";
        $result = $mysqli->query($query);
        echo "<table>";
        echo "<h1>Mentees in Meeting</h1>";
        echo "<tr><td>" . "name" . "</td><td>" . "email" . "</td></tr>";
        while($row = mysqli_fetch_array($result)) {
          $mentee_id = $row['mentee_id'];
          $mquery = "SELECT * FROM users WHERE id='$mentee_id'";
          $mresult = $mysqli->query($mquery);
          $mrow = mysqli_fetch_array($mresult);
          echo "<tr><td>" . $mrow['name'] . "</td><td>" . $mrow['email'] . "</td></tr>";
        }
      }
    }
    if(isset($_POST['info'])) {
      $meet_id = $_POST['info'];

      $query = "SELECT * FROM enroll2 WHERE mentor_id='$sid' AND meet_id='$meet_id'";
      $result = $mysqli->query($query);
      if($result->num_rows !== 0) {
        $query = "SELECT * FROM enroll2 WHERE meet_id='$meet_id'";
        $result = $mysqli->query($query);
        echo "<table>";
        echo "<h1>Mentors in Meeting</h1>";
        echo "<tr><td>" . "name" . "</td><td>" . "email" . "</td></tr>";
        while($row = mysqli_fetch_array($result)) {
          $mentor_id = $row['mentor_id'];
          $mquery = "SELECT * FROM users WHERE id='$mentor_id'";
          $mresult = $mysqli->query($mquery);
          $mrow = mysqli_fetch_array($mresult);
          echo "<tr><td>" . $mrow['name'] . "</td><td>" . $mrow['email'] . "</td></tr>";
        }
      }

      $query = "SELECT * FROM enroll2 WHERE mentor_id='$sid' AND meet_id='$meet_id'";
      $result = $mysqli->query($query);
      if($result->num_rows !== 0) {
        $query = "SELECT * FROM enroll WHERE meet_id='$meet_id'";
        $result = $mysqli->query($query);
        echo "<table>";
        echo "<h1>Mentees in Meeting</h1>";
        echo "<tr><td>" . "name" . "</td><td>" . "email" . "</td></tr>";
        while($row = mysqli_fetch_array($result)) {
          $mentee_id = $row['mentee_id'];
          $mquery = "SELECT * FROM users WHERE id='$mentee_id'";
          $mresult = $mysqli->query($mquery);
          $mrow = mysqli_fetch_array($mresult);
          echo "<tr><td>" . $mrow['name'] . "</td><td>" . $mrow['email'] . "</td></tr>";
        }
      }
    }
    unset($_POST['submit']);
    unset($_POST['submit2']);
    unset($_POST['info']);
  ?>

</body>
</html>
