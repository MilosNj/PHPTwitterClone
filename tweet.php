<?php

	//Start the session
	require_once('startsession.php');

	//Insert header
	$page_title='Tweet';
	require_once('header.php');


	//Show the navigation menu
	require_once('navmenu.php');
?>

<?php
	
	//prepare error message var
	$error_msg="";
	//if not even logged in
	if (!isset($_SESSION['u_id'])){
		$error_msg= 'Sorry, please login to tweet.';
	}

	//if logged in and already posted
	else if (isset($_GET['submit'])){
		//Connect to database
		require_once('connectvars.php');
		$dbc=mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to MySQL Database');
		
		//parse geneinfo
		$u_id=$_SESSION['u_id'];
		$t_name=mysqli_real_escape_string($dbc, trim($_GET['t_name']));

		
		if(!empty($t_name)){
				
			//fix tweets
			$query="select * FROM tweets WHERE t_name='$t_name'";
			$data=mysqli_query($dbc, $query);
			$checktweet=mysqli_num_rows($data);
			if ($checktweet == 0) {  //fresh tweet

				//insert into tweet bank
				$query="INSERT INTO tweets (t_name) VALUES ('$t_name')";
				mysqli_query($dbc, $query);
			}else{

				//update t_update_time
				$row=mysqli_fetch_array($data);
				$t_id=$row['t_id'];
				$query="UPDATE tweets SET t_update_time=NOW() WHERE t_id='$t_id'";
				mysqli_query($dbc, $query) or die ('Error updating tweets');
			}


			//get t_id from tweets
			$query="SELECT * FROM tweets WHERE t_name='$t_name'";
			$data=mysqli_query($dbc, $query);
			$row=mysqli_fetch_array($data);
			$t_id=$row['t_id'];
				

			//fix users_of_tweet
			if ($checktweet == 0) {  //create table when no current table exists
				$query="CREATE TABLE users_of_tweet_$t_id (u_id int NOT NULL UNIQUE, t_update_time timestamp NOT NULL DEFAULT NOW())";
				mysqli_query($dbc, $query) or die('Error creating tweet\'s own table');
			}

			$query="SELECT * FROM users_of_tweet_$t_id WHERE u_id='$u_id'";
			$data=mysqli_query($dbc, $query) or die('Error retrieving tweet\'s own table');
			$checkuser=mysqli_num_rows($data);
			if ($checkuser == 0) { //new author of the tweet
				$query="INSERT INTO users_of_tweet_$t_id (u_id) VALUES ('$u_id')";
				mysqli_query($dbc, $query) or die('Error inserting into tweets\'s own table');
			}else{ //old user update tweet
				$query="UPDATE users_of_tweet_$t_id SET t_update_time=NOW() WHERE u_id='$u_id'";
				mysqli_query($dbc, $query) or die('Error updating tweet time in tweet\'s own table');
			}

			//fix tweets_of_user
			$query="SELECT * FROM tweets_of_user_$u_id WHERE t_id='$t_id'";
			$data=mysqli_query($dbc, $query);
			$checktweet=mysqli_num_rows($data);
			if ($checktweet == 0) { //insert fresh tweet
				$msg='Fresh tweet acquired.';
				$query="INSERT INTO tweets_of_user_$u_id (t_id) VALUES ('$t_id')";
				mysqli_query($dbc, $query) or die('Error inserting into user\'s own table');
			}else{ //update old tweet
				$msg='Existing tweet changed.';
				$query="UPDATE tweets_of_user_$u_id SET t_update_time=NOW() WHERE t_id='$t_id'";
				mysqli_query($dbc, $query) or die('Error updating into user\'s own table');
			}

			//redirect to my tweet
			$mytweet_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/mytweet.php';
			header('Location: ' . $mytweet_url . '?msg=' .$msg );
	
		}else{
			$error_msg= 'Sorry, please fill in the tweet before tweeting.';
		} 
	}

	//print error
	echo '<p class="error">' . $error_msg . '</p>';
	
?>

<form method="get" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
	<fieldset>
		<legend>Tweet</legend>
		<label for="t_name">Tweet Name:</label>
		<input type="text" name="t_name" value="<?php if (!empty($_GET['t_name'])) echo $_GET['t_name']; ?>" />
	</fieldset>
	<input type="submit" value="Add" name="submit" />
</form>

<?php
	//insert footer
	require_once('footer.php');
?>
