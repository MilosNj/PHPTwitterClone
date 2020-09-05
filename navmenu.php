<?php

	//generating navigation bar
	echo '<hr />';
	if(isset($_SESSION['u_name'])){
		echo '<a href="index.php">HOME</a>';
		echo ' | ';
		echo '<a href="tweet.php">TWEET</a>';
		echo ' | ';
		echo '<a href="explore.php">EXPLORE</a>';
		echo ' | ';
		echo '<a href="mytweet.php">MY TWEETS</a>';
		echo ' | ';
		echo '<a href="logout.php">LOGOUT ('.$_SESSION['u_name'].')</a>';
	}
	else{
		echo '<a href="login.php">LOGIN</a>';
		echo ' | ';
		echo '<a href="signup.php">SIGN-UP</a>';
	}
	echo '<hr />';
?>
