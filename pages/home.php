<html>
  <HEAD>
    <title>Fantasy Playoff Hockey</title>
    <LINK href="default.css" rel="stylesheet" type="text/css">
  </HEAD>
<h1 align="center">Fantasy Playoff Hockey</h1>
<h2 align="center"><a href="picks">Make Picks</a>
<a href="results">Results</a></h2>
</head>
<body>
<table align="center" border=1>
    <tr>
	<td><p align="center">Rank</p></td>
	<td><p align="center">Username</p></td>
	<td><p align="center">Real Name</p></td>
	<td><p align="center">Picks</p></td>
	<td><p align="center">Points</p></td>
    </tr>
<?php
	include_once "../data_access.php";	
        session_start();
        if(!isset($_SESSION['loggedin']))
        {
        echo " <script type=\"text/javascript\">
            <!--
                window.location = \"login.php\"
            //-->
            </script> ";
        }

	$db = new DataAccess();
	$users = $db->get_users();
	$row = $db->get_current_round_and_year();
	$year = $row['year'];
	while ($user = mysql_fetch_array($users)) {
		$points = $db->get_total_points	($user['userid'], $year);
    	echo "
    <tr>
	<td><p align=\"center\">1</p></td>
	<td><p align=\"center\">" . $user['username'] . "</p></td>
	<td><p align=\"center\">" . $user['realname'] . "</p></td>
	<td><a align=\"center\" href=\"results\">Picks</a></td>
	<td><p align=\"center\">" . $points . "</p></td>
    </tr>";
	}
?>
</table>
</body>
</html>
