<!DOCTYPE html>
<html>
<body>
<?php
  $host="localhost";
  $servername = "nhlstats";
  $username = "nhlBuilder";
  $password = "BuildTheStats";

  // Create connection
  $conn = mysqli_connect($host, $username, $password, $servername);

  // Check connection
  if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
  }

  $delSQL = "DELETE FROM nhlteams";
  $query = $conn->query($delSQL);

  $content = file_get_contents("http://statsapi.web.nhl.com/api/v1/teams");
  $result = json_decode($content);

  $insertSQL = "INSERT INTO nhlteams (`teamID`, `location`, `teamName`, `abbr`) VALUES ";
  $count = 0;
  foreach ($result->teams as $team) {
      if ($count == 0) {
          $insertSQL .= "('" . $team->id . "', '" . $team->locationName . "', '" . $team->teamName . "', '" . $team->abbreviation . "')";
      } else {
          $insertSQL .= ", ('" . $team->id . "', '" . $team->locationName . "', '" . $team->teamName . "', '" . $team->abbreviation . "')";
      }
      $count++;
  }
  echo $insertSQL;
  $query = $conn->query($insertSQL);

?>

</body>
</html>
