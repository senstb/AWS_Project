

<?php
require 'vendor/autoload.php';

$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);

$result = $rds->describeDBInstances([
    'DBInstanceIdentifier' => 'mp1-rca',
]);
$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
print "============\n". $endpoint . "================\n";
$link = mysqli_connect($endpoint,"bsenst","josy93!","itm444db") or die("Error " . mysqli_error($link)); 

if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

//conection: 
//echo "Hello world"; 
//echo "Printing Result: " . $link;

$sql = "CREATE TABLE comments
(
	ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	user VARCHAR(32),
	email VARCHAR(32),
	phone VARCHAR(32),
	s3x VARCHAR(256),
	s3y VARCHAR(256),
	jpgfile VARCHAR(256),
	state TINYINT(3),
	date TIMESTAMP)";

if (mysqli_query($link, $sql)){
    echo "Table persons created successfully";
} else {
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}
mysqli_close($link);
?>
