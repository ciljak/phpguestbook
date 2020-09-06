<!-- ************************************************* -->
<!-- PHP "self" code handling guestbook                -->
<!-- ************************************************* -->
<!-- Vrsion: 1.0        Date: 5.9.2020 by CDesigner.eu -->

<?php
	// two variables for message and styling of the mesage with bootstrap
	$msg = '';
	$msgClass = '';

	// default values of auxiliary variables
	$postmessage ="";
	$is_result = "false"; //before hitting submit button no result is available
	


	// Control if data was submitted
	if(filter_has_var(INPUT_POST, 'submit')){
		// Data obtained from $_postmessage are assigned to local variables
		$name = htmlspecialchars($_POST['name']);
		$email = htmlspecialchars($_POST['email']);
		$postmessage = htmlspecialchars($_POST['postmessage']); 
		
		$is_result = "true";

		// Controll if all required fields was written
		if(!empty($email) && !empty($name) && !empty($postmessage)){
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
					<h4>Message</h4><p>'.$postmessage.'</p>
				';

				// Email Headers
				$headers = "MIME-Version: 1.0" ."\r\n";
				$headers .="Content-Type:text/html;charset=UTF-8" . "\r\n";

				// Additional Headers
				$headers .= "From: " .$name. "<".$email.">". "\r\n";

				// !!! Add entry to the database and redraw all postmessages into guestbook list with newest postmessage as first

				   // insert into databse 

						// make database connection
						$dbc = mysqli_connect("localhost", "admin", "test*555", "test");
 
                        // Check connection
							if($dbc === false){
								die("ERROR: Could not connect to database. " . mysqli_connect_error());
							}
						
						// INSERT new entry
					    $date = date('Y-m-d H:i:s'); // get current date to log into databse along postmessage written
						$sql = "INSERT INTO guestbook (name_of_writer, write_date, email, message_text) 
						VALUES ('$name', '$date', '$email' , '$postmessage')";



						if(mysqli_query($dbc, $sql)){
							
							$msg = 'postmessage sucessfully added to database.';
					        $msgClass = 'alert-success';
						} else{
							
							$msg = "ERROR: Could not able to execute $sql. " . mysqli_error($dbc);
					        $msgClass = 'alert-danger';
						}

						// end connection
						    mysqli_close($dbc);
				if(mail($toEmail, $subject, $body, $headers)){
					// Email Sent
					$msg .= 'Your postmessage was sucessfully send via e-mail';
					$msgClass = 'alert-success';
				} else {
					// Failed
					$msg = 'Your postmessage was not sucessfully send via e-mail';
					$msgClass = 'alert-danger';
				}
			}
		} else {
			// Failed - if not all fields are fullfiled
			$msg = 'Please fill in all contactform fields';
			$msgClass = 'alert-danger'; // bootstrap format for allert message with red color
		}

	};	
  
	// if delete button clicked
	if(filter_has_var(INPUT_POST, 'delete')){

		    $msg = 'Delete last mesage hit';
			$msgClass = 'alert-danger'; // bootstrap format for allert message with red color
        
            // insert into databse 

			// make database connection
			$dbc = mysqli_connect("localhost", "admin", "test*555", "test");

			// Check connection
				if($dbc === false){
					die("ERROR: Could not connect to database. " . mysqli_connect_error());
				}
			
			// DELETE last input by matching your written message
			   // obtain message string for comparison

			   $postmessage = htmlspecialchars($_POST['postmessage']); 
			   $postmessage = trim($postmessage);

			   // create DELETE query
			   $sql = "DELETE FROM guestbook WHERE message_text = "."'$postmessage'" ;



				if(mysqli_query($dbc, $sql)){
					
					$msg = 'Last message sucessfully removed from database.';
					$msgClass = 'alert-success';

					// clear entry fileds after sucessfull deleting from database
					$name ='';
					$email ='';
					$postmessage = ''; 
				} else{
					
					$msg = "ERROR: Could not able to execute $sql. " . mysqli_error($dbc);
					$msgClass = 'alert-danger';
				}

			// end connection
				mysqli_close($dbc);


			

	};

	// if reset button clicked
	if(filter_has_var(INPUT_POST, 'reset')){
		$msg = '';
		$msgClass = ''; // bootstrap format for allert message with red color
		$name = '';
		$email = '';
		$postmessage = '';
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
	      	<label>Your message for Guestbook:</label>  <!-- textera for input large text -->
	      	<textarea id="postmessage" name="postmessage" class="form-control" rows="6" cols="50"><?php echo isset($_POST['postmessage']) ? $postmessage : 'Your text goes here ...'; ?></textarea>
	      </div>
	      
		  

		  <button type="submit" name="submit" class="btn btn-warning"> Send your post </button>
		  
		  <button type="submit" name="delete" class="btn btn-danger"> Delete latest message </button>

		  <button type="submit" name="reset" class="btn btn-info"> Reset form </button>

		  <?php   //($is_result == "true") ? {          
			     // echo "<label> = </label> ";
				 // echo " <input type="text" id="result_field" name="result_field" value="$result"  >  <br>" ;   
				 //    } : ''; 
				 if ($is_result ) {
					

						echo "<br> <br>";
						 echo " <table class=\"table table-success\"> ";
						echo " <tr>
						       <td><h5> <em> Yours currently written text is: </em>$postmessage</h5> <td>
							  </tr> "; 
							  echo " </table> ";
					
					//echo " <input type="text" id="result_field" name="result_field" value="$result"  >  <br>" ;
				} ; 
				 ?>
                 <br>
				 	
					 
    	           
					
	  </form>

	  

	  <?php // script for accessing database for all records and then output them in page

			/* Attempt MySQL server connection. Assuming you are running MySQL
			server with default setting (user 'root' with no password) */
			$dbc = mysqli_connect("localhost", "admin", "test*555", "test");
			
			// Check connection
			if($dbc === false){
				die("ERROR: Could not connect to database - stage of article listing. " . mysqli_connect_error());
			}
			
			
				
						
			// read all rows (data) from guestbook table in test database
			$sql = "SELECT * FROM guestbook ORDER BY id DESC";  // read in reverse order - newest article first
			/*************************************************************************/
			/*  Output in Table - solution 1 - for debuging data from database       */
			/*************************************************************************/
			/* if data properly selected from guestbook database tabele
				if($output = mysqli_query($dbc, $sql)){
					if(mysqli_num_rows($output) > 0){  // if any record obtained from SELECT query
						// create table output
						echo "<table>"; //head of table
							echo "<tr>";
								echo "<th>id</th>";
								echo "<th>name_of_writer</th>";
								echo "<th>write_date</th>";
								echo "<th>email</th>";
								echo "<th>message_text</th>";
							echo "</tr>";
						while($row = mysqli_fetch_array($output)){ //next rows outputed in while loop
							echo "<tr>";
								echo "<td>" . $row['id'] . "</td>";
								echo "<td>" . $row['name_of_writer'] . "</td>";
								echo "<td>" . $row['write_date'] . "</td>";
								echo "<td>" . $row['email'] . "</td>";
								echo "<td>" . $row['message_text'] . "</td>";
							echo "</tr>";
						}
						echo "</table>";
						// Free result set
						mysqli_free_result($output);
					} else{
						echo "There is no postmessage in Guestbook. Please wirite one."; // if no records in table
					}
				} else{
					echo "ERROR: Could not able to execute $sql. " . mysqli_error($dbc); // if database query problem
				}
            */
			/*************************************************************************/
			/*  Output in form of Article - solution 2 - for Guestbook functionality */
			/*************************************************************************/
			// if data properly selected from guestbook database table
			if($output = mysqli_query($dbc, $sql)){
				if(mysqli_num_rows($output) > 0){  // if any record obtained from SELECT query
					
					// create Guestbook articles on page
					
					echo "<h4>Our cutomers written into the Guestbook</h4>";
					echo "<br>";

					while($row = mysqli_fetch_array($output)){ //next rows outputed in while loop
						
							// echo "<td>" . $row['id'] . "</td>"; //id is not important for common visitors
							echo " <div class=\"guestbook\"> " ;
							echo "<h4>" ."<b>From: </b>" . $row['name_of_writer'] . "</h4>";
							echo "<h6>" ."<b>Date of postmessage: </b>" . $row['write_date'] . "</h6>";
							echo "<h5>" ." <b>E-mail of sender: </b>" . $row['email'] . "</h5>";
							echo "<p id=\"guestbooktext\">" . "  <b>Text of the message: </b> <em>" . $row['message_text'] . "</em></p>";
							//echo "<br>";
							echo " </div> " ;

							echo " <div class=\"guestbookbreak\"> " ;
							echo "<br>";
							echo " </div> " ;
					}
					echo "<br>";
					// Free result set - free the memory associated with the result
					mysqli_free_result($output);
				} else{
					echo "There is no postmessage in Guestbook. Please wirite one."; // if no records in table
				}
			} else{
				echo "ERROR: Could not able to execute $sql. " . mysqli_error($dbc); // if database query problem
			}

			// Close connection
			mysqli_close($dbc);
			?>
		
		</div>
		
		
	   <div class="footer"> 
          <a class="navbar-brand" href="https://cdesigner.eu"> Visit us on CDesigner.eu </a>
		</div>
		
      
</body>
</html>