<?php
        session_start();
?>
<html>
<head>
    <?php
	require_once "../data_access.php";
	date_default_timezone_set('America/Toronto');

	$db = new DataAccess();
	$row = $db->get_current_round_and_year();
	$round = $row['round'];
	if ($round > 4) $round = 4;
	$year = $row['year'];
	if(!isset($_SESSION['loggedin']) || ($_SESSION['name'] != 'Rob2'))
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
    <form id="picks-form" action="games.php" method="POST">
	<h1 align="center">Fantasy Playoff Hockey</h1>
<h2 align="center"><a class="topbar" href="home">Standings</a>
<a class="topbar" href="picks">Make Picks</a>
<a class="topbar" href="results">Results</a>
<a class="topbar" href="logout">Log Out</a></h2>
	<table class="main" align="center">
	    <tr><td colspan=8 align="center">
	        <p class="Header" align="center">Add a Game for round  <?php echo $round ?>
		 </p>
	    <tr>
		<td colspan=3 align=center><p>Top Seed</p></td>
		<td colspan=3 align=center><p>Bottom Seed</p></td>
	    </tr>
<?php
	require_once "../matchups.php";
	$count = 0;
	$matchups = $db->get_matchups($round, $year);
	while ($row = @mysql_fetch_array($matchups)) {
		$count += 1;
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
		    <Input type=\"text\" ". $topPick ." align=\"right\" name=\"top_" . $matchup->matchid . "\"/></p>
		</td>
		<td colspan=2>
		    <p class=\"table-entry\">" . $matchup->bottom_seed_rank . " " . $matchup->bottom_seed_team . "
		</td>
		<td colspan=1>
		    <input type=\"text\" " . $bottomPick . " align=\"right\" name=\"bottom_" . $matchup->matchid . "\"/></p>
		</td>
	    </tr>";
	}
	if ($count > 0) {
		echo "<tr><td colspan=8 align=\"center\"><input type=\"submit\" value=\"Submit\"></td></tr>";
	} else echo "<tr><td colspan=8 align=\"center\"><p class=\"Error\">Matchups for round " . $round . " are not yet determined.</p></td></tr>";
?>
	</table>
    </form>

<?php
	$matchups = $db->get_matchups($round, $year);
	$count = 1;
        if (count($_POST) > 0) {
		foreach ($_POST as $entry){
			if ($count % 2) {
				$match = mysql_fetch_array($matchups);
				$top_seed_score = $entry;
			} else {
				$bottom_seed_score = $entry;
			}
			$matchid = $match['matchid'];
			$gameNum = $db->get_game_num($matchid) + 1;
		
			if ((($count % 2) == 0) && is_numeric($top_seed_score) && is_numeric($bottom_seed_score)) {
				$db->add_game($matchid, $gameNum, $top_seed_score, $bottom_seed_score); 
			}
			$count += 1;
		}
	}
?>
</body>
</html>
