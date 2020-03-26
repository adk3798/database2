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

    // cancel meetings with fewer than 3 mentees when it is Friday
    $day_of_week = date('l');
    $today = date('Y-m-d');
    /*$day_of_week = "Friday";
    $today = "2020-04-10";*/
    if($day_of_week === "Friday") {
      $mysqli = new mysqli('localhost', 'root', '', 'DB2'); //The Blank string is the password
      $meeting_query = "SELECT * FROM meetings WHERE date>='$today' ORDER BY date ASC"; //You don't need a ; like you do in SQL
      $result = $mysqli->query($meeting_query);
      while($row = mysqli_fetch_array($result)) {
        $meet_date = $row['date'];
        $meet_id = $row['meet_id'];
        $subject = $row['meet_name'];
        $dt1 = date_create($meet_date);
        $dt2 = date_create($today);
        $diff = date_diff($dt1, $dt2, true);
        if((int)$diff->format('%a') <= 2) {
          $mentee_query = "SELECT * FROM enroll WHERE meet_id=$meet_id";
          $mentee_result = $mysqli->query($mentee_query);
          $mentee_count = $mentee_result->num_rows;
          if($mentee_count < 3) {
            // notify students of cancellation
            while($mentee_row = mysqli_fetch_array($mentee_result)) {
              $sid = $mentee_row['mentee_id'];
              $mentee_query2 = "SELECT * FROM users WHERE id=$sid";
              $mentee_result2 = $mysqli->query($mentee_query2);
              $mentee_row2 = mysqli_fetch_array($mentee_result2);
              $name = $mentee_row2['name'];
              $email = $mentee_row2['email'];
              $phone = $mentee_row2['phone'];
              $contents = $name . ' | ' . $email . ' | ' . $phone . ' | ' . $subject . ' | ' . $meet_date . "\n";

              $file = 'cancel_notification.txt';

              if(!is_file($file)){
                file_put_contents($file, $contents);
              }
              else {
                $fp = fopen('cancel_notification.txt', 'a');//opens file in append mode
                fwrite($fp, $contents);
                fclose($fp);
              }
            }
            $mentor_query = "SELECT * FROM enroll2 WHERE meet_id=$meet_id";
            $mentor_result = $mysqli->query($mentor_query);
            while($mentor_row = mysqli_fetch_array($mentor_result)) {
              $sid = $mentor_row['mentor_id'];
              $mentor_query2 = "SELECT * FROM users WHERE id=$sid";
              $mentor_result2 = $mysqli->query($mentor_query2);
              $mentor_row2 = mysqli_fetch_array($mentor_result2);
              $name = $mentor_row2['name'];
              $email = $mentor_row2['email'];
              $phone = $mentor_row2['phone'];
              $contents = $name . ' | ' . $email . ' | ' . $phone . ' | ' . $subject . ' | ' . $meet_date . "\n";

              $file = 'cancel_notification.txt';

              if(!is_file($file)){
                file_put_contents($file, $contents);
              }
              else {
                $fp = fopen('cancel_notification.txt', 'a');//opens file in append mode
                fwrite($fp, $contents);
                fclose($fp);
              }
            }
            $delete_query = "DELETE FROM meetings WHERE meet_id=$meet_id";
            $delete_result = $mysqli->query($delete_query);
          }
        }
      }
    }
  ?>
  <?php if(isset($_SESSION['name'])) : ?>
    <?php
      echo '<h1>Welcome, ' . $_SESSION['name'] . '</h1>';
      echo "<form action=\"edit_info.php\">
        Edit User Info:
        <input type=\"submit\" value=\"Edit Information\"></input><br><br>
      </form>";
      echo "<form action=\"logout.php\">
        Logout:
        <input type=\"submit\" value=\"Logout\"></input><br><br>
      </form>";
    ?>
  <?php else : ?>
    <h1> Login or Register </h1>
    <form action="student_registration.php">
      <input type="submit" value="Register a Student"></input><br>
    </form>
    <form action="student_login.php">
      <input type="submit" value="Login as Student"></input><br><br>
    </form>
    <form action="parent_registration.php">
      <input type="submit" value="Register a Parent"></input><br>
    </form>
    <form action="parent_login.php">
      <input type="submit" value="Login as Parent"></input><br><br>
    </form>
    <form action="admin_login.php">
      <input type="submit" value="Login as Admin"></input><br><br>
    </form>
  <?php endif; ?>
  <?php if(isset($_SESSION['pid'])) : ?>
    <?php
      unset($_SESSION['sid']);
      $pid = $_SESSION['pid'];
      $mysqli = new mysqli('localhost', 'root', '', 'DB2'); //The Blank string is the password

      $query = "SELECT * FROM users WHERE id IN(SELECT student_id FROM students WHERE parent_id='$pid')"; //You don't need a ; like you do in SQL
      $result = $mysqli->query($query);
      echo "<h1> Sign Up Student for Meetings</h1>";
      while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through results
        $sid = $row['id'];
        $name = $row['name'];
        echo  "<form action=\"meeting_signup.php\" method=\"post\">
                <label for=\"parent_join\">$name:</label>
                <button name=\"parent_join\" value=\"$sid\" type=\"submit\">Meeting Signup</button><br><br>
                </form>
              ";
      }
      $mysqli->close();
    ?>
    <?php
    unset($_SESSION['sid']);
    $pid = $_SESSION['pid'];
    $mysqli = new mysqli('localhost', 'root', '', 'DB2'); //The Blank string is the password
      $query = "SELECT * FROM users WHERE id IN(SELECT student_id FROM students WHERE parent_id='$pid')"; //You don't need a ; like you do in SQL
      $result = $mysqli->query($query);
      echo "<h1> Edit Student's Info</h1>";
      while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through results
        $sid = $row['id'];
        $name = $row['name'];
        echo  "<form action=\"edit_info.php\" method=\"post\">
              <label for=\"parent_join\">$name:</label>
              <button name=\"parent_edit\" value=\"$sid\" type=\"submit\">Edit Info</button><br><br>
              </form>
            ";
    }
      $mysqli->close();

    ?>
  <?php elseif(isset($_SESSION['sid'])) : ?>
    <h1> Meeting Sign Up </h1>
    <form action="meeting_signup.php">
      <input type="submit" value="Signup for Meetings"></input><br>
    </form><br><br>
  <?php elseif(isset($_SESSION['aid'])) : ?>
    <h1> Post Study Materials </h1>
    <form action="study_materials.php">
      <input type="submit" value="Post Study Materials"></input><br>
    </form><br><br>
    <h1> Assign to Meetings </h1>
    <form action="meeting_assign.php">
      <input type="submit" value="Assign to Meetings"></input><br>
    </form><br><br>
  <?php endif; ?>
</body>
</html>
