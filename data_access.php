<?php
	class DataAccess
	{
		private $con;
		
		function __construct(){
			$this->con = mysql_connect("localhost", "root", "virtual");
			mysql_select_db("FantasyPlayoffHockey"); 
			#$this->con = mysql_connect("db411188437.db.1and1.com", "dbo411188437", "virtual");
			#mysql_select_db("db411188437"); 
		}

		function __destruct() {
			@mysql_close($this->con);
		}

		function get_discussion_posts() {
			$query = "SELECT username, realname, post FROM USERS, DISCUSSION WHERE DISCUSSION.userid = USERS.userid ORDER BY POSTID DESC LIMIT 15;";
		return self::exec_query($query);		
		}

		function add_discussion_post($userid, $post) {
			$query = "INSERT INTO DISCUSSION (userid, post) values ('" . $userid . "','" . $post . "')";
			self::exec_query($query);	
		}

		function get_matchups($round, $year) {
			$query = "SELECT matchid, top_seed_rank, top_seed_team, bottom_seed_rank, bottom_seed_team, round FROM MATCHUPS ";
			$query .= " WHERE round = " . $round;
			$query .= " AND year = '" . $year . "'";
			$query .= " ORDER BY conference, top_seed_rank";
			return self::exec_query($query);
		}

		function get_match($matchid) {
			$query = "SELECT * FROM MATCHUPS WHERE matchid = " . $matchid;
			return self::exec_query($query);
		}

		function get_pick($matchid, $userid) {
			$query = "SELECT pick, games, goal_diff, points FROM PICKS ";
			$query .= " WHERE matchid = " . $matchid;
			$query .= " AND userid = " . $userid;
			return self::exec_query($query);
		}

		function get_game_num($matchid) {
			$query = " SELECT game_number FROM GAMES WHERE matchid = " . $matchid . " ORDER BY game_number Desc LIMIT 1";
			$results = self::exec_query($query);
			$row = mysql_fetch_array($results);
			if ($row) return $row['game_number'];
			else return 0;
		}

		function add_game($matchid, $game_num, $top_seed_score, $bottom_seed_score) {
			$query = "INSERT INTO GAMES (date, matchid, game_number, top_seed_score, bottom_seed_score) VALUES (" . time() . "," . $matchid . "," . $game_num . "," . $top_seed_score . "," . $bottom_seed_score . ")";
			self::exec_query($query);
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

		function update_tie_breaker($userid, $matchid, $tie_breaker) {
			$query = "UPDATE PICKS SET tie_breaker = " . $tie_breaker;
			$query .= " WHERE userid = " . $userid;
			$query .= " AND matchid = " . $matchid;
			self::exec_query($query);
		}

		function get_total_points($userid, $year, $round) {
			$query = "SELECT sum(Points) as P FROM MATCHUPS, PICKS";
			$query .= " WHERE MATCHUPS.matchid = PICKS.matchid";
			$query .= " AND USERID = " . $userid;
			$query .= " AND YEAR = '" . $year . "'";
			if ($round > 0) $query .= " AND ROUND = " . $round;

			$results = self::exec_query($query);
			$row = @mysql_fetch_array($results);
			if ($row['P'] === NULL) return 0;		
			else return $row['P'];		
		}

		function get_total_tie_breaker($userid, $year) {
			$query = "SELECT sum(tie_breaker) as T FROM MATCHUPS, PICKS";
			$query .= " WHERE MATCHUPS.matchid = PICKS.matchid";
			$query .= " AND USERID = " . $userid;
			$query .= " AND YEAR = '" . $year . "'";

			$results = self::exec_query($query);
			$row = @mysql_fetch_array($results);
			if ($row['T'] === NULL) return 0;		
			else return $row['T'];		
		}
	
		function get_goal_diff($matchid) {
			$query = "SELECT sum(top_seed_score - bottom_seed_score) as diff,  top_seed_team, bottom_seed_team, GAMES.* FROM GAMES, MATCHUPS WHERE GAMES.matchid = MATCHUPS.matchid AND MATCHUPS.matchid = " . $matchid;
			$results = self::exec_query($query);
			$row = @mysql_fetch_array($results);
			return $row['diff'];
		}

		function get_results($matchid) {
			$query = "SELECT winner, games, goal_diff FROM RESULTS";
			$query .= " WHERE matchid = " . $matchid;
			return self::exec_query($query);
		}

		function get_current_round_and_year() {
			$query = "SELECT round, year FROM MATCHUPS ORDER BY ROUND DESC LIMIT 1";
			$results = self::exec_query($query);
			$row = @mysql_fetch_array($results);
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
			$query = "SELECT USERS.*, sum(POINTS) as P, sum(TIE_BREAKER) as T FROM USERS, PICKS WHERE USERS.userid = PICKS.userid GROUP BY USERS.userid ORDER BY P DESC, T ASC";
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
