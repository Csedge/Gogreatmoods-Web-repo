<?php
	session_start();
	ob_start();
	if(!isset($_SESSION['authenticated']))
       {
            header('Location: ../index.php');
            exit;
       }
	include '../includes/connection.inc.php';
	include('../samplewebsites/imageFunctions.inc.php');
        $link = connectTo();
        $userID = $_SESSION['userId'];
        $table = "Dealers";
        
	$upload_msg = "Message: <br />";
	
	
	// check if form has been submitted
	if(isset($_POST['submit'])){
	  $dealer_group = $_POST['group'];
	  if ($dealer_group == ""){
	    $upload_msg .= "Cannot save because group ID not found. <br />";
	  }else{
	  $orgLeaderPhoto = $_FILES['AddOrgLeaderPhoto']['tmp_name'];
	  $orgLocationPhoto = $_FILES['AddOrgLocationPhoto']['tmp_name'];
	  $orgGroupPhoto = $_FILES['AddOrgGroupPhoto']['tmp_name'];
	  $orgContactPhoto = $_FILES['AddOrgContactPhoto']['tmp_name'];
	  $orgBannerPhoto = $_FILES['AddOrgBannerPhoto']['tmp_name'];
	  $imageDirPath = $_SERVER['DOCUMENT_ROOT'].'/images/dealers/';
	  $OrgGroupPicPath = "";
	  $OrgLeaderPicPath = "";
	  $OrgLocationPicPath = "";
	  $OrgContactPhotoPath = "";
	  $OrgBannerPathPath = "";
	  	
	  $link = connectTo();
	  $table = "Dealers";
	
	  /**  process_image
	  **	This function will first verify if the file uploaded is an image file.
	  **	Next, the image will save the file in the desired directory in a folder labeled with the ID from the parameter.
	  **      Last, the directory path to the image is returned so it can be saved to the database.
	  **/
	  function process_image($name, $id, $tmpPic, $baseDirPath){

		$cleanedPic = checkName($_FILES["$name"]["name"]);	
		if(!is_image($tmpPic)) {
    			// is not an image
    			$upload_msg .= $cleanedPic . " is not an image file. <br />";
    		} else {
    			if($_FILES['$name']['error'] > 0) {
				$upload_msg .= $_FILES['$name']['error'] . "<br />";
			} else {
				
				if (file_exists($baseDirPath.$id."/".$cleanedPic)){
					$imagePath = "images/dealers/".$id."/".$cleanedPic;
				} else {
					$picDirectory = $baseDirPath;
					
					
					if (!is_dir($picDirectory.$id)){
						mkdir($picDirectory.$id);
						
					}
					$picDirectory = $picDirectory.$id."/";
					move_uploaded_file($tmpPic, $picDirectory . $cleanedPic);
					$upload_msg .= "$cleanedPic uploaded.<br />";
					$imagePath = "images/dealers/".$id."/".$cleanedPic;
					
					
				}// end third inner else
				return $imagePath;
			} // end first inner else
		      } // end else
	     }// end process_image
	     if($orgLeaderPhoto != ''){
		$OrgLeaderPicPath = process_image('AddOrgLeaderPhoto',$dealer_group, $orgLeaderPhoto, $imageDirPath);
		if($OrgLeaderPicPath !=''){
			$query = "UPDATE $table SET leader_pic = '$OrgLeaderPicPath' WHERE loginid = '$dealer_group'";
			mysqli_query($link, $query);
			}
		}
	    if($orgContactPhoto != ''){
		$OrgContactPicPath = process_image('AddOrgContactPhoto',$dealer_group, $orgContactPhoto, $imageDirPath);
		if($OrgContactPicPath !=''){
			$query = "UPDATE $table SET contact_pic = '$OrgContactPicPath' WHERE loginid = '$dealer_group'";
			mysqli_query($link, $query);
			}
		}
		if($orgLocationPhoto != ''){
		$OrgLocationPicPath = process_image('AddOrgLocationPhoto',$dealer_group, $orgLocationPhoto, $imageDirPath);
		if($OrgLocationPicPath !=''){
			$query = "UPDATE $table SET location_pic = '$OrgLocationPicPath' WHERE loginid = '$dealer_group'";
			mysqli_query($link, $query);
			}
		}
	    if($orgGroupPhoto != ''){
		$OrgGroupPicPath = process_image('AddOrgGroupPhoto',$dealer_group, $orgGroupPhoto, $imageDirPath);
		if($OrgGroupPicPath !=''){
			$query = "UPDATE $table SET group_team_pic = '$OrgGroupPicPath' WHERE loginid = '$dealer_group'";
			mysqli_query($link, $query);
			}
		}
		if($orgBannerPhoto != ''){
		$OrgBannerPathPath = process_image('AddOrgBannerPhoto',$dealer_group, $orgBannerPhoto, $imageDirPath);
		if($OrgBannerPathPath !=''){
			$query = "UPDATE $table SET banner_path = '$OrgBannerPathPath' WHERE loginid = '$dealer_group'";
			mysqli_query($link, $query);
			}
		}
		$redirect = "Location:goals.php?groupid=$dealer_group";
         	header($redirect);	
	  }// end $dealer_group else	
	}// end if
	$group = $_GET["groupid"];
        
	$query = "SELECT * FROM $table WHERE loginid='$group'";
	$result = mysqli_query($link, $query)or die ("couldn't execute query.".mysql_error());
	$row = mysqli_fetch_assoc($result);
	$about = $row['About'];
	$fundraiserid = $row['loginid'];
