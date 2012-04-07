<html>
  <HEAD>
    <title>Fantasy Playoff Hockey</title>
    <LINK href="default.css" rel="stylesheet" type="text/css">
  </HEAD>
<body>
  <h1 align="center">Fantasy Playoff Hockey</h1>
     <form id='login' action='login' method='POST'>
        <table class="main" align="center"><tbody>
           <tr>
                <td colspan=2 align="center">
                   <p class="Header">Login</p>
                </td>
           </tr>
           <tr>
                <td><p>Username</p></td>
                <td><input type="text" name="username" value="<?php echo htmlspecialchars($_POST['username']); ?>" maxlength="30"/></td>
           </tr>
           <tr>
                <td><p>Password</p></td>
                <td><input type="password" name="password" maxlength="30"/></td>
           </tr>
	   <tr><td colspan=2 align="center"><input type="submit" name="submit" value="Login"/></td></tr>
	   <tr>
		<td colspan=2 align="center">
		  <p class=Error>		
<?php
require_once "../account.php";

session_start();
if(isset($_SESSION['loggedin']))
{
echo " <script type=\"text/javascript\">
<!--
    window.location = \"home.php\"
//-->
</script> ";
}

if(isset($_POST['submit']))
{
   $db = new DataAccess();
   $name = mysql_real_escape_string($_POST['username']);
   $pass = mysql_real_escape_string($_POST['password']);

   if (strlen($pass) == 0) $pass = "NULL";

   $account = new Account($name, '', $pass);
   $account->do_login();
   echo $account->string;
   if ($account->string === "Login Successful") {
   echo " <script type=\"text/javascript\">
	<!--
           window.location = \"home.php\"
	//-->
	</script> ";
   }
}
?>
</form>
</p>
</table>
</body>
