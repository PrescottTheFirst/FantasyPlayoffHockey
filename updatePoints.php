<?php
	require_once "data_access.php";
	require_once "matchups.php";

        function calculate_points($winner_pick, $seed_diff, $games, $games_pick, $round) {
                $points = 0;
                if (!$winner_pick) return $points;
		if ($round < 4) $points += 4;  // 4 points for correct winner
		else $points += 8;
                if (($seed_diff > 0) && ($round < 4)) {
                        $points += ceil($seed_diff / 2);
                }
                if ($games_pick) {
                        if ($games == 4) $points += 4;
                        else $points += 2;
                }
                return $points;
        }

	function update_tie_breaker($db, $userid, $matchid, $top_seed_team) {
		$row = @mysql_fetch_array($db->get_pick($matchid, $userid));
		$goal_diff_pick = $row['goal_diff'];
		$pick = $row['pick'];
		if ($pick === $top_seed_team) $picked_top_seed = true;
		$goal_diff_act = $db->get_goal_diff($matchid);
		if (!$picked_top_seed) {
			$goal_diff_pick = 0 - $goal_diff_pick;
		}
		$db->update_tie_breaker($userid, $matchid, abs($goal_diff_pick - $goal_diff_act));
	}
	
	function updatePoints() {
		$db = new DataAccess();
		$row = $db->get_current_round_and_year();
		$round = $row['round'];
		$year = $row['year'];

		$matchups = $db->get_matchups($round, $year);
		while ($row = @mysql_fetch_array($matchups)) {
			$matchup = new Matchup($row);
			$users = $db->get_users();
			while($user = @mysql_fetch_array($users)) {
				$userid = $user['userid'];
				update_tie_breaker($db, $userid, $matchup->matchid, $matchup->top_seed_team);
				$row = @mysql_fetch_array($db->get_pick($matchup->matchid, $userid));
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
				while ($game = @mysql_fetch_array($games)) {
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
				$points = calculate_points($winner_pick, $seed_diff, $totalGamesCount, $games_pick, $matchup->round);
				$db->update_points($userid, $matchup->matchid, $points);
			}
		}
	}
?>
