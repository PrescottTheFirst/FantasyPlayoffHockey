<?php
        session_start();
?>
<html>
  <HEAD>
    <title>Fantasy Playoff Hockey</title>
    <LINK href="default.css" rel="stylesheet" type="text/css">
  </HEAD>
<h1 align="center">Fantasy Playoff Hockey</h1>
<h2 align="center"><a class="topbar" href="">Standings</a>
<a class="topbar" href="picks">Make Picks</a>
<a class="topbar" href="results">Results</a>
<a class="topbar" href="logout">Log Out</a></h2>
</head>
<body>
<table align="center">
<tr><td colspan=2>
<table align="center" border=1>
    <tr>
	<td><p align="center">Rank</p></td>
	<td><p align="center">Username</p></td>
	<td><p align="center">Real Name</p></td>
	<td><p align="center">Picks</p></td>
	<td><p align="center">Points</p></td>
	<td><p align="center">Tie Breaker</p></td>
	<td><p align="center">For Money</p></td>
    </tr>
<?php
	include_once "../data_access.php";
	require_once "../updatePoints.php";	
        if(!isset($_SESSION['loggedin']))
        {
        echo " <script type=\"text/javascript\">
            <!--
                window.location = \"login.php\"
            //-->
            </script> ";
        }

	updatePoints();

	$db = new DataAccess();
	$users = $db->get_users();
	$row = $db->get_current_round_and_year();
	$year = $row['year'];
	$count = 0;
	while ($user = @mysql_fetch_array($users)) {
		$points = $db->get_total_points	($user['userid'], $year, 0);
		$tiebreaker=  $db->get_total_tie_breaker($user['userid'], $year);
		$count += 1;
    	echo "
    <tr>
	<td><p align=\"center\">" . $count . "</p></td>
	<td><p align=\"center\">" . $user['username'] . "</p></td>
	<td><p align=\"center\">" . $user['realname'] . "</p></td>
	<td><a align=\"center\" href=\"results?user=" . $user['userid'] . "&username=" . $user['username'] . "\">Picks</a></td>
	<td><p align=\"center\">" . $points . "</p></td>
	<td><p align=\"center\">" . $tiebreaker . "</p></td>
	<td><p align=\"center\">" . $user['ten_bucks'] . "</p></td>
    </tr>";
	}
?>
</table>
</td>
<td colspan=1>
<table border=1>
<tr>
<td>
<form method="post" action="home">
<p>Rage Here</p>
<textarea name="comments" cols="40" rows="5">
</textarea><br>
<input type="submit" value="Submit" />
</form>
</td>
</tr>
<?php
	$posts = $db->get_discussion_posts();
	while ($post = mysql_fetch_array($posts)) {
		echo "<tr><td><p align=\"center\"> " . $post['username'] . " -  " . $post['realname'] . "</p>";
		echo "<p>" . $post['post'] . "</p></td><tr>";
	}

	if((isset($_POST['comments'])) &&(strlen($_POST['comments']) > 0)) {
		$db->add_discussion_post($_SESSION['userid'], mysql_real_escape_string($_POST['comments']));
        echo " <script type=\"text/javascript\">
            <!--
                window.location = \"home.php\"
            //-->
            </script> ";

	}
?>
</table>
</td>
</tr></table>
<p align="center">Note: The lower the tiebreaker the better, but it doesn't mean anything until a round is complete</p>
</body>
</html>
