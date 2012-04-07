<html>
    <?php
        require_once "../data_access.php";

	function calculate_points($winner_pick, $seed_diff, $games, $games_pick, $goal_diff_pick) {
		$points = 0;
		if (!$winner_pick) return $points;
		$points += 4;  // 4 points for correct winner
		if ($seed_diff > 0) {
			$points += ceil($seed_diff / 2);
		}
		if ($games_pick) {
			if ($games == 4) $points += 4;
			else $points += 2;
		}
		if ($goal_diff_pick) $points += 2;
		return $points;
	}

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
<head>
<LINK href="default.css" rel="stylesheet" type="text/css">
<title>Fantasy Playoff Hockey</title>
<h1 align="center">Fantasy Playoff Hockey</h1>
</head>
<body>
<h2 align="center">Results for round <?php echo $round ?></h2>
<p>User: <?php print_r($_SESSION['name']); ?><br></p>
<table align="center" cellpadding=2 border=1>
    <tr>
	<td colspan=2><p class="Header" align="center">Matchup</b></p></td>
	<td colspan=3><p class="Header" align="center">Picks</p></td>
	<td><p class="Header" align="center">Points</p></td>
    </tr>
    <tr>
	<td colspan=2></td>
	<td><p align="center" class="subheader">Team</p></td>
	<td><p align="center" class="subheader">Games</p></td>
	<td><p align="center" class="subheader">Goal Diff</p></td>
	<td></td>
    </tr>
<?php
	require_once "../matchups.php";
	$matchups = $db->get_matchups($round, $year);
	while ($row = mysql_fetch_array($matchups)) {
		$matchup = new Matchup($row);
		$row = mysql_fetch_array($db->get_pick($matchup->matchid, $_SESSION['userid']));

		$tgameCount = 0;
		$bgameCount = 0;
		$tgoalCount = 0;
		$bgoalCount = 0;	

		$pick = $row['pick'];
		$pgames = $row['games'];
		$goals = $row['goal_diff'];

		$games = $db->get_games($matchup->matchid);
		$twinner_class = "table-entry";
		$bwinner_class = "table-entry";
		while ($game = mysql_fetch_array($games)) {
			if ($game['top_seed_score'] > $game['bottom_seed_score']) $tgameCount += 1;
			else $bgameCount += 1;
			$tgoalCount += $game['top_seed_score'];
			$bgoalCount += $game['bottom_seed_score'];
			$winner = "";
			if ($tgameCount == 4) {
				$twinner_class= "winner";
				$winner = $matchup->top_seed_team;
				$goal_diff = $tgoalCount - $bgoalCount;
				$seed_diff = 0;
			}
			elseif ($bgameCount == 4) {
				$bwinner_class = "winner";
				$winner = $matchup->bottom_seed_team;
				$goal_diff = $bgoalCount - $tgoalCount;
				$seed_diff = $matchup->bottom_seed_rank - $matchup->top_seed_rank;
			}
		}
		$totalGamesCount = $tgameCount + $bgameCount;
		if ($pick == $winner) $winner_pick = True;
		else $winner_pick = False;
		if ($pgames == $totalGamesCount) $games_pick = True;
		else $games_pick = False;
		if ($goal_diff == $goals) $goal_diff_pick = True;
		else $goal_diff_pick = False;
		$points = calculate_points($winner_pick, $seed_diff, $totalGamesCount, $games_pick, $goal_diff_pick);
		$db->update_points($_SESSION['userid'], $matchup->matchid, $points);
		echo "
    <tr>
	<td><p class=\"table-entry\"><br></p>
	    <p class=\"" . $twinner_class . "\">" . $matchup->top_seed_rank . " " . $matchup->top_seed_team . "</p>
	    <p class=\"". $bwinner_class . "\">" . $matchup->bottom_seed_rank . " " . $matchup->bottom_seed_team . "</p></td>
	<td>
	<table border=1 cellspacing=0 cellpadding=0 solid black>";

	$games = $db->get_games($matchup->matchid);
	echo "<tr>";
	while ($game = mysql_fetch_array($games)) {

	echo "
		    <td class=\"games\"><p align=\"center\" class=\"table-entry\"><b>" . $game['game_number'] . "</b></p></td>";
	}

	echo "</tr>";
	$games = $db->get_games($matchup->matchid);
	echo "<tr>";
	while ($game = mysql_fetch_array($games)) {
	echo "
                    <td class=\"games\"><p align=\"center\" class=\"table-entry\">" . $game['top_seed_score'] . "</p></td>
               ";
	}
	echo "</tr>";
	$games = $db->get_games($matchup->matchid);
	echo "<tr>";
	while ($game = mysql_fetch_array($games)) {
	echo "
                    <td class=\"games\"><p align=\"center\" class=\"table-entry\">" . $game['bottom_seed_score'] . "</p></td>";
	}	
	
	echo "
	    </table>
	</td>
	<td><p align=\"center\">" . $pick . "</p></td>
	<td><p align=\"center\">" . $pgames . "</p></td>
	<td><p align=\"center\">" . $goals . "</p></td>
	<td><p align=\"center\">" . $points . "</p></td>
    </tr>";
	}
?>
<tr>
    <td colspan=5></td><td><p align="center"> <?php echo $db->get_total_points($_SESSION['userid'], $year); ?>
    </td>
</tr>
</table>
</body>
</html>
