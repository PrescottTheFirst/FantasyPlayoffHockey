<?php
	class DataAccess
	{
		private $con;
		
		function __construct(){
			$this->con = mysql_connect("localhost", "root", "virtual");
			mysql_select_db("FantasyPlayoffHockey"); 
		}

		function __destruct() {
			mysql_close($this->con);
		}

		function get_matchups($round, $year) {
			$query = "SELECT matchid, top_seed_rank, top_seed_team, bottom_seed_rank, bottom_seed_team FROM MATCHUPS ";
			$query .= " WHERE round = " . $round;
			$query .= " AND year = '" . $year . "'";
			$query .= " ORDER BY conference, top_seed_rank";
			return self::exec_query($query);
		}

		function get_pick($matchid, $userid) {
			$query = "SELECT pick, games, goal_diff, points FROM PICKS ";
			$query .= " WHERE matchid = " . $matchid;
			$query .= " AND userid = " . $userid;
			return self::exec_query($query);
		}

		function insert_pick($userid, $matchid, $pick, $games, $goal_diff) {
			$query = "DELETE FROM PICKS WHERE userid = " . $userid . " AND matchid = " . $matchid;
			self::exec_query($query);
			$query = " INSERT INTO PICKS (userid, matchid, pick, games, goal_diff, points) ";
			$query .= "values (" . $userid . "," . $matchid . ",'" . $pick . "'," . $games . "," . $goal_diff . ", 0)";
			self::exec_query($query);
		}

		function update_points($userid, $matchid, $points) {
			$query = "UPDATE PICKS SET points = " . $points;
			$query .= " WHERE userid = " . $userid;
			$query .= " AND matchid = " . $matchid;
			self::exec_query($query);
		}

		function get_total_points($userid, $year) {
			$query = "SELECT sum(Points) as P FROM MATCHUPS, PICKS";
			$query .= " WHERE MATCHUPS.matchid = PICKS.matchid";
			$query .= " AND USERID = " . $userid;
			$query .= " AND YEAR = '" . $year . "'";

			$results = self::exec_query($query);
			$row = mysql_fetch_array($results);
			if ($row['P'] === NULL) return 0;		
			else return $row['P'];		
		}

		function get_results($matchid) {
			$query = "SELECT winner, games, goal_diff FROM RESULTS";
			$query .= " WHERE matchid = " . $matchid;
			return self::exec_query($query);
		}

		function get_current_round_and_year() {
			$query = "SELECT round, year FROM MATCHUPS ORDER BY ROUND DESC LIMIT 1";
			$results = self::exec_query($query);
			$row = mysql_fetch_array($results);
			if ($row === NULL) return NULL;			
			else return $row;
		}

		function get_games($matchid) {
			$query = "SELECT game_number, top_seed_score, bottom_seed_score FROM GAMES ";
			$query .= "WHERE matchid = " . $matchid;
			return self::exec_query($query);
		}

		function get_user($username) {
			$query = "SELECT userid, realname, password FROM USERS";
			$query .= " WHERE username = '" . $username . "'";
			return self::exec_query($query);
		}

		function get_users() {
			$query = "SELECT * FROM USERS";
			return self::exec_query($query);
		}

		function insert_user($username, $realname, $password) {
			$query = "INSERT INTO USERS (username, realname, password) ";
			$query .= "VALUES ('" . $username . "','" . $realname . "','" . $password . "')";
			self::exec_query($query);
		}

		function exec_query($query) {
			return mysql_query($query);
		}
	}
	
	//$db = new DataAccess();	

	//$row = $db->get_current_round_and_year();
		//echo $row['round'] . "\n";	

?>
