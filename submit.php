
<?php
session_start();
require 'vendor/autoload.php';
#use Aws\S3\S3Client;
#$client = S3Client::factory();
$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);
echo $_POST['useremail'];

$dir = '/tmp/';
$uploadfile = $dir . basename($_FILES['userfile']['name']);

$bucket = uniqid("php-rca-",false);

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
    'DBInstanceIdentifier' => 'mp1-rca',
]);

$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
#print "============\n". $endpoint . "================\n";

//echo ^M
$link = mysqli_connect($endpoint,"bsenst","josy93!","itm444mp") or die("Error " . mysqli_error($link));

if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

/* Prepared statement, stage 1: prepare */
#if ($stmt = $link->prepare("INSERT INTO comments (id, email,phone,filename,s3url,finishedurl,status,issubscribed) VALUES (NULL,?,?,?,?,?,?,?)")) {
 #   //echo "Prepare failed: (" . $link->errno . ") " . $link->error;
#}
#ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,

#Title VARCHAR(32),
#Content VARCHAR(500),
#user VARCHAR(20),
#email VARCHAR(20),
#phone VARCHAR(20),
#s3URL VARCHAR(256),
#jpgfile VARCHAR(256),
#state TINYINT(3),
#date TIMESTAMP)";

#$statement = $link->prepare("INSERT INTO comments (ID,Title,Content,user,phone,s3URL,jpgfile,state,date) VALUES (NULL,?,?,?,?,?,?,?,?,NULL)");

$user = $_POST['username'];
$email = $_POST['useremail'];
$phone = $_POST['phone'];
$s3url = $url; //  $result['ObjectURL']; from above
$filename = basename($_FILES['userfile']['name']);
$finishedurl = "none";
$status =0;
$issubscribed=0;

mysqli_query($link, "INSERT INTO comments (ID, user,email,phone,s3x,s3y,jpgfile,state,date) VALUES (NULL, '$user', '$email', '$phone', '$s3url', '$finishedurl', '$filename', '$status', NULL)");
$results = $link->insert_id;
echo $link->error;
echo $results;
#if( $statement !== FALSE){
#	$statement->bind_param("ssssssssi",$email,$filename,$filename,$filename,$email,$phone,$s3url,$uploadfile,$status);
#	$statement->execute();
#}

#$statement->bind_param("ssssssssi",$email,$filename,$filename,$filename,$email,$phone,$s3url,$uploadfile,$status);
#	$statement->execute();



#$stmt->bind_param("sssssii",$email,$phone,$filename,$s3url,$finishedurl,$status,$issubscribed);

#if (!$stmt->execute()) {
  #  echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;

#}
#printf("%d Row inserted.\n", $statement->affected_rows);
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
header('Location: gallery.php');    
?>