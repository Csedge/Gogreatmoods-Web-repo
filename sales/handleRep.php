<?php
	session_start();
	ob_start();
	include "connectTo.php";
	$id = $_SESSION['userId'];
	//include($_SESSION['fileroot'].'includes/connection.inc.php');
	//include($_SESSION['fileroot'].'email/newDistributorEmail.php');

?>

<body>
<div id="container">

  <!--<?php include 'distributor/leftSidebarNavDistributor.php' ; ?>-->
  <div id="content">
  <?php 
  	function isUniqueEmail($link, $table1, $email) {
		$query = "SELECT * FROM $table1 WHERE email='$email'";
		$result = mysqli_query($link, $query);
		if(mysqli_num_rows($result) >= 1) {
			echo "I'm sorry, that email address is already being used, please use another one.";
			return false;
		} else {
			return true;
		}
	}
	function is_image($path)
	{
		$a = getimagesize($path);
		$image_type = $a[2];
		if(in_array($image_type , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP)))
		{
			return true;
		}
			return false;
	}
	// Remove empty spaces from file name
	function checkName($name){
		$nospaces = str_replace(' ','_',$name);
		return $nospaces;
	}
	/**  process_image
	**	This function will first verify if the file uploaded is an image file.
	**	Next, the image will save the file in the desired directory in a folder labeled with the ID from the parameter.
	**      Last, the directory path to the image is returned so it can be saved to the database.
	**/
	function process_image($name, $id, $tmpPic, $baseDirPath){
	
		$cleanedPic = checkName($_FILES["$name"]["name"]);	
		if(!is_image($tmpPic)) {
    			// is not an image
    			echo $cleanedPic . " is not an image file. ";
    		} else {
    			if($_FILES['$name']['error'] > 0) {
				echo $_FILES['$name']['error'] . "<br />";
			} else {
				
				if (file_exists($baseDirPath.$id."/".$cleanedPic)){
					echo $cleanedPic . " already exists. ";
				} else {
					$picDirectory = $baseDirPath;
					
					
					if (!is_dir($picDirectory.$id)){
						mkdir($picDirectory.$id);
						
					}
					$picDirectory = $picDirectory.$id."/";
					move_uploaded_file($tmpPic, $picDirectory . $cleanedPic);
					echo "\n$cleanedPic uploaded.<br />";
					$imagePath = "images/distributors/".$id."/".$cleanedPic;
					
					return $imagePath;
				}
			}
		}
	}// end banner picture upload operations
	
	//include "../redirect.php";
	
	$link = connectTo();
	$table1 = "user_info";
	$table2 = "users";
	$table3 = "representatives";
	$company = mysqli_real_escape_string($link, $_POST['firmName']);
	$FName = mysqli_real_escape_string($link, $_POST['fname']);
	$MName = mysqli_real_escape_string($link, $_POST['mname']);
	$LName = mysqli_real_escape_string($link, $_POST['lname']);
	$ssn = mysqli_real_escape_string($link, $_POST['ssn']);
	$address1 = mysqli_real_escape_string($link, $_POST['address1']);
	$address2 = mysqli_real_escape_string($link, $_POST['address2']);
	$city = mysqli_real_escape_string($link, $_POST['city']);
	$state = mysqli_real_escape_string($link, $_POST['state']);
	$zip = mysqli_real_escape_string($link, $_POST['zip']);
	$email = mysqli_real_escape_string($link, $_POST['email']);
	$homePhone = mysqli_real_escape_string($link, $_POST['phone']);
	$fbPage = mysqli_real_escape_string($link, $_POST['fbPage']);
	$twitter = mysqli_real_escape_string($link, $_POST['twitter']);
	$linkedin = mysqli_real_escape_string($link, $_POST['linkedin']);
	$loginPass = mysqli_real_escape_string($link, trim($_POST['pass']));
	$salesMan = mysqli_real_escape_string($link, $_POST['sales']);
	//$cellPhone = mysqli_real_escape_string($link, $_POST['cellphone']);
	//$workPhone = $_POST['workphone'];
	$extPhone = mysqli_real_escape_string($link, $_POST['ext']);
	$paypal = mysqli_real_escape_string($link, $_POST['paypalemail']);
	$landingPage = "setupEditWebsite/editClub.php";
	$who = "Representative";
	$salt = time(); 			// create salt using the current timestamp 
	$loginPass = sha1($loginPass.$salt); 	// encrypt the password and salt with SHA1
	$bannerDirPath = "";
	$distPicDirPath = "";
	// code to upload banner and picture
	$banner = $_FILES['AddGroupPhoto']['tmp_name'];
	$distPic = $_FILES['AddPersonalPhoto']['tmp_name'];
	$imageDirPath = $_SERVER['fileroot'].'images/representatives/';
	
	
	
	if(isUniqueEmail($link, $table1, $email))
	 {
		$query1 = "INSERT INTO $table2 (username, password, Security, landingPage, salt, created, lastLogin, role)";
		$query1 .= "VALUES('$email','$loginPass','1','$landingPage','$salt', now(), now(), '$who')";
		$res1 = mysqli_query($link, $query1);
		

		
		$query2 = "INSERT INTO $table1 (companyName, FName, MName, LName, ssn, address1, address2, city, state, zip, email, homePhone, fbPage, twitter, linkedin, salesPerson, workPhoneExt, userPaypal,role)";
		$query2 .= " VALUES('$company','$FName','$MName','$LName','$ssn','$address1','$address2','$city','$state','$zip','$email','$homePhone','$fbPage','$twitter','$linkedin', '$id','$extPhone','$paypal','$who')";
		$res2 = mysqli_query($link, $query2);
		
		$query4 = "SELECT * FROM $table1 WHERE email = '$email';";
		$result4 = mysqli_query($link, $query4)or die("MySQL ERROR om query 4: ".mysqli_error($link));
		$idrow = mysqli_fetch_assoc($result4);
		$userid = $idrow['userInfoID'];
		
                $query3 = "INSERT INTO $table3 (companyName, FName, MName, LName, ssn, address1, address2, city, state, zip, email, homePhone, fbPage, twitter, linkedin, salesPerson, workPhoneExt, bannerPath, repPicPath,setupID,loginid)";
		$query3 .= " VALUES('$company','$FName','$MName','$LName','$ssn','$address1','$address2','$city','$state','$zip','$email','$homePhone','$fbPage','$twitter','$linkedin', '$salesMan','$extPhone', '$banner','$distPic','$id','$userid')";
                $res3 = mysqli_query($link, $query3)or die("MySQL ERROR om query 3: ".mysqli_error($link));

		mysqli_query($link, "start transaction;");
	        //insert data into users table
		
		// insert data into distributors table
		
		if($res1 && $res2 && $res3 && $result4)
		{
		     mysqli_query($link, "commit;");
	         	//echo "Your account has been successfuly created.\n\n";
			 //newDistributorEmail($email,$FName,$LName,$cellPhone);
	        // get the user id and insert into user_info		 
	        $getUser = "SELECT * FROM users WHERE username = '$email'";
		$userResult = mysqli_query($link, $getUser)or die("MySQL ERROR get user query: ".mysqli_error($link));
		$userRow = mysqli_fetch_assoc($userResult);
		$cust = $userRow['customerID'];
		
		$idInsert = "UPDATE user_info SET user_table_id ='$cust' WHERE email = '$email'";
		$insertResult = mysqli_query($link, $idInsert)
		or die("MySQL ERROR get user insert: ".mysqli_error($link));
		
		} else
		{
                       mysqli_query($link, "rollback;");
		     echo "I'm sorry, there was a problem creating your account.";
		}
	       
                
              
	// picture operations
	$query2 = "SELECT userInfoID FROM $table1 WHERE email = '$email'";
	$res2 = mysqli_query($link, $query2);
	
	if ($res2)
	{
	    // get userInfoID so we can create a folder to store the images
	    // the path will be images/Sample_Websites/websiteID/<image>
	    $row = mysqli_fetch_assoc($res2);
	    $userID = $row[userInfoID];
	    // image upload operations
	    if($banner != '')
	    {
	    
		$bannerPicPath = process_image('AddGroupPhoto',$userID, $banner, $imageDirPath);
		if($bannerPicPath !='')
		{
			$query = "UPDATE $table1 SET bannerPath = '$bannerPicPath' WHERE userInfoID = '$userID'";
			mysqli_query($link, $query);
		}
	   }
	    if($distPic != '')
	    {
		$personalPicPath = process_image('AddPersonalPhoto',$userID, $distPic, $imageDirPath);
		if($personalPicPath !='')
		{
			$query = "UPDATE $table1 SET picPath = '$personalPicPath' WHERE userInfoID = '$userID'";
			mysqli_query($link, $query);
		}
	    }
	} 
	else
	{
			echo $email." not found in database.\n\n";
	}// end else
		header( 'Location: viewReps.php' );
	}// end if	

?>
   
</div>
<!--end content-->
<?php include 'footer.php' ; ?>
</div>
<!--end container-->

</body>
</html>
<?php
ob_end_flush();
?>