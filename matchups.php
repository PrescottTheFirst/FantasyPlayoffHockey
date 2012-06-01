<?php
	include_once "data_access.php";	

	$username = "CactusFantastico";
	$realname = "Rob";
	$userid = 1;
	//$round = NULL;
	//$year = 2012;

	class Matchup
	{
		public $matchid;
		public $top_seed_rank;
		public $top_seed_team;
		public $bottom_seed_rank;
		public $bottom_seed_team;
		public $pick;
		public $result;
		public $round;
		public $games;

		function __construct($row) {
			global $year, $round;
			$this->matchid = $row['matchid'];
			$this->top_seed_rank = $row['top_seed_rank'];
			$this->top_seed_team = $row['top_seed_team'];
			$this->bottom_seed_rank = $row['bottom_seed_rank'];
			$this->bottom_seed_team = $row['bottom_seed_team'];
			$this->round = $row['round'];
		} 

	}

	//function get_round($db) {
	//	global $round, $year;
	//	if ($round != NULL)  return $round;
	//	$row = $db->get_current_round_and_year();
	//	$row = mysql_fetch_array($results);
	//	return $row['round'] . "\n";
	//}

	function get_matchups($db, $round) {
		global $year;
		$results = $db->get_matchups($round, $year);
		$matchups = array();
		while ($row = @mysql_fetch_array($results)) {
        		array_push($matchups, $row);
        	}
		return $matchups;	
	}

	function get_pick($db, $matchid, $userid) {
		$results = $db->get_picks($matchid, $userid);
		return @mysql_fetch_array($results);		
	}
	
	function get_result($db, $matchid) {
		$results = $db->get_results($matchid);
		return @mysql_fetch_array($results);	
	}

	function get_games($db, $matchid) {
		$results = $db->get_games($matchid);
		$games = array();
		while ($row = @mysql_fetch_array($results)) {
			array_push($games, $row);
		}
		return $games;
	}

	//$db = new DataAccess();
	//$round = get_round($db);
	//$matchups_raw = get_matchups($db, $round);
	//$matchups = array();

	//foreach ($matchups_raw as $row) {
	//	$match = new Matchup($row);
	//	$match->pick = get_pick($db, $match->matchid, $userid);
	//	$match->result = get_result($db, $match->matchid);
	//	$match->games = get_result($db, $match->matchid); 
	//	array_push($matchups, $match);
	//	print_r($match);
	//}
	
?>
