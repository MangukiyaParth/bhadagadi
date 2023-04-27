<?php

function add_banner()
{
	global $outputjson, $gh, $db, $const,$tz_name, $tz_offset, $loggedin_user;
	$outputjson['status'] = 0;

	$id = $gh->read("id");
	$target_dir = "upload/images/banners/";
	$target_file = $target_dir . basename($_FILES["file"]["name"]);
    $dateNow = date('Y-m-d H:i:s');

	if(isset($_FILES['file'])){
		$message = "";
		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		$check = getimagesize($_FILES["file"]["tmp_name"]);
		if($check !== false) {
			$message = "File is an image - " . $check["mime"] . ".";
			$uploadOk = 1;
		} else {
			$message = "File is not an image.";
			$uploadOk = 0;
		}

		// Check if file already exists
		if (file_exists($target_file)) {
			$target_file = str_replace(".".$imageFileType, "", $target_file,).time().".".$imageFileType;
		}

		// Check file size
		if ($_FILES["file"]["size"] > 500000) {
			$message = "Sorry, your file is too large.";
			$uploadOk = 0;
		}

		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
			$message = "Sorry, only JPG, JPEG & PNG files are allowed.";
			$uploadOk = 0;
		}

		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			$outputjson['message'] = $message;
		} else {
			if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
				if($id > 0)
				{
					$banners_query = "SELECT * FROM tbl_banners WHERE id = $id";
    				$banners_rows = $db->execute($banners_query);
					unlink($banners_rows[0]['file']);
					$result = $db->update("tbl_banners", array("file"=> $target_file, "created_by"=>$loggedin_user['id']), array("id"=>$id));
					$outputjson['oldimg'] = $banners_rows[0]['file'];
					$outputjson['message'] = 'data updated successfully.';
				}
				else{
					$result = $db->insert("tbl_banners", array("file"=> $target_file, "created_by"=>$loggedin_user['id']));
					$outputjson['message'] = 'data inserted successfully.';
				}
				
				$outputjson['result'] = $result;
				$outputjson['status'] = 1;
			} else {
				$outputjson['message'] = "Sorry, there was an error uploading your file.";
			}
		}
	}
	else {
		$outputjson['message'] = "Please select image!";
	}

}

?>