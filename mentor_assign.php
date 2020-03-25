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
  ?>
  <?php

    echo '<h1>Welcome, ' . $_SESSION['name'] . '</h1>';

    echo "<form action=\"landing.php\">
          Return to main page:
          <input type=\"submit\" value=\"Main Page\"></input><br><br>
          </form>
          Return to Assign to Meetings Page:
          <form action=\"meeting_assign.php\">
            <input type=\"submit\" value=\"Assign to Meetings\"></input><br>
          </form><br><br>";

    if(isset($_POST['assign'])) { // submit being set means mentee
        $mysqli = new mysqli('localhost', 'root', '', 'DB2'); //The Blank string is the password

        $sid = $_POST['assign'];
        $meet_id = $_SESSION['mid'];
        $query = "INSERT INTO enroll2 (meet_id, mentor_id) VALUES ({$meet_id}, {$sid})";
        $result = $mysqli->query($query);

        $query = "SELECT * FROM users WHERE id=$sid";
        $result = $mysqli->query($query);
        $row = mysqli_fetch_array($result);

        $name = $row['name'];
        $email = $row['email'];
        $phone = $row['phone'];
        $contents = $name . ' | ' . $email . ' | ' . $phone . "\n";

        $file = 'mentor_add_notification.txt';

        if(!is_file($file)){
          file_put_contents($file, $contents);
        }
        else {
          $fp = fopen('mentor_add_notification.txt', 'a');//opens file in append mode
          fwrite($fp, $contents);
          fclose($fp);
        }

        $mysqli->close();
    }
    if(isset($_POST['remove'])) { // submit being set means mentee
        $mysqli = new mysqli('localhost', 'root', '', 'DB2'); //The Blank string is the password

        $sid = $_POST['remove'];
        $meet_id = $_SESSION['mid'];
        $query = "DELETE FROM enroll2 WHERE mentor_id=$sid AND meet_id=$meet_id";
        $result = $mysqli->query($query);

        $query = "SELECT * FROM users WHERE id=$sid";
        $result = $mysqli->query($query);
        $row = mysqli_fetch_array($result);

        $name = $row['name'];
        $email = $row['email'];
        $phone = $row['phone'];
        $contents = $name . ' | ' . $email . ' | ' . $phone . "\n";

        $file = 'mentor_remove_notification.txt';

        if(!is_file($file)){
          file_put_contents($file, $contents);
        }
        else {
          $fp = fopen('mentor_remove_notification.txt', 'a');//opens file in append mode
          fwrite($fp, $contents);
          fclose($fp);
        }

        $mysqli->close();
    }
    if (isset($_POST['submit'])) {
      unset($_SESSION['mid']);
      $_SESSION['mid'] = $_POST['submit'];
    }

      $meet_id = $_SESSION['mid'];
      $mysqli = new mysqli('localhost', 'root', '', 'DB2'); //The Blank string is the password

      $meeting_query = "SELECT * FROM meetings WHERE meet_id=$meet_id"; //You don't need a ; like you do in SQL
      $result = $mysqli->query($meeting_query);

      echo '<h1>Meeting</h1>';

      echo "<table>"; // start a table tag in the HTML

      echo "<tr><td>" . "Name" . "</td><td>" . "Date" . "</td>
            <td>" . "Announcement" . "</td><td>" . "Day of the Week" . "</td>
            <td>" . "Grade" . "</td><td>" . "Start Time" . "</td><td>" . "End Time" . "</td>
            <td>" . "Mentee Count" . "</td><td>" . "Mentor Count" . "</td></tr>";

      $row = mysqli_fetch_array($result);
      $tid = $row['time_slot_id'];
      $time_query = "SELECT * FROM time_slot WHERE time_slot_id ='$tid'";
      $tresult = $mysqli->query($time_query);
      $trow = mysqli_fetch_array($tresult);
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
            <td>". $mentor_count . "</td></tr>";
      echo "</table><br><br>";


      $query = "SELECT * FROM meetings WHERE meet_id='$meet_id'";
      $result = $mysqli->query($query);
      $row = mysqli_fetch_array($result);
      $meet_name = $row['meet_name'];
      $group = $row['group_id'];

      $req_query = "SELECT * FROM groups WHERE group_id=$group";
      $req_result = $mysqli->query($req_query);
      $req_row = mysqli_fetch_array($req_result);
      $grade_req = $req_row['mentor_grade_req'];

      $query = "SELECT * FROM users WHERE id IN(
         SELECT mentor_id FROM mentors) AND id IN(
           SELECT student_id FROM students WHERE grade>=$grade_req)";

      echo '<h1>Possible Mentors</h1>';
      echo "<table>";
      echo "<tr><td>" . "name" . "</td> <td>" . "email" . "</td>
            <td>" . "phone" . "</td><td>" . "grade" . "</td></tr>";  //$row['index'] the index here is a field name
      $result = $mysqli->query($query);
      while($row = mysqli_fetch_array($result)){
        $sid = $row['id'];
        $mentor_query = "SELECT * FROM enroll2 WHERE mentor_id=$sid AND meet_id=$meet_id";
        $mentor_result = $mysqli->query($mentor_query);
        $student_query = "SELECT * FROM students WHERE student_id=$sid";
        $student_result = $mysqli->query($student_query);
        $student_row = mysqli_fetch_array($student_result);
        $mentor_grade = $student_row['grade'];
        if($mentor_result->num_rows === 0 ) {
          $conflict = mentor_conflict($sid, $meet_id, $mysqli);

          if(!$conflict){
            echo "<tr><td>" . $row['name'] . "</td> <td>" . $row['email'] . "</td>
                   <td>" . $row['phone'] . "</td><td>" . $mentor_grade;  //$row['index'] the index here is a field name
            echo  "</td><td>
                  <form action=\"mentor_assign.php\" method=\"post\">
                    <button name=\"assign\" value=\"$sid\" type=\"submit\">Assign</button>
                 </form></td></tr>";
          }
        }
        else {
          echo "<tr><td>" . $row['name'] . "</td> <td>" . $row['email'] . "</td>
                 <td>" . $row['phone'] . "</td><td>" . $mentor_grade;  //$row['index'] the index here is a field name
          echo  "</td><td>
                <form action=\"mentor_assign.php\" method=\"post\">
                  <button name=\"remove\" value=\"$sid\" type=\"submit\">Remove From Meeting</button>
               </form></td></tr>";
        }
      }
      echo "</table>";
      $mysqli->close();
  ?>

</body>
</html>