?>

<!DOCTYPE html>
<html>
	<head>
	<meta charset="UTF-8" />
	<title>GreatMoods | Setup/Edit Website - Photos</title>
	<script type="text/javascript">
	function validate(form) {
		var pass1 = form['loginPass'].value;
		var pass2 = form['confirmPass'].value;
		if(pass1 == "" || pass1 == null) {
			alert("please enter a valid password");
			return false;
		}
		if(pass1 != pass2) {
			alert("passwords do not match");
			return false;
		}
		return true;
	}
	</script>
	<link href="../css/mainRecruitingStyles.css" rel="stylesheet" type="text/css" />
	<link href="../css/setupFormStyles.css" rel="stylesheet" type="text/css" />
	</head>
	<body id="photos">
<div id="container">
      <?php include 'header.inc.php' ; ?>
      <?php include 'leftNavSetupEditWebsite.php' ; ?>
      <div id="contentMain858">
    <div class="nav2">
          <ul id="setupNav">
        <li><a href="contacts.php" class="contactsnav"><<&nbsp;</a></li>
        <li><a href="selectFundraiser.php" class="infonav">Fundraiser Type</a></li>
        <li>|</li>
        <li><a href="disthome.php" class="editnav">Edit Account</a></li>
        <li>|</li>
        <li><a href="information.php?groupid=<?echo $fundraiserid;?>" class="infonav">Information</a></li>
        <li>|</li>
        <li><a href="reasons.php?groupid=<?echo $fundraiserid;?>" class="reasonsnav">Reasons</a></li>
        <li>|</li>
        <li><a href="contacts.php?groupid=<?echo $fundraiserid;?>" class="contactsnav">Contacts</a></li>
        <li>|</li>
        <li id="current"><a href="photos.php" class="photosnav">Photos</a></li>
        <li>|</li>
        <li><a href="goals.php?groupid=<?echo $fundraiserid;?>" class="goalsnav">Goals</a></li>
        <li><a href="goals.php">&nbsp;>> </a></li>
      </ul>
        </div>
    <!--end nav2-->
    
     <p style="font-size: 24px;">Setup/Edit Website</p>
    <div class="setupLeft">
          <h3>Personalize Website With Photos</h3>
          <p>To personalize your webpage, attach photo(s).<br> 
          Acceptable formats for photos are .jpg or .png files.<br> 
          Acceptable formats for video are .avi wmv, mpg/.mpeg, .mov, mp4.rm/.ram, .swf/.flv, </p>
    <form class="photos" action="photos.php" method="POST" name="addPhotos" enctype="multipart/form-data" id="addPhotos" onSubmit="return validate(this);">
          <p>
        <label for="AddOrgLeaderPhoto"><b>Photo of Organization Leader(s):</b></label>
        <br />
        <input name="AddOrgLeaderPhoto" type="file" id="AddOrgLeaderPhoto" />
        <input name="RemoveOrgLeaderPhoto" type="checkbox" id="RemoveOrgLeaderPhoto" value="RemoveOrgLeaderPhoto">
        <label for="RemoveOrgLeaderPhoto">Remove Current Photo</label>
		<label for="descOrgLeader"><b>Name(s)of Leader(s):</b></label>
		<textarea name="description" cols="50" rows="1" id="description"></textarea>
      </p>
         <p>
        <label for="AddOrgContactPhoto"><b>Photo for Contact Information:</b></label>
        <br />
        <input name="AddOrgContactPhoto" type="file" id="AddOrgContactPhoto" />
        <input name="RemoveOrgContactPhoto" type="checkbox" id="RemoveOrgContactPhoto" value="RemoveOrgContactPhoto">
        <label for="RemoveOrgContactPhoto">Remove Current Photo</label>
		<label for="descOrgLeader"><b>Name for Contact Information:</b></label>
		<textarea name="description" cols="50" rows="1" id="description"></textarea>
      </p>
		 <p>
        <label for="AddOrgLocationPhoto"><b>Photo of Student Leader:</b></label>
        <br />
        <input name="AddOrgLocationPhoto" type="file" id="AddOrgLocationPhoto" />
        <input name="RemoveOrgLocationPhoto" type="checkbox" id="RemoveOrgLocationPhoto" value="RemoveOrgLocationPhoto">
        <label for="RemoveOrgLocationPhoto">Remove Current Photo</label>
		<label for="descOrgLeader"><b>Name(s)of Student Leader(s):</b></label>
		<textarea name="description" cols="50" rows="1" id="description"></textarea>
      </p>
      <p>
        <label for="AddOrgGroupPhoto"><b>Photo of Group/Team:</b></label>
        <br />
        <input name="AddOrgGroupPhoto" type="file" id="AddOrgGroupPhoto" />
        <input name="RemoveOrgGroupPhoto" type="checkbox" id="RemoveOrgGroupPhoto" value="RemoveOrgGroupPhoto">
        <label for="RemoveOrgGroupPhoto">Remove Current Photo</label>
		<label for="descOrgLeader"><b>Name(s)of Leader:</b></label>
		<textarea name="description" cols="50" rows="1" id="description"></textarea>
      </p>
      <p>
        <label for="AddOrgBannerPhoto"><b>Add or Change Your Current Banner:</b></label>
        <br />
        <input name="AddOrgBannerPhoto" type="file" id="AddOrgBannerPhoto" />
        <input name="RemoveOrgBannerPhoto" type="checkbox" id="RemoveOrgBannerPhoto" value="RemoveOrgBannerPhoto">
        <label for="RemoveOrgBannerPhoto">Remove Current Photo</label>
      </p>
	  <!--<p>
        <label for="AddOrgSupportVideo"><b>Fun Video Requesting Support:</b></label>
        <br />
        <input name="AddOrgSupportVideo" type="file" id="AddOrgSupportVideo" />
        <input name="RemoveOrgSupportVideo" type="checkbox" id="RemoveOrgSupportVideo" value="RemoveOrgSupportVideo">
        <label for="RemoveOrgSupportVideo">Remove Current Video</label>-->
      </p>
          <input name="group" type="hidden" value="<?php echo $group; ?>">
          <input name="submit" type="submit" value="Save and continue">
          
        </form>
        </div>
    <!--end setupLeft-->
    
   <!-- <div class="setupRightPhoto">
      <img src="../images/placeholders/placeholder-404x220.jpg" width="404" height="220" alt="Placeholder"></div>
    <!--end setupRight--> 
 
  </div>
      <!--end contentMain858-->
      <?php include 'footer.php' ; ?>
    </div>
<!--end container-->

</body>
</html>

<?php
   ob_end_flush();
?>