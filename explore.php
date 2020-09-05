<?php
	  
	//start session
	require_once('startsession.php');
	
	//Insert header
	$page_title='Explore';
	require_once('header.php');

	//Insert navigation menu
	require_once('navmenu.php');
?>


<?php
	//prepare error_msg var
	$error_msg="";
	//if not logged in
	if (!isset($_SESSION['u_id'])){
		$error_msg= 'Sorry, please login to start exploring.';
	}

	//if logged in and already posted
	else if (isset($_GET['submit'])){
		//connect to database
		require_once('connectvars.php');
		$dbc=mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die ('Error connecting to MySQL DB');
		
		//parse info
		$type=$_GET['type'];
		//explore user
		if (($type == 'u_name') && ($_GET['name'] != '')){
			$u_name=mysqli_real_escape_string($dbc, trim($_GET['name']));

			//get u_id of target user name
			$query="SELECT u_id FROM users WHERE u_name='$u_name'";
			$data=mysqli_query($dbc, $query);
			$checkuser=mysqli_num_rows($data);
			if ($checkuser == 0) {
				$error_msg="User doesn't exist.";
			}else{
				$row=mysqli_fetch_array($data);
				$u_id=$row['u_id'];
	
				$query= "SELECT tweets.t_name, tweets_of_user_$u_id.t_update_time" .
						" FROM tweets INNER JOIN tweets_of_user_$u_id" . 
						" ON tweets_of_user_$u_id.t_id = tweets.t_id" .
						" ORDER BY tweets_of_user_$u_id.t_update_time DESC";
				$data=mysqli_query($dbc, $query) or die ('Error retrieving user\'s tweets');
				echo '<p> Tweets of ' . $u_name ;
				echo '</p>';
				echo '<table>';
				echo '<tr><th>Tweet</th><th>Last Change</th>';
				while($row = mysqli_fetch_array($data)){
					echo '<tr>';
					echo '<td>';
					echo '(<a href="explore.php?type=t_name&name=' . $row['t_name'] . '&submit=Explore">explore</a>';
					echo '|';
					echo '<a href="tweet.php?t_name=' . $row['t_name'] . '">tweet</a>)';
					echo $row['t_name'];
					echo '</td>';
					echo '<td>'. $row['t_update_time'] . '</td>';
					echo '</tr>';
				}
				echo '</table>';
			}
		}

		//explore tweet
		else if (($type == 't_name') && ($_GET['name'] != '')){
			$t_name=$_GET['name'];
	
			//get t_id of target tweet name
			$query="SELECT t_id FROM tweets WHERE t_name='$t_name'";
			$data=mysqli_query($dbc, $query);
			$checktweet=mysqli_num_rows($data);
			if ($checktweet == 0) {
				$error_msg='Tweet ' . $t_name . ' doesn\'t exist.' .
						   ' <a href="tweet.php?t_name=' . $t_name . '">Tweet</a> it?';
			}else{
				$row=mysqli_fetch_array($data);
				$t_id=$row['t_id'];

				$query= "SELECT users.u_id, users.u_name, users_of_tweet_$t_id.t_update_time" .
						" FROM users INNER JOIN users_of_tweet_$t_id" . 
						" ON users_of_tweet_$t_id.u_id = users.u_id" .
						" ORDER BY users_of_tweet_$t_id.t_update_time DESC";
				$data=mysqli_query($dbc, $query) or die ('Error retrieving user\'s tweets');
				echo '<p> Authors of ' . $t_name;
				echo '<a href="tweet.php?t_name=' . $t_name . '">(tweet)</a>';
				echo '</p>';
				echo '<table>';
				echo '<tr><th>User</th><th>Last Change</th>';
				while($row = mysqli_fetch_array($data)){
					echo '<tr>';
					echo '<td>';
					echo '(<a href="explore.php?type=u_name&name=' . $row['u_name'] . '&submit=Explore">explore</a>)';
					echo $row['u_name'];
					echo '</td>';
					echo '<td>';
					echo $row['t_update_time'];
					echo '</tr>';
				}
				echo '</table>';
			}
		}

		//invalid explore
		else{
			$error_msg= "Please select the type and provide a name.";
		}

		//display error
		echo '<p class="error">' . $error_msg . '</p>';
	}
?>

<!-- html explore form -->

<form method="get" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
	<fieldset>
		<legend>Explore</legend>
		<input type="radio" name="type" value="u_name">User<br>
		<input type="radio" name="type" value="g_name">Tweet<br>
		<label for="name">Name</label>
		<input type="text" name="name" />
	</fieldset>
	<input type="submit" value="Explore" name="submit" />
</form>

<?php

	//insert footer
	require_once('footer.php');
?>
