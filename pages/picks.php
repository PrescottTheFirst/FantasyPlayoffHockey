<html>
<head>
    <?php
	require_once "../data_access.php";
	date_default_timezone_set('America/Toronto');
	$date2 = strtotime("04/15/2012 19:00:00");
	$date = time();

	$db = new DataAccess();
	$row = $db->get_current_round_and_year();
	$round = $row['round'];
	$year = $row['year'];
	session_start();
	if(!isset($_SESSION['loggedin']))
	{
    	echo " <script type=\"text/javascript\">
            <!--
                window.location = \"login.php\"
            //-->
            </script> ";
	}
    ?>
    <title>Fantasy Playoff Hockey</title>
    <LINK href="default.css" rel="stylesheet" type="text/css">
</head>
<body>
    <form id="picks-form" action="picks.php" method="POST">
	<h1 align="center">Fantasy Playoff Hockey</h1>
	<table class="main" align="center">
	    <tr><td colspan=8 align="center">
	        <p class="Header" align="center">Make your Picks for Round <?php echo $round ?>
		 </p>
	    </td></tr>
	    <tr>
		<td colspan=3 align=center><p>Top Seed</p></td>
		<td colspan=3 align=center><p>Bottom Seed</p></td>
		<td colspan=1 align=center><p>Games</p></td>
		<td colspan=1 align=center><p>Goal Diff</p></td>
	    </tr>
<?php
	require_once "../matchups.php";
	$matchups = $db->get_matchups($round, $year);
	while ($row = mysql_fetch_array($matchups)) {
		$matchup = new Matchup($row);
		if ($_POST['pick'.$matchup->matchid] === $matchup->top_seed_team . "_" . $matchup->matchid) {
			$topPick = "checked";
			$bottomPick = "";
		} elseif ($_POST['pick'.$matchup->matchid] === $matchup->bottom_seed_team . "_" . $matchup->matchid) {
			$topPick = "";
			$bottomPick = "checked";
		} else {
			$topPick = "";
			$bottomPick = "";
		}
	    	echo "
		<tr>
		<td colspan=2>
		    <p class=\"table-entry\">" . $matchup->top_seed_rank . " " . $matchup->top_seed_team . "
		</td>
		<td colspan=1>
		    <Input type=\"radio\" ". $topPick ." value=\"" . $matchup->top_seed_team . "_" . $matchup->matchid . "\" align=\"right\" name=\"pick".$matchup->matchid . "\"/></p>
		</td>
		<td colspan=2>
		    <p class=\"table-entry\">" . $matchup->bottom_seed_rank . " " . $matchup->bottom_seed_team . "
		</td>
		<td colspan=1>
		    <input type=\"radio\" " . $bottomPick . " value=\"" . $matchup->bottom_seed_team . "_" . $matchup->matchid . "\" align=\"right\" name=\"pick" . $matchup->matchid . "\"/></p>
		</td>
		<td colspan=1>
		    <input class=\"number\" type=\"integer\" name=\"games" . $matchup->matchid . "\" value=\"" . $_POST['games' . $matchup->matchid] . "\" MAXLENGTH=1/>
		</td>
		<td colspan=1>
		    <input class=\"number\" type=\"integer\" name=\"goals" . $matchup->matchid . "\" value=\"" . $_POST['goals' . $matchup->matchid] . "\" MAXLENGTH=2/>	
		</td>
	    </tr>";
	}
?>
	<tr><td colspan=8 align="center"><input type="submit" value="Submit"></td></tr>
	</table>
    </form>

<?php
        if (count($_POST) > 0) {
		if ($date > $date2) {
			echo "<p class=\"Error\">Start of round " . $round . " has passed.</p>";
		}
		foreach ($_POST as $entry){
			if (!intval($entry) && strlen($entry) > 2) {		
				$array = explode("_",$entry);
				$matchid = $array[1];
				$pick = $array[0];
				$games = $_POST['games' . $matchid];
				$goal_diff = $_POST['goals' . $matchid];
				if (!(intval($games)) || (!intval($goal_diff)) || ($games < 4) || ($games > 7)) {
					echo "<p class=\"Error\"> Invalid Entry </p>";
					break;
				}
				$userid = $_SESSION['userid'];
				$db->insert_pick($userid, $matchid, $pick, $games, $goal_diff);
			}
		}
	}
?>
</body>
</html>
