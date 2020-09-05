<?php
	  
	//start session
	require_once('startsession.php');
	
	//Insert header
	$page_title='Home';
	require_once('header.php');

	//Insert navigation menu
	require_once('navmenu.php');
?>

	<!--display tweets here-->
<?php

	//check if logged in
	if (isset($_SESSION['u_id'])){
		echo '<p>Welcome ' . $_SESSION['u_name'] . '.</p>';
		require_once('connectvars.php');
		$dbc=mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to MySQL Database');

		//display 10 latest updates
		$query="SELECT t_id, t_name, t_update_time FROM tweets ORDER BY t_update_time DESC LIMIT 10"; 
		$data=mysqli_query($dbc, $query) or die ('Error fetching tweets');
		echo '<p>Latest Updates</p>';
		echo '<table>';
		echo '<tr><th>Tweet</th><th>|</th><th>Last Change</th></tr>';
		while($row = mysqli_fetch_array($data)){
			echo '<tr>';
			echo '<td>';
			echo '(<a href="tweet.php?t_name=' . $row['t_name'] . '">tweet</a>';
			echo '|';
			echo '<a href="explore.php?type=t_name&name=' . $row['t_name'] . '&submit=Explore">explore</a>) ';
			echo $row['t_name'];
			echo '</td>';
			echo '<td>|</td>';
			echo '<td>';
			echo $row['t_update_time'];
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
	}else{
		echo '<p>Welcome to Twitter Clone</p>';
	}
?>

<?php

	//insert footer
	require_once('footer.php');
?>
