<!-- ************************************************* -->
<!-- PHP "self" code handling guestbook                -->
<!-- ************************************************* -->
<!-- Vrsion: 1.0        Date: 5.9.2020 by CDesigner.eu -->

<?php
	// two variables for message and styling of the mesage with bootstrap
	$msg = '';
	$msgClass = '';

	// default values of auxiliary variables
	$condolence ="";
	$is_result = "false"; //before hitting submit button no result is available
	


	// Control if data was submitted
	if(filter_has_var(INPUT_POST, 'submit')){
		// Data obtained from $_POST are assigned to local variables
		$name = htmlspecialchars($_POST['name']);
		$email = htmlspecialchars($_POST['email']);
		$condolence = htmlspecialchars($_POST['condolence']); 
		
		$is_result = "true";

		// Controll if all required fields was written
		if(!empty($email) && !empty($name) && !empty($condolence)){
			// If check passed - all needed fields are written
			// Check if E-mail is valid
			if(filter_var($email, FILTER_VALIDATE_EMAIL) === false){
				// E-mail is not walid
				$msg = 'Please use a valid email';
				$msgClass = 'alert-danger';
			} else {
				// E-mail is ok
				$toEmail = 'ciljak@localhost.org'; //!!! e-mail address to send to - change for your needs!!!
				$subject = 'Guestbook entry from '.$name;
				$body = '<h2>To your Guestbook submitted:</h2>
					<h4>Name</h4><p>'.$name.'</p>
					<h4>Email</h4><p>'.$email.'</p>
					<h4>Message</h4><p>'.$condolence.'</p>
				';

				// Email Headers
				$headers = "MIME-Version: 1.0" ."\r\n";
				$headers .="Content-Type:text/html;charset=UTF-8" . "\r\n";

				// Additional Headers
				$headers .= "From: " .$name. "<".$email.">". "\r\n";

				// !!! Add entry to the database and redraw all posts into guestbook list with newest post as first

				   // insert into databse 

						// make database connection
						$dbc = mysqli_connect("localhost", "admin", "test*555", "test");
 
                        // Check connection
							if($dbc === false){
								die("ERROR: Could not connect to database. " . mysqli_connect_error());
							}
						
						// INSERT new entry
					    $date = date('Y-m-d H:i:s'); // get current date to log into databse along condolence written
						$sql = "INSERT INTO guestbook (name_of_writer, write_date, email, message_text) 
						VALUES ('$name', '$date', '$email' , '$condolence')";



						if(mysqli_query($dbc, $sql)){
							
							$msg = 'Condolence sucessfully added to database.';
					        $msgClass = 'alert-success';
						} else{
							
							$msg = "ERROR: Could not able to execute $sql. " . mysqli_error($dbc);
					        $msgClass = 'alert-danger';
						}

						// end connection
						    mysqli_close($dbc);
				if(mail($toEmail, $subject, $body, $headers)){
					// Email Sent
					$msg = 'Your condolence was sucessfully send via e-mail';
					$msgClass = 'alert-success';
				} else {
					// Failed
					$msg = 'Your condolence was not sucessfully send via e-mail';
					$msgClass = 'alert-danger';
				}
			}
		} else {
			// Failed - if not all fields are fullfiled
			$msg = 'Please fill in all contactform fields';
			$msgClass = 'alert-danger'; // bootstrap format for allert message with red color
		}

	};	
		
?>

<!-- **************************************** -->
<!-- HTML code containing Form for submitting -->
<!-- **************************************** -->
<!DOCTYPE html>
<html>
<head>
	<title> Guestbook example  </title>
	<link rel="stylesheet" href="./css/bootstrap.min.css"> <!-- bootstrap mini.css file -->
	<link rel="stylesheet" href="./css/style.css"> <!-- my local.css file -->
	
</head>
<body>
	<nav class="navbar navbar-default">
      <div class="container">
        <div class="navbar-header">    
          <a class="navbar-brand" href="index.php">Guestbook example v. 1.0</a>
        </div>
      </div>
    </nav>
    <div class="container">	
    	
	  <?php if($msg != ''): ?>
    		<div class="alert <?php echo $msgClass; ?>"><?php echo $msg; ?></div>
      <?php endif; ?>	

		<img id="calcimage" src="./images/guestbook.jpg" alt="Calc image" width="200" height="200">

      <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	      <div class="form-group">
		      <label>Please provide Your name:</label>
		      <input type="text" name="name" class="form-control" value="<?php echo isset($_POST['name']) ? $name : 'Your Name'; ?>">
	      </div>
	      <div class="form-group">
	      	<label>E-mail:</label>
	      	<input type="text" name="email" class="form-control" value="<?php echo isset($_POST['email']) ? $email : 'e-mail'; ?>">
	      </div>
		  <div class="form-group">
	      	<label>Your message for Guestbook:</label>
	      	<input type="text" name="condolence" class="form-control" value="<?php echo isset($_POST['condolence']) ? $condolence : 'Your text goes here ...'; ?>">
	      </div>
	      
		  

	      <button type="submit" name="submit" class="btn btn-warning"> Send your condolence </button>

		  <?php   //($is_result == "true") ? {          
			     // echo "<label> = </label> ";
				 // echo " <input type="text" id="result_field" name="result_field" value="$result"  >  <br>" ;   
				 //    } : ''; 
				 if ($is_result ) {
					

						echo "<br> <br>";
						 echo " <table class=\"table table-success\"> ";
						echo " <tr>
						       <td><h3> = $condolence</h3> <td>
							  </tr> "; 
							  echo " </table> ";
					
					//echo " <input type="text" id="result_field" name="result_field" value="$result"  >  <br>" ;
				} ; 
				 ?>
                 <br>
				 	
					 
    	            <?php if($msg != ''): ?>  <!-- This part show error or warning message if one of the operand does not meet calculations requirements - dividing by zero -->
						<br><br>	
    		        <div class="alert <?php echo $msgClass; ?>"><?php echo $msg; ?></div>
		            <?php endif; ?>
					
      </form>
	</div>
	
	   <div class="footer"> 
          <a class="navbar-brand" href="https://cdesigner.eu"> Visit us on CDesigner.eu </a>
		</div>
      
</body>
</html>