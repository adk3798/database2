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

    if (isset($_POST['parent_join'])) { // submit being set means mentee
      $sid = $_POST['parent_join'];
      $_SESSION['sid'] = $sid;
    }

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
            //echo '<br>' . $meet_ts . '   ' . $meet2_ts . '<br>';
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

  <?php if(isset($_SESSION['sid'])) : ?>
    <?php

      $mysqli = new mysqli('localhost', 'root', '', 'db2_project'); //The Blank string is the password

      $sid = $_SESSION['sid'];

      $query = "SELECT name FROM users WHERE id = '$sid'";
      $result = $mysqli->query($query);
      $row = mysqli_fetch_array($result);
      $sname = $row['name'];

      $query = "SELECT grade FROM students WHERE student_id = '$sid'";
      $result = $mysqli->query($query);
      $row = mysqli_fetch_array($result);
      $grade = $row['grade'];

      echo '<h1>Welcome, ' . $_SESSION['name'] . '</h1>';
      echo "<h2>Currently signing <span style=\"color:red\"> $sname </span> up for meetings</h2>";
      echo '<p>Meetings you can currently signup for will have a "Join Meeting" button next to them</p>';
      echo '<p>Meetings you are currently enrolled in will have a "Leave Meeting" button next to them</p>';
      echo '<p>Meetings you could be eligible to sign up for but can\'t due to conflicts will be displayed but have no join/leave option.</p>';
      echo '<p>Meetings you are enrolled in as a mentor will have a "Meeting Info" button that will allow you to see the current mentors and mentees for that meeting.</p>';
      echo '<p>The "Join Recurring Meetings" button will sign up you up for every meeting for the rest of the school year for that subject and time slot where there aren\'t conflicts</p>';

      $today = date("Y-m-d");

      $mentee_query =
        "SELECT * FROM meetings WHERE group_id IN
          (SELECT group_id FROM groups WHERE description='$grade' AND date>='$today')
        ORDER BY date ASC"; //You don't need a ; like you do in SQL
      $result2 = $mysqli->query($mentee_query);

      echo '<h1>Meetings student can mentee</h1>';

      echo "<table>"; // start a table tag in the HTML

      echo "<tr><td>" . "Name" . "</td><td>" . "Date" . "</td>
            <td>" . "Announcement" . "</td><td>" . "Day of the Week" . "</td>
            <td>" . "Start Time" . "</td><td>" . "End Time" . "</td></tr>";

      while($row = mysqli_fetch_array($result2)){   //Creates a loop to loop through results
        $tid = $row['time_slot_id'];
        $time_query = "SELECT * FROM time_slot WHERE time_slot_id ='$tid'";
        $tresult = $mysqli->query($time_query);
        $trow = mysqli_fetch_array($tresult);
        $meet_id = $row['meet_id'];
        echo "<tr><td>" . $row['meet_name'] . "</td><td>" . $row['date'] . "</td>
              <td>" . $row['announcement'] . "</td><td>" . $trow['day_of_the_week'] . "</td>
              <td>" . $trow['start_time'] . "</td><td>" . $trow['end_time'];
        $query = "SELECT * FROM enroll WHERE mentee_id='$sid' AND meet_id='$meet_id'";
        $result = $mysqli->query($query);
        $conflict = mentee_conflict($sid, $meet_id, $mysqli);
        if($result->num_rows === 0 && !$conflict) {
          echo  "</td><td>
                <form action=\"meeting_info.php\" method=\"post\">
                  <button name=\"submit\" value=\"$meet_id\" type=\"submit\">Join Individual Meeting</button>
                </form></td>";
          echo  "</td><td>
                  <form action=\"meeting_info.php\" method=\"post\">
                    <button name=\"submit_all_join\" value=\"$meet_id\" type=\"submit\">Join Recurring Meetings</button>
                  </form></td></tr>";
        }
        else if($result->num_rows !== 0){
          echo  "</td><td>
                <form action=\"meeting_info.php\" method=\"post\">
                  <button name=\"submit\" value=\"$meet_id\" type=\"submit\">Leave Meeting</button>
                </form></td>";
          echo  "</td><td>
                  <form action=\"meeting_info.php\" method=\"post\">
                    <button name=\"submit_all_leave\" value=\"$meet_id\" type=\"submit\">Leave Recurring Meetings</button>
                  </form></td></tr>";
        }
      }

      echo "</table><br><br>"; //Close the table in HTML

      echo  "<form action=\"meeting_info.php\" method=\"post\">
              <button name=\"leave_all_mentee\" value=\"-1\" type=\"submit\">LEAVE ALL MEETINGS AS MENTEE</button>
            </form><br><br>";

      $mentor_query =
        "SELECT * FROM meetings WHERE group_id IN
          (SELECT group_id FROM groups WHERE mentor_grade_req <='$grade' AND date>='$today')
        ORDER BY date ASC"; //You don't need a ; like you do in SQL
      $result2 = $mysqli->query($mentor_query);

      echo '<h1>Meetings student can mentor</h1>';

      echo "<table>"; // start a table tag in the HTML

      echo "<tr><td>" . "Name" . "</td><td>" . "Date" . "</td>
            <td>" . "Announcement" . "</td><td>" . "Day of the Week" . "</td>
            <td>" . "Start Time" . "</td><td>" . "End Time" . "</td></tr>";

      while($row = mysqli_fetch_array($result2)){   //Creates a loop to loop through results
        $tid = $row['time_slot_id'];
        $time_query = "SELECT * FROM time_slot WHERE time_slot_id ='$tid'";
        $tresult = $mysqli->query($time_query);
        $trow = mysqli_fetch_array($tresult);
        $meet_id = $row['meet_id'];
        echo "<tr><td>" . $row['meet_name'] . "</td><td>" . $row['date'] . "</td>
              <td>" . $row['announcement'] . "</td><td>" . $trow['day_of_the_week'] . "</td>
              <td>" . $trow['start_time'] . "</td><td>" . $trow['end_time'];
        $query = "SELECT * FROM enroll2 WHERE mentor_id='$sid' AND meet_id='$meet_id'";
        $result = $mysqli->query($query);
        $conflict = mentor_conflict($sid, $meet_id, $mysqli);
        if($result->num_rows === 0 && !$conflict) {
          echo  "</td><td>
                <form action=\"meeting_info.php\" method=\"post\">
                  <button name=\"submit2\" value=\"$meet_id\" type=\"submit\">Join Individual Meeting</button>
                </form></td>";
          echo  "</td><td>
                  <form action=\"meeting_info.php\" method=\"post\">
                    <button name=\"submit2_all_join\" value=\"$meet_id\" type=\"submit\">Join Recurring Meetings</button>
                  </form></td></tr>";
        }
        else if($result->num_rows !== 0){
          echo  "</td><td>
                <form action=\"meeting_info.php\" method=\"post\">
                  <button name=\"submit2\" value=\"$meet_id\" type=\"submit\">Leave Individual Meeting</button>
                </form></td>";
          echo  "</td><td>
                  <form action=\"meeting_info.php\" method=\"post\">
                    <button name=\"submit2_all_leave\" value=\"$meet_id\" type=\"submit\">Leave Recurring Meetings</button>
                  </form></td>";
          echo  "</td><td>
                <form action=\"meeting_info.php\" method=\"post\">
                  <button name=\"info\" value=\"$meet_id\" type=\"submit\">Meeting info</button>
                </form></td></tr>";
        }
      }

      echo "</table><br><br>"; //Close the table in HTML

      $mysqli->close();

      echo  "<form action=\"meeting_info.php\" method=\"post\">
              <button name=\"leave_all_mentor\" value=\"-1\" type=\"submit\">LEAVE ALL MEETINGS AS MENTOR</button>
            </form><br><br>";
    ?>
  <?php else : ?>
    <h1>Error, no student currently selected. Please return to main page</h1>
  <?php endif; ?>
    <form action="landing.php">
      Return to main page:
      <input type="submit" value="Main Page"></input><br><br>
    </form>
</body>
</html>
