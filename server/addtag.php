<?php
include 'dbh.php';
ini_set('display_errors',1); error_reporting(E_ALL);
if(isset($_POST['tag_name'])) {
	$tagname = trim(strtolower($_POST['tag_name']));
	$channel_id = $_POST['channel_id'];
	
	$stmt = mysqli_stmt_init($conn);
	
	if($tagname != "") {
    	$check = "SELECT * FROM tags WHERE tag_name=?";
    	if(!mysqli_stmt_prepare($stmt, $check)) {
    		header("Location: /rate/ouplonk.php?error=103");
    	} else {
    		mysqli_stmt_bind_param($stmt, "s", $tagname);
    		mysqli_stmt_execute($stmt);
    		$result = $stmt->get_result();
    		if (mysqli_num_rows($result) == 0) { 
    			$newtag = "INSERT INTO tags_requests (tag_name, tag_channel) VALUES (?, ?)";
    			if(!mysqli_stmt_prepare($stmt, $newtag)) {
    				header("Location: /rate/ouplonk.php?error=103");
    			} else {
    				mysqli_stmt_bind_param($stmt, "ss", $tagname, $channel_id);
    				if(!$stmt->execute()) {
    					trigger_error("there was an error....".$conn->error, E_USER_WARNING);
    				}
    			}
    		}
    		else {
    		    $checkconn = "SELECT * FROM `tags_connections` INNER JOIN `tags` ON `tags_connections`.`tag_id`=`tags`.`tag_id` WHERE tag_name=? AND tag_channels=?";
    			if(!mysqli_stmt_prepare($stmt, $checkconn)) {
    				echo "error";
    			} else {
    				mysqli_stmt_bind_param($stmt, "ss", $tagname, $channel_id);
    				mysqli_stmt_execute($stmt);
    				$result2 = $stmt->get_result();
    				if (mysqli_num_rows($result2) == 0) {
    					$row = $result->fetch_assoc();
    					$addtag = "INSERT INTO tags_connections (tag_id, tag_channels) VALUES (?, ?)";
    					if(!mysqli_stmt_prepare($stmt, $addtag)) {
    						echo "can't prepare insert";
    					} else {
    						mysqli_stmt_bind_param($stmt, "is", $row['tag_id'], $channel_id);
    						if(!$stmt->execute()) {
    							trigger_error("there was an error....".$conn->error, E_USER_WARNING);
    						}
    					}
    				}
    			}
    		}
    	}
	}
}