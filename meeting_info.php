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

    function mentor_conflict($student_id, $meeting_id, $mysqli) {
      $meet_query = "SELECT * FROM meetings WHERE meet_id='$meeting_id'";
      $meet_result = $mysqli->query($meet_query);
      $meet_row = mysqli_fetch_array($meet_result);
      $meet_date = $meet_row['date'];
      $subject = $meet_row['meet_name'];

      $enroll2_query = "SELECT * FROM enroll2 WHERE mentor_id='$student_id'";
      $enroll2_result = $mysqli->query($enroll2_query);
      if($enroll2_result->num_rows === 0) {
        return false;
      }
      else {
        while($enroll2_row = mysqli_fetch_array($enroll2_result)) {
          $other_mid = $enroll2_row['meet_id'];
          if($other_mid != $meeting_id) {
            $meet2_query = "SELECT * FROM meetings WHERE meet_id='$other_mid'";
            $meet2_result = $mysqli->query($meet2_query);
            $meet2_row = mysqli_fetch_array($meet2_result);
            $meet2_date = $meet2_row['date'];
            $dt1 = date_create($meet_date);
            $dt2 = date_create($meet2_date);
            $diff = date_diff($dt1, $dt2, true);
            if((int)$diff->format('%a') <= 1) {
              return true;
            }
          }
        }
      }
      return false;
    }

    function mentee_conflict($student_id, $meeting_id, $mysqli) {
      $meet_query = "SELECT * FROM meetings WHERE meet_id='$meeting_id'";
      $meet_result = $mysqli->query($meet_query);
      $meet_row = mysqli_fetch_array($meet_result);
      $meet_date = $meet_row['date'];
      $subject = $meet_row['meet_name'];
      $meet_ts = $meet_row['time_slot_id'];

      $enroll_query = "SELECT * FROM enroll WHERE mentee_id='$student_id'";
      $enroll_result = $mysqli->query($enroll_query);
      if($enroll_result->num_rows === 0) {
        return false;
      }
      else {
        while($enroll_row = mysqli_fetch_array($enroll_result)) {
          $other_mid = $enroll_row['meet_id'];
          if($other_mid != $meeting_id) {
            $meet2_query = "SELECT * FROM meetings WHERE meet_id='$other_mid'";
            $meet2_result = $mysqli->query($meet2_query);
            $meet2_row = mysqli_fetch_array($meet2_result);
            $meet2_date = $meet2_row['date'];
            $meet2_subject = $meet2_row['meet_name'];
            $meet2_ts = $meet2_row['time_slot_id'];
            $dt1 = date_create($meet_date);
            $dt2 = date_create($meet2_date);
            $diff = date_diff($dt1, $dt2, true);
            if(((int)$diff->format('%a') <= 1 && $subject == $meet2_subject) ||
                  ((int)$diff->format('%a') == 0 && $meet_ts == $meet2_ts)) {
              return true;
            }
          }
        }
      }
      return false;
    }
  ?>
  <?php
    $today = date("Y-m-d");
    if (isset($_POST['submit'])) { // submit being set means mentee
      $mysqli = new mysqli('localhost', 'root', '', 'DB2'); //The Blank string is the password

      $sid = $_SESSION['sid'];
      $meet_id = $_POST['submit'];

      $query = "SELECT * FROM meetings WHERE meet_id='$meet_id'";
      $result = $mysqli->query($query);
      $row = mysqli_fetch_array($result);
      $meet_name = $row['meet_name'];

      $query = "SELECT * FROM enroll WHERE mentee_id='$sid' AND meet_id='$meet_id'";
      $result = $mysqli->query($query);
      $conflict = mentee_conflict($sid, $meet_id, $mysqli);
      if($result->num_rows !== 0 && !$conflict) {
        $query = "DELETE FROM enroll WHERE mentee_id='$sid' AND meet_id='$meet_id'";
        $result = $mysqli->query($query);

        $query = "SELECT * FROM enroll WHERE mentee_id='$sid'";
        $result = $mysqli->query($query);

        echo "<h1>You have left meeting $meet_name as a mentee</h1>";
      }
      else if(!$conflict){
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
      $mysqli = new mysqli('localhost', 'root', '', 'DB2'); //The Blank string is the password

      $sid = $_SESSION['sid'];
      $meet_id = $_POST['submit2'];

      $query = "SELECT * FROM meetings WHERE meet_id='$meet_id'";
      $result = $mysqli->query($query);
      $row = mysqli_fetch_array($result);
      $meet_name = $row['meet_name'];

      $query = "SELECT * FROM enroll2 WHERE mentor_id='$sid' AND meet_id='$meet_id'";
      $result = $mysqli->query($query);

      $conflict = mentor_conflict($sid, $meet_id, $mysqli);

      if($result->num_rows !== 0 && !$conflict) {
        $query = "DELETE FROM enroll2 WHERE mentor_id='$sid' AND meet_id='$meet_id'";
        $result = $mysqli->query($query);

        $query = "SELECT * FROM enroll2 WHERE mentor_id='$sid'";
        $result = $mysqli->query($query);

        echo "<h1>You have left meeting $meet_name as a mentor</h1>";
      }
      else if(!$conflict){
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

    else if(isset($_POST['submit_all_join'])) { // submit2 means mentor
      $mysqli = new mysqli('localhost', 'root', '', 'DB2'); //The Blank string is the password

      $printed = false;

      $sid = $_SESSION['sid'];
      $meet_id = $_POST['submit_all_join'];

      $query = "SELECT * FROM meetings WHERE meet_id='$meet_id'";
      $result = $mysqli->query($query);
      $row = mysqli_fetch_array($result);
      $meet_name = $row['meet_name'];
      $meet_ts = $row['time_slot_id'];
      $meet_group = $row['group_id'];

      $am_query = "SELECT * FROM meetings WHERE meet_name='$meet_name' AND time_slot_id='$meet_ts' AND group_id='$meet_group' AND date>='$today'";
      $am_result = $mysqli->query($am_query);

      while($am_row = mysqli_fetch_array($am_result)) {
        $meet_id = $am_row['meet_id'];

        $query = "SELECT * FROM enroll WHERE mentee_id='$sid' AND meet_id='$meet_id'";
        $result = $mysqli->query($query);

        $conflict = mentee_conflict($sid, $meet_id, $mysqli);

        if(!$conflict && $result->num_rows === 0){
          $query = "SELECT * FROM mentees WHERE mentee_id='$sid'";
          $result = $mysqli->query($query);

          if($result->num_rows === 0) {
            $query = "INSERT INTO mentees (mentee_id) VALUES ({$sid})";
            $result = $mysqli->query($query);
          }

          $query = "INSERT INTO enroll (meet_id, mentee_id) VALUES ({$meet_id}, {$sid})";
          $result = $mysqli->query($query);
          if(!$printed) {
            echo "<h1>You have joined reccuring $meet_name meeting as a mentee</h1>";
            $printed = true;
          }
        }
      }
      $mysqli->close();
    }

    else if(isset($_POST['submit_all_leave'])) { // submit2 means mentor
      $mysqli = new mysqli('localhost', 'root', '', 'DB2'); //The Blank string is the password

      $printed = false;

      $sid = $_SESSION['sid'];
      $meet_id = $_POST['submit_all_leave'];

      $query = "SELECT * FROM meetings WHERE meet_id='$meet_id'";
      $result = $mysqli->query($query);
      $row = mysqli_fetch_array($result);
      $meet_name = $row['meet_name'];
      $meet_ts = $row['time_slot_id'];
      $meet_group = $row['group_id'];

      $am_query = "SELECT * FROM meetings WHERE meet_name='$meet_name' AND time_slot_id=$meet_ts AND group_id=$meet_group";
      $am_result = $mysqli->query($am_query);

      while($am_row = mysqli_fetch_array($am_result)) {
        $meet_id = $am_row['meet_id'];

        $query = "SELECT * FROM enroll WHERE mentee_id='$sid' AND meet_id='$meet_id'";
        $result = $mysqli->query($query);

        $conflict = mentee_conflict($sid, $meet_id, $mysqli);

        if($result->num_rows !== 0 && !$conflict) {
          $query = "DELETE FROM enroll WHERE mentee_id='$sid' AND meet_id='$meet_id'";
          $result = $mysqli->query($query);

          $query = "SELECT * FROM enroll WHERE mentee_id='$sid'";
          $result = $mysqli->query($query);

          if(!$printed) {
            echo "<h1>You have left recurring $meet_name meeting as a mentee</h1>";
            $printed = true;
          }
        }
      }
      $mysqli->close();
    }

    else if(isset($_POST['submit2_all_join'])) { // submit2 means mentor
      $mysqli = new mysqli('localhost', 'root', '', 'DB2'); //The Blank string is the password

      $printed = false;

      $sid = $_SESSION['sid'];
      $meet_id = $_POST['submit2_all_join'];

      $query = "SELECT * FROM meetings WHERE meet_id='$meet_id'";
      $result = $mysqli->query($query);
      $row = mysqli_fetch_array($result);
      $meet_name = $row['meet_name'];
      $meet_ts = $row['time_slot_id'];
      $meet_group = $row['group_id'];

      $am_query = "SELECT * FROM meetings WHERE meet_name='$meet_name' AND time_slot_id='$meet_ts' AND group_id='$meet_group' AND date>='$today'";
      $am_result = $mysqli->query($am_query);

      while($am_row = mysqli_fetch_array($am_result)) {
        $meet_id = $am_row['meet_id'];

        $query = "SELECT * FROM enroll2 WHERE mentor_id='$sid' AND meet_id='$meet_id'";
        $result = $mysqli->query($query);

        $conflict = mentor_conflict($sid, $meet_id, $mysqli);

        if(!$conflict && $result->num_rows === 0){
          $query = "SELECT * FROM mentors WHERE mentor_id='$sid'";
          $result = $mysqli->query($query);

          if($result->num_rows === 0) {
            $query = "INSERT INTO mentors (mentor_id) VALUES ({$sid})";
            $result = $mysqli->query($query);
          }

          $query = "INSERT INTO enroll2 (meet_id, mentor_id) VALUES ({$meet_id}, {$sid})";
          $result = $mysqli->query($query);
          if(!$printed) {
            echo "<h1>You have joined reccuring $meet_name meeting as a mentor</h1>";
            $printed = true;
          }
        }
      }
      $mysqli->close();
    }

    else if(isset($_POST['submit2_all_leave'])) { // submit2 means mentor
      $mysqli = new mysqli('localhost', 'root', '', 'DB2'); //The Blank string is the password

      $printed = false;

      $sid = $_SESSION['sid'];
      $meet_id = $_POST['submit2_all_leave'];

      $query = "SELECT * FROM meetings WHERE meet_id='$meet_id'";
      $result = $mysqli->query($query);
      $row = mysqli_fetch_array($result);
      $meet_name = $row['meet_name'];
      $meet_ts = $row['time_slot_id'];
      $meet_group = $row['group_id'];

      $am_query = "SELECT * FROM meetings WHERE meet_name='$meet_name' AND time_slot_id='$meet_ts' AND group_id='$meet_group' AND date>='$today'";
      $am_result = $mysqli->query($am_query);

      while($am_row = mysqli_fetch_array($am_result)) {
        $meet_id = $am_row['meet_id'];

        $query = "SELECT * FROM enroll2 WHERE mentor_id='$sid' AND meet_id='$meet_id'";
        $result = $mysqli->query($query);

        $conflict = mentor_conflict($sid, $meet_id, $mysqli);

        if($result->num_rows !== 0 && !$conflict) {
          $query = "DELETE FROM enroll2 WHERE mentor_id='$sid' AND meet_id='$meet_id'";
          $result = $mysqli->query($query);

          $query = "SELECT * FROM enroll2 WHERE mentor_id='$sid'";
          $result = $mysqli->query($query);

          if(!$printed) {
            echo "<h1>You have left recurring $meet_name meeting as a mentor</h1>";
            $printed = true;
          }
        }
      }
      $mysqli->close();
    }

    else if(isset($_POST['leave_all_mentee'])) { // submit2 means mentor
      $mysqli = new mysqli('localhost', 'root', '', 'DB2'); //The Blank string is the password

      $sid = $_SESSION['sid'];

      $query = "DELETE FROM enroll WHERE mentee_id='$sid'";
      $result = $mysqli->query($query);

      echo "<h1>You have left all meetings as a mentee</h1>";

      $mysqli->close();
    }

    else if(isset($_POST['leave_all_mentor'])) { // submit2 means mentor
      $mysqli = new mysqli('localhost', 'root', '', 'DB2'); //The Blank string is the password

      $sid = $_SESSION['sid'];

      $query = "DELETE FROM enroll2 WHERE mentor_id='$sid'";
      $result = $mysqli->query($query);


      echo "<h1>You have left all meetings as a mentor</h1>";

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

    $mysqli = new mysqli('localhost', 'root', '', 'DB2'); //The Blank string is the password

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
