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

  $gameSQL = "SELECT gameID
         FROM nhlgames";
  $gameQuery = $conn->query($gameSQL);

  $penalties=array();

  while ($gameRow = mysqli_fetch_array($gameQuery)) {
      $delSQL = "DELETE FROM nhlshots WHERE gameID='" . $gameRow["gameID"];
      $query = $conn->query($delSQL);

      $content = file_get_contents("http://statsapi.web.nhl.com/api/v1/game/" . $gameRow["gameID"] . "/feed/live");
      $result = json_decode($content);

      $insertSQL = "INSERT INTO nhlshots (`shotID`, `playerID`, `xCord`, `yCord`, `gameID`, `teamID`) VALUES ";
      $count = 0;
      foreach ($result->liveData->plays->allPlays as $play) {
          if ($play->result->eventTypeId == "SHOT" || $play->result->eventTypeId == "GOAL") {
              if ($count == 0) {
                  $insertSQL .= "('" . $gameRow["gameID"] . sprintf('%03u', $play->about->eventIdx) . "', '"
                       . $play->players[0]->player->id . "', '"
                       . $play->coordinates->x . "', '"
                       . $play->coordinates->y . "', '"
                       . $gameRow["gameID"] . "', '"
                       . $play->team->id . "')";
              } else {
                  $insertSQL .= ", ('" . $gameRow["gameID"] . sprintf('%03u', $play->about->eventIdx) . "', '"
                      . $play->players[0]->player->id . "', '"
                      . $play->coordinates->x . "', '"
                      . $play->coordinates->y . "', '"
                      . $gameRow["gameID"] . "', '"
                      . $play->team->id . "')";
              }
              $count++;
          }
      }
      echo $insertSQL;
      echo "<BR><BR>";
      $query = $conn->query($insertSQL);
  }


?>

</body>
</html>
