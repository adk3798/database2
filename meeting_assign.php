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

      $mysqli = new mysqli('localhost', 'root', '', 'db2_project'); //The Blank string is the password

      echo '<h1>Welcome, ' . $_SESSION['name'] . '</h1>';

      echo "<form action=\"landing.php\">
            Return to main page:
            <input type=\"submit\" value=\"Main Page\"></input><br><br>
            </form>";

      $today = date("Y-m-d");

      $meeting_query = "SELECT * FROM meetings WHERE date>='$today' ORDER BY date ASC"; //You don't need a ; like you do in SQL
      $result = $mysqli->query($meeting_query);

      echo '<h1>Meetings</h1>';

      echo "<table>"; // start a table tag in the HTML

      echo "<tr><td>" . "Name" . "</td><td>" . "Date" . "</td>
            <td>" . "Announcement" . "</td><td>" . "Day of the Week" . "</td>
            <td>" . "Grade" . "</td><td>" . "Start Time" . "</td><td>" . "End Time" . "</td>
            <td>" . "Mentee Count" . "</td><td>" . "Mentor Count" . "</td></tr>";

      while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through results
        $tid = $row['time_slot_id'];
        $time_query = "SELECT * FROM time_slot WHERE time_slot_id ='$tid'";
        $tresult = $mysqli->query($time_query);
        $trow = mysqli_fetch_array($tresult);
        $meet_id = $row['meet_id'];
        $mentee_query = "SELECT * FROM enroll WHERE meet_id=$meet_id";
        $mentee_result = $mysqli->query($mentee_query);
        $mentee_count = $mentee_result->num_rows;
        $mentor_query = "SELECT * FROM enroll2 WHERE meet_id=$meet_id";
        $mentor_result = $mysqli->query($mentor_query);
        $mentor_count = $mentor_result->num_rows;
        echo "<tr><td>" . $row['meet_name'] . "</td><td>" . $row['date'] . "</td>
              <td>" . $row['announcement'] . "</td><td>" . $trow['day_of_the_week'] . "</td>
              <td>" . $row['group_id'] . "</td><td>" . $trow['start_time'] . "</td>
              <td>". $trow['end_time'] . "</td><td>". $mentee_count . "</td>
              <td>". $mentor_count;

        echo  "</td><td>
              <form action=\"mentee_assign.php\" method=\"post\">
                <button name=\"submit\" value=\"$meet_id\" type=\"submit\">Assign Mentees</button>
              </form></td>";
        echo  "</td><td>
                <form action=\"mentor_assign.php\" method=\"post\">
                  <button name=\"submit\" value=\"$meet_id\" type=\"submit\">Assign Mentors</button>
              </form></td></tr>";
      }

      $mysqli->close();
    ?>

</body>
</html>
