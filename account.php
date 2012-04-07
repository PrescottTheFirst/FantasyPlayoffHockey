<?php
	include_once "data_access.php";	

	class Account
	{
		private $username;
		private $realname;
		private $password;
		private $db;
		public $string;
		
		function __construct($username, $realname, $password) {
		// This will be rewritten with the front end
			$this->db = new DataAccess();
			$this->username = $username;
			$this->realname = $realname;
			$this->password = $password;
		}

		function make_account() {
			$results = $this->db->get_user($this->username);
			$row = mysql_fetch_array($results);
			if ($row == NULL) {
				$this->db->insert_user($this->username, $this->realname, $this->password);
				$this->string =  "OK";
			} else {
				$this->string =  "ERROR: Username " . $this->username . " already taken\n";
			}			
		}
		
		function do_login() {
			$results = $this->db->get_user($this->username);
			$row = mysql_fetch_array($results);
			if ($this->password == $row['password']) {
				$this->string = "Login Successful";
				$_SESSION['loggedin'] = "YES"; // Set it so the user is logged in!
				$_SESSION['name'] = $this->username; // Make it so the username can be called by $_SESSION['name']
				$results = $this->db->get_user($this->username);
				$row = mysql_fetch_array($results);
				$_SESSION['userid'] = $row['userid'];
				return True;
			}
			else {
				$this->string = "Login unsuccessful";
				return False;
			}
		}
	}
	
?>
