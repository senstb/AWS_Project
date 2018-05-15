
<?php
// Start the session
session_start();
// In PHP versions earlier than 4.1.0, $HTTP_POST_FILES should be used instead
// of $_FILES.
require 'vendor/autoload.php';
#use Aws\S3\S3Client;
#user AWS\SNS\SNSClient;
#$client = S3Client::factory();
$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);
echo $_POST['useremail'];

$snCl = SnsClinet::factory(
	array(
		'profile'=> 'sns-reminders'
		'region'=> 'us-east-1'
		'version'=>'2008-10-17')
);

$topic = $snCl->create_topic('asf');

$topic = $snCl->subscribe([
	'Endpoint'=> $_POST['useremail'],
	'Protocol'=> 'email',
	'TopicArn'=> 'arn:aws:sns:us-east-1:551559498977:asf'
])

$topic = $snCl->publish([
	'Message'=> 'Here is the SNS notification'
	'MessageAttributes'=>[
		'DataType'=>'string'
		'StringValue'=>'110 0001'
	],
	'Subject'=>'Notification from SNS Client'
	'TargetArn'=>'TopicArn'
	'TopicArn'=>'arn:aws:sns:us-east-1:551559498977:asf'
])

$uploaddir = '/tmp/';
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);

echo '<pre>';
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    echo "File is valid, and was successfully uploaded.\n";
} else {
    echo "Possible file upload attack!\n";
}

echo 'Here is some more debugging info:';
print_r($_FILES);

print "</pre>";




$bucket = uniqid("php-bs-",false);

#$result = $client->createBucket(array(
#    'Bucket' => $bucket
#));
# AWS PHP SDK version 3 create bucket


$result = $s3->createBucket([
    'ACL' => 'public-read-write',
    'Bucket' => $bucket,
]);

print_r($result);
#$client->waitUntilBucketExists(array('Bucket' => $bucket));
#Old PHP SDK version 2
#$key = $uploadfile;
#$result = $client->putObject(array(
#    'ACL' => 'public-read',
#    'Bucket' => $bucket,
#    'Key' => $key,
#    'SourceFile' => $uploadfile 
#));

# PHP version 3
$result = $s3->putObject([
    'ACL' => 'public-read-write',
    'Bucket' => $bucket,
   'Key' => $uploadfile,
   'SourceFile' => $uploadfile,
]);  


$url = $result['ObjectURL'];
echo $url;

$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);


$result = $rds->describeDBInstances([
    'DBInstanceIdentifier' => 'mp1-brs',
    
]);


$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
#print "============\n". $endpoint . "================\n";

//echo "begin database";^M
$link = mysqli_connect($endpoint,"controller","josy93!","itm444mp") or die("Error " . mysqli_error($link));


/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}


/* Prepared statement, stage 1: prepare */
#if ($stmt = $link->prepare("INSERT INTO comments (id, email,phone,filename,s3rawurl,s3finishedurl,status,issubscribed) VALUES (NULL,?,?,?,?,?,?,?)")) {
 #   //echo "Prepare failed: (" . $link->errno . ") " . $link->error;
#}
#ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
#PosterName VARCHAR(32),
#Title VARCHAR(32),
#Content VARCHAR(500),
#uname VARCHAR(20),
#email VARCHAR(20),
#phone VARCHAR(20),
#s3URL VARCHAR(256),
#jpgfile VARCHAR(256),
#state TINYINT(3),
#date TIMESTAMP)";



#$statement = $link->prepare("INSERT INTO comments (ID, PosterName,Title,Content,uname,phone,s3URL,jpgfile,state,date) VALUES (NULL,?,?,?,?,?,?,?,?,NULL)");



$uname = $_POST['username'];
$email = $_POST['useremail'];
$phone = $_POST['phone'];
$s3rawurl = $url; //  $result['ObjectURL']; from above
$filename = basename($_FILES['userfile']['name']);
$s3finishedurl = "none";
$status =0;
$issubscribed=0;


mysqli_query($link, "INSERT INTO comments (ID, uname,email,phone,rs3URL,fs3URL,jpgfile,state,date) VALUES (NULL, '$uname', '$email', '$phone', '$s3rawurl', '$s3finishedurl', '$filename', '$status', NULL)");

$results = $link->insert_id;
echo $link->error;
echo $results;
#if( $statement !== FALSE){
#	$statement->bind_param("ssssssssi",$email,$filename,$filename,$filename,$email,$phone,$s3rawurl,$uploadfile,$status);
#	$statement->execute();
#}

#$statement->bind_param("ssssssssi",$email,$filename,$filename,$filename,$email,$phone,$s3rawurl,$uploadfile,$status);
#	$statement->execute();



#$stmt->bind_param("sssssii",$email,$phone,$filename,$s3rawurl,$s3finishedurl,$status,$issubscribed);

#if (!$stmt->execute()) {
  #  echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;

#}
#printf("%d Row inserted.\n", $statement->affected_rows);

/* explicit close recommended */
#$statement->close();

#$link->real_query("SELECT * FROM comments");
#$res = $link->use_result();


$query = "SELECT * FROM comments";
if($res =$link->query($query))
{
	 printf("Select returned %d rows.\n", $res->num_rows);
}

echo "Result set order...\n";




while ($row = $res->fetch_assoc()) {
    echo $row['ID'] . " " . $row['email']. " " . $row['phone'];
}


$link->close();

//add code to detect if subscribed to SNS topic 
//if not subscribed then subscribe the user and UPDATE the column in the database with a new value 0 to 1 so that then each time you don't have to resubscribe them

// add code to generate SQS Message with a value of the ID returned from the most recent inserted piece of work
//  Add code to update database to UPDATE status column to 1 (in progress)

?>