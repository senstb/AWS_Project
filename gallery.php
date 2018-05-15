<html>
<head><title>Gallery</title>
<style type="text/css:">
tst {
text-align: center;
text-size: 150px;
}


</style>
</head>
<body>

<?php

session_start();
$email = $_POST["email"];
require 'vendor/autoload.php';
$rds = new Aws\Rds\RdsClient([
'version' => 'latest',
'region'  => 'us-east-1'
]);

#$result = $client->describeDBInstances(array(
 #   'DBInstanceIdentifier' => 'mp1-rca',
#));

$result = $rds->describeDBInstances([
    'DBInstanceIdentifier' => 'mp1-rca',
]);

$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
#print "============\n". $endpoint . "================\n";

#echo $endpoint;
//echo "begin database";
$link = mysqli_connect($endpoint,"controller","letmein888","db444Name") or die("Error " . mysqli_error($link));

if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

mysqli_query($link, "SELECT * FROM comments WHERE email = '$email'");
$results = $link->insert_id;
$query = "SELECT * FROM comments";

if($res =$link->query($query))
{
	# printf("Select returned %d rows.\n", $res->num_rows);
}

//$link->real_query("SELECT * FROM items");

#$res = $link->use_result();
#function imageCreateFromAny($filepath) { 
  #$type = exif_imagetype($filepath); // [] if you don't have exif you could use getImageSize() 
  #$imageTypes = array( 
     #  1,  // [] jpg 
     #  2,  // [] png 
    #); 
    #if (!in_array($type, $imageTypes)) { 
     #  return false; 
    #} 
    #switch ($type) { 
        #case 2 : 
         #  $img = imageCreateFromJpeg($filepath); 
        #break; 
        #case 2 : 
         #   $img = imageCreateFromPng($filepath); 
        #break; 
    #}    
    #return $img;  
#} 

#function LoadJPEG ($imgURL) {

   # $fo = fopen($imgURL, "r");
   # $imageFile = fread ($fo, 3000000);
   # fclose($fo);

    #$tmp = tempnam ("/temp", "IMG");
    #$fo = fopen($tmp, "w");
    #fwrite($fo, $imageFile);
    #fclose($fo);
    #$img = imagecreatefromjpeg ($tmp);
    #unlink($tmp);
    #if (!$img) {
     #   print "Could not create JPEG image $imgURL";
    #}

    #return $img;
#}

#echo "Result set order...\n";
while ($row = $res->fetch_assoc()) {
#$img = imagecreatefrompng($row['rs3URL']);
#imagepng($img);
#$p = $row['rs3URL'];
#echo "here";
#echo $p;
#$a = file_get_contents("$p");
#echo "aboutA ";
#echo $a; 
#$img = imagecreatefromjpeg("".$row['rs3URL']);
#       $image = new Imagick();
#		$image Imagick::thumbnailImage(int 25, int 25 [, bool $bestfit= false [, bool $fill = false]] )
#       $f = fopen($row['rs3URL'], 'rb');
#		$imageck = new\Imagick(realpath($f));
#		$imageck -> thumbnailImage(100, 100, true, true);
#       $image->readImageFile($f);

#    echo '<img src=$a border=0>';
#echo $row['ID'] . "Email: " . $row['email'];
#echo $row['rs3URL'] . "f : " . $row['fs3URL'];

	#$image = new Imagick();
	#$f = fopen('http://www.url.com/image.jpg', 'rb');
	#$imageck = new\Imagick(realpath($f));
	#$imageck -> thumbnailImage(100, 100, true, true)
	#$image->readImageFile($f);
	printf("\n");
	echo $row['email'];
	echo '<img src="'.$row['rs3URL'].'" width="200" height="200" />';
	printf("\n");
    #echo "<img src =\" " . $row['rs3URL'] . "\" /><img src =\"" .$row['fs3URL'] . "\"/>";
#echo $row['ID'] . "Email: " . $row['email'];
#echo $row['rs3URL'] . "f : " . $row['fs3URL'];
}
$link->close();
?>
</body>
</html>
