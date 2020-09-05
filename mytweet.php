<?php
	  
	//start session
	require_once('startsession.php');
	
	//Insert header
	$page_title='My Tweets';
	require_once('header.php');

	//Insert navigation menu
	require_once('navmenu.php');
?>

	<!--display tweets here-->
<?php

	//check if logged in
	if (!isset($_SESSION['u_id'])){
		echo '<p>Please login to display your tweets</p>';
	}else{

		//dispaly any necessary mesage
		if (!empty($_GET['msg'])) {
			echo '<p class="message">' . $_GET['msg'] . '</p>';	
		}

		//connect to database
		require_once('connectvars.php');
		$dbc=mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to MySQL Database');

		//get all tweets under this user
		$u_id=$_SESSION['u_id'];
		$query="SELECT tweets.t_name, tweets_of_user_$u_id.t_update_time" .
			   " FROM tweets INNER JOIN tweets_of_user_$u_id" . 
			   " ON tweets.t_id=tweets_of_user_$u_id.t_id" .
			   " ORDER BY tweets_of_user_$u_id.t_update_time DESC";
		$data=mysqli_query($dbc, $query) or die('Error retrieving user\'s tweets');
		echo '<table>';
		echo '<tr><th>Tweet</th><th>|</th><th>Last Change</th></tr>';
		while($row = mysqli_fetch_array($data)){
			echo '<tr>';
			echo '<td>';
			echo '(<a href="tweet.php?t_name=' . $row['t_name'] . '">tweet</a>)';
			echo $row['t_name'];
			echo '</td>';
			echo '<td>|</td>';
			echo '<td>';
			echo $row['t_update_time'];
			echo '</td>';
			echo '</tr>';
		}	
		echo '</table>';
	}
?>

<?php

	//insert footer
	require_once('footer.php');
?>
