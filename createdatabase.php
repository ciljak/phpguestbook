
<?php // script for accessing database and first table structure establishement

/* Attempt MySQL server connection. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
$dbc = mysqli_connect("localhost", "admin", "test*555", "test");
 
// Check connection
if($dbc === false){
    die("ERROR: Could not connect to database. " . mysqli_connect_error());
}
 
// Attempt create table query execution
$sql = "CREATE TABLE guestbook(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name_of_writer VARCHAR(30) NOT NULL,
    write_date DATETIME NOT NULL,
    email VARCHAR(70) NOT NULL UNIQUE,
    message_text TEXT
)";
if(mysqli_query($dbc, $sql)){
    echo "Table created successfully.";
} else{
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($dbc);
}
 
// Close connection
mysqli_close($dbc);
?>