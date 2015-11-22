<?php 
	$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
	require_once( $parse_uri[0] . 'wp-load.php' );
?>

<?php 

// the email
$email = strtolower($_POST['email']);

//if the email is valid
if (eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", trim($email))) 
{
	
	//get all the current emails
	$stack = get_option('subscribed_emails');
	
	//if there are no emails in the database
	if(!$stack)
	{
		//update the option with the first email as an array
		update_option('subscribed_emails', array($email));	
	}
	else
	{
		//if the email already exists in the array
		if(in_array($email, $stack))
		{
			echo '<div class="alert-box red">'; 
			_e('<strong>Oh snap!</strong> That email address is already subscribed!', THB_THEME_NAME);
			echo "</div>";
		}
		else
		{
			
			// If there is more than one email, add the new email to the array
			array_push($stack, $email);
			
			//update the option with the new set of emails
			update_option('subscribed_emails', $stack);
			
			//Open subscribers csv file
			$fp = fopen('subscribers.csv', 'w');
			
			//write in a format that CSV intepreters can understand
			foreach($stack as $line)
			{
				$val = explode(",",$line);
				fputcsv($fp, $val);
			}
			
			//close file
			fclose($fp);
			echo '<div class="alert-box green">'; 
			_e("<strong>Well done!</strong> Your address has been added", THB_THEME_NAME);
			echo "</div>";
		}
	}
}
else
{
	echo '<div class="alert-box red">';  
	_e("<strong>Oh snap!</strong> Please enter a valid email address", THB_THEME_NAME);
	echo "</div>";
}

?>