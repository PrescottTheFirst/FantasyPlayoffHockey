<?php
        session_start();
?>
<html>
  <HEAD>
    <title>Fantasy Playoff Hockey</title>
    <LINK href="default.css" rel="stylesheet" type="text/css">
  </HEAD>
<body>
<?php
if(isset($_SESSION['loggedin']))
{
    echo " <script type=\"text/javascript\">
        <!--
            window.location = \"home.php\"
        //-->
        </script> ";
}
?>
    <form id = "signup-form" action = "signup" method = "POST">
	<h1 align="center">Fantasy Playoff Hockey</h1>
	<table align="center"><tbody>
	   <tr>
		<td colspan=2 align="center">
                   <p class="Header">Sign up</p>
		</td>
	   </tr>		
	   <tr>
		<td><p>Username</p></td>
		<td><input type="text" name="username" value="<?php echo htmlspecialchars($_POST['username']); ?>" maxlength="30"/></td>
	   </tr>
	   <tr>
		<td><p>Name</p></td>
		<td><input type="text" name="realname" value="<?php echo htmlspecialchars($_POST['realname']); ?>" maxlength="30"/></td>
	   </tr>
	   <tr>
		<td><p>Password</p></td>
		<td><input type="password" name="password" maxlength="30"/></td>
	   </tr>
	   <tr>
		<td><p>Re-enter Password</p></td>
		<td><input type="password" name="repassword" maxlength="30"/></td>
	   </tr>
	   <tr>
		<td colspan=2 align="center">
		<input type="submit" value="Submit"/>
		</td>
	   </tr>
	   <tr>
		<td colspan=2 align="center">
		  <p class="Error"> 
		  <?php
			include_once "../account.php";

			if (count($_POST) > 0) {
				signup($_POST['username'], $_POST['realname'], $_POST['password'], $_POST['repassword']);
			}
		  ?>
		  </p>
		</td>
	    </tr>
	</tbody></table>
    </form>
</body>
<?php
	include_once "../account.php";

	function validate($username, $realname, $password, $repassword) {
		if (strlen($username) == 0 ) return "Please enter a username";
		if (strlen($realname) == 0 ) return "Please enter a Name";
		if ($password != $repassword) return "Passwords do not match";
		if (strlen($password) < 6) return "Password must be at least 6 characters";
		return "OK";
	}

	function signup($username, $realname, $password, $repassword) {
		$ok = validate($username, $realname, $password, $repassword);
		if ($ok === "OK") {
			$account = new Account(mysql_escape_string($username), mysql_escape_string($realname), mysql_escape_string($password));
			$account->make_account();
			$ok = $account->string;
			echo $OK;
			if ($ok === "OK") {
		        echo " <script type=\"text/javascript\">
            <!--
                window.location = \"login.php\"
            //-->
            </script> ";	
			}
			else echo $ok;
		} else {
			echo $ok;
		}
	}
?>
</html>
