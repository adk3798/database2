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
  <?php endif; ?>
  <?php if(isset($_SESSION['pid'])) : ?>
    <?php
      unset($_SESSION['sid']);
      $pid = $_SESSION['pid'];
      $mysqli = new mysqli('localhost', 'root', '', 'db2_project'); //The Blank string is the password

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
    $mysqli = new mysqli('localhost', 'root', '', 'db2_project'); //The Blank string is the password
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
  <?php endif; ?>
    <?php

      $mysqli = new mysqli('localhost', 'root', '', 'db2_project'); //The Blank string is the password

      $query = "SELECT * FROM users"; //You don't need a ; like you do in SQL
      $result = $mysqli->query($query);
      echo "<h1>Users</h1>";
      echo "<table>"; // start a table tag in the HTML

      while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through results
        echo "<tr><td>" . $row['id'] . "</td><td>" . $row['email'] . "</td>
              <td>" . $row['password'] . "</td><td>" . $row['name'] . "</td>
              <td>" . $row['phone'] . "</td></tr>";  //$row['index'] the index here is a field name
      }

      echo "</table>"; //Close the table in HTML
      $query = "SELECT parent_id FROM parents"; //You don't need a ; like you do in SQL
      $result = $mysqli->query($query);
      echo "<h1>Parents</h1>";
      echo "<table>"; // start a table tag in the HTML

      while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through results
        echo "<tr><td>" . $row['parent_id'] . "</td></tr>";  //$row['index'] the index here is a field name
      }

      echo "</table>"; //Close the table in HTML
      $query = "SELECT * FROM students"; //You don't need a ; like you do in SQL
      $result = $mysqli->query($query);
      echo "<h1>Students</h1>";
      echo "<table>"; // start a table tag in the HTML

      while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through results
        echo "<tr><td>" . $row['student_id'] . "</td><td>" . $row['grade'] . "</td>
              <td>" . $row['parent_id'] . "</td></tr>";  //$row['index'] the index here is a field name
      }

      echo "</table>"; //Close the table in HTML

      $mysqli->close();

    ?>
</body>
</html>
