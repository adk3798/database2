<!DOCTYPE html>
<html lang="en">
<!--
File: test page for database 2 project
Adam M. King, UMass Lowell Computer Science Student
adam_king@student.uml.edu
uml.cs username: aking
Updated February 24th, 2020
-->
<head>
    <title>Database 2</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <?php
      session_start();
      session_destroy();
      echo 'You have successfully logged out';
    ?>

    <form action="landing.php">
      Return to main page:
      <input type="submit" value="Main Page"></input><br><br>
    </form>
</body>
</html>
