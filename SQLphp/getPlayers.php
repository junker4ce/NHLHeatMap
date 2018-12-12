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

  $teamSQL = "SELECT teamID
         FROM nhlteams";
  $teamQuery = $conn->query($teamSQL);

  while ($teamRow = mysqli_fetch_array($teamQuery)) {
      $delSQL = "DELETE FROM nhlplayers WHERE teamID='" . $teamRow["teamID"];
      $query = $conn->query($delSQL);

      $content = file_get_contents("http://statsapi.web.nhl.com/api/v1/teams/" . $teamRow["teamID"] . "?expand=team.roster");
      $result = json_decode($content);

      $insertSQL = "INSERT INTO nhlplayers (`playerID`, `fullName`, `teamID`, `jerseyNum`, `position`) VALUES ";
      $count = 0;
      foreach ($result->teams[0]->roster->roster as $player) {
          if ($count == 0) {
              $insertSQL .= "('" . $player->person->id . "', '" . $player->person->fullName . "', '" . $teamRow["teamID"] . "', '" . $player->jerseyNumber . "', '" . $player->position->code . "')";
          } else {
              $insertSQL .= ", ('" . $player->person->id . "', '" . $player->person->fullName . "', '" . $teamRow["teamID"] . "', '" . $player->jerseyNumber . "', '" . $player->position->code . "')";
          }
          $count++;
      }
      echo $insertSQL;
      echo "<BR><BR>";
      $query = $conn->query($insertSQL);
  }

?>

</body>
</html>
