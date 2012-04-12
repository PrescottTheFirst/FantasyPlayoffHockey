<?php
        session_start();
?>
<html>
    <?php
        require_once "../data_access.php";
	require_once "../updatePoints.php";
	
	updatePoints();
        $start_of_round[1] = strtotime("04/11/2012 19:00:00");
        $start_of_round[2] = strtotime("04/15/2012 19:00:00");
        $start_of_round[3] = strtotime("04/15/2012 19:00:00");
        $start_of_round[4] = strtotime("04/15/2012 19:00:00");
        $date = time();

        $db = new DataAccess();
        $row = $db->get_current_round_and_year();
        if ($_GET['round']) $round = $_GET['round'];
	else $round = $row['round'];
        $year = $row['year'];

        if(!isset($_SESSION['loggedin']))
        {
        echo " <script type=\"text/javascript\">
            <!--
                window.location = \"login.php\"
            //-->
            </script> ";
        }
	if (!($_GET['user'])) {
		$user = ($_SESSION['name']);
		$userid = ($_SESSION['userid']);
	} else {
		$userid = ($_GET['user']);
		$user = ($_GET['username']);
	}
    ?>
<head>
<LINK href="default.css" rel="stylesheet" type="text/css">
<title>Fantasy Playoff Hockey</title>
<h1 align="center">Fantasy Playoff Hockey</h1>
</head>
<body>
<h2 align="center">Results for round <?php echo $round ?></h2>
<?php echo "<h2 align=\"center\">Round : 
	<a href=\"results?user=" . $userid . "&username=" . $user . "&round=1\">1</a>
	<a href=\"results?user=" . $userid . "&username=" . $user . "&round=2\">2</a>
	<a href=\"results?user=" . $userid . "&username=" . $user . "&round=3\">3</a>
	<a href=\"results?user=" . $userid . "&username=" . $user . "&round=4\">4</a>
	</h2>";
?>
<h2 align="center"><a class="topbar" href="home">Standings</a>
<a class="topbar" href="picks">Make Picks</a>
<a class="topbar" href="results">Results</a>
<a class="topbar" href="logout">Log Out</a></h2>
<p>User: <?php print_r($user); ?><br></p>
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
	while ($row = @mysql_fetch_array($matchups)) {
		$matchup = new Matchup($row);
		$row = @mysql_fetch_array($db->get_pick($matchup->matchid, $userid));

		$tgameCount = 0;
		$bgameCount = 0;

		if ($date > $start_of_round[$round] || $_SESSION['userid'] == $userid) {
		$pick = $row['pick'];
		$pgames = $row['games'];
		$goals = $row['goal_diff'];
		$points = $row['points'];
		} else $pick = "Hidden";

		$games = $db->get_games($matchup->matchid);
		$twinner_class = "table-entry";
		$bwinner_class = "table-entry";
		while ($game = @mysql_fetch_array($games)) {
			if ($game['top_seed_score'] > $game['bottom_seed_score']) $tgameCount += 1;
			else $bgameCount += 1;
			$tgoalCount += $game['top_seed_score'];
			$bgoalCount += $game['bottom_seed_score'];
			$winner = "";
			if ($tgameCount == 4) {
				$twinner_class= "winner";
			}
			elseif ($bgameCount == 4) {
				$bwinner_class = "winner";
			}
		}
		echo "
    <tr>
	<td><p class=\"table-entry\"><br></p>
	    <p class=\"" . $twinner_class . "\">" . $matchup->top_seed_rank . " " . $matchup->top_seed_team . "</p>
	    <p class=\"". $bwinner_class . "\">" . $matchup->bottom_seed_rank . " " . $matchup->bottom_seed_team . "</p></td>
	<td>
	<table border=1 cellspacing=0 cellpadding=0 solid black>";

	$games = $db->get_games($matchup->matchid);
	echo "<tr>";
	while ($game = @mysql_fetch_array($games)) {

	echo "
		    <td class=\"games\"><p align=\"center\" class=\"table-entry\"><b>" . $game['game_number'] . "</b></p></td>";
	}

	echo "</tr>";
	$games = $db->get_games($matchup->matchid);
	echo "<tr>";
	while ($game = @mysql_fetch_array($games)) {
	echo "
                    <td class=\"games\"><p align=\"center\" class=\"table-entry\">" . $game['top_seed_score'] . "</p></td>
               ";
	}
	echo "</tr>";
	$games = $db->get_games($matchup->matchid);
	echo "<tr>";
	while ($game = @mysql_fetch_array($games)) {
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
    <td colspan=5></td><td><p align="center"> <?php echo $db->get_total_points($userid, $year); ?>
    </td>
</tr>
</table>
</body>
</html>
