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
  
  <?php if(isset($_SESSION['sid'])) : ?>
    <?php

      $mysqli = new mysqli('localhost', 'root', '', 'db2_project'); //The Blank string is the password

      $sid = $_SESSION['sid'];
      $query = "SELECT grade FROM students WHERE student_id = '$sid'";
      $result = $mysqli->query($query);
      $row = mysqli_fetch_array($result);
      $grade = $row['grade'];

      echo '<h1>Welcome, ' . $_SESSION['name'] . '</h1>';
      echo '<p>This student is in grade  ' . $grade . '</p>';

      $mentee_query =
        "SELECT * FROM meetings WHERE group_id IN
          (SELECT group_id FROM groups WHERE description='$grade')
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
        if($result->num_rows === 0) {
          echo  "</td><td>
                <form action=\"meeting_info.php\" method=\"post\">
                  <button name=\"submit\" value=\"$meet_id\" type=\"submit\">Join Meeting</button>
                </form></td></tr>";
        }
        else {
          echo  "</td><td>
                <form action=\"meeting_info.php\" method=\"post\">
                  <button name=\"submit\" value=\"$meet_id\" type=\"submit\">Leave Meeting</button>
                </form></td></tr>";
        }
      }

      echo "</table><br><br>"; //Close the table in HTML

      $mentor_query =
        "SELECT * FROM meetings WHERE group_id IN
          (SELECT group_id FROM groups WHERE mentor_grade_req <='$grade')
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
        if($result->num_rows === 0) {
          echo  "</td><td>
                <form action=\"meeting_info.php\" method=\"post\">
                  <button name=\"submit2\" value=\"$meet_id\" type=\"submit\">Join Meeting</button>
                </form></td></tr>";
        }
        else {
          echo  "</td><td>
                <form action=\"meeting_info.php\" method=\"post\">
                  <button name=\"submit2\" value=\"$meet_id\" type=\"submit\">Leave Meeting</button>
                </form></td>";
          echo  "</td><td>
                <form action=\"meeting_info.php\" method=\"post\">
                  <button name=\"info\" value=\"$meet_id\" type=\"submit\">Meeting info</button>
                </form></td></tr>";
        }
      }

      echo "</table><br><br>"; //Close the table in HTML

      $mysqli->close();

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
