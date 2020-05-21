<?php
/**
 * KindEditor PHP
 *
* This PHP program is a demonstration program, it is recommended not to use it directly in the actual project.
  * If you are sure to use this program directly, please confirm the relevant security settings carefully before use.
 *
 */

require_once 'JSON.php';

$php_path = dirname(__FILE__) . '/';
$php_url = dirname($_SERVER['PHP_SELF']) . '/';

//File save directory path
$save_path = $php_path . '../attached/';
//File save directory URL
$save_url = $php_url . '../attached/';
//Define file extensions that are allowed to upload
$ext_arr = array(
	'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
	'flash' => array('swf', 'flv'),
	'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
	'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'),
);
//Maximum file size
$max_size = 1000000;

$save_path = realpath($save_path) . '/';

//PHP upload failed
if (! empty ($_FILES ['imgFile'] ['error'])) {
    switch ($_FILES ['imgFile'] ['error']) {
    case '1':
        $error = 'Exceeded the size allowed by php.ini. ';
        break;
    case '2':
        $error = 'Exceeded the size allowed by the form. ';
        break;
    case '3':
        $error = 'Only part of the image was uploaded. ';
        break;
    case '4':
        $error = 'Please select an image. ';
        break;
    case '6':
        $error = 'Temporary directory not found. ';
        break;
    case '7':
        $error = 'Error writing file to hard disk. ';
        break;
    case '8':
        $error = 'File upload stopped by extension. ';
        break;
    case '999':
    default:
        $error = 'Unknown error. ';
    }
    alert ($error);
}

// When uploading files
if (empty ($_FILES) === false) {
    // Original file name
    $file_name = $_FILES ['imgFile'] ['name'];
    // Temporary file name on the server
    $tmp_name = $_FILES ['imgFile'] ['tmp_name'];
    //File size
    $file_size = $_FILES ['imgFile'] ['size'];
    // Check the file name
    if (! $file_name) {
        alert ("Please select a file.");
    }
    // Check the directory
    if (@is_dir ($save_path) === false) {
        alert ("The upload directory does not exist.");
    }
    // Check the directory write permission
    if (@is_writable ($save_path) === false) {
        alert ("The upload directory does not have write permission.");
    }
    // Check if it has been uploaded
    if (@is_uploaded_file ($tmp_name) === false) {
        alert ("Upload failed.");
    }
	//Check file size
	if ($file_size > $max_size) {
		alert("The upload file size exceeds the limit.");
	}
	//Check directory name
	$dir_name = empty($_GET['dir']) ? 'image' : trim($_GET['dir']);
	if (empty($ext_arr[$dir_name])) {
		alert("The directory name is incorrect.");
	}
	//Get file extension
	$temp_arr = explode(".", $file_name);
	$file_ext = array_pop($temp_arr);
	$file_ext = trim($file_ext);
	$file_ext = strtolower($file_ext);
	//Check extension
	if (in_array($file_ext, $ext_arr[$dir_name]) === false) {
		alert("Upload file extension is not allowed. \n Only allowed" . implode(",", $ext_arr[$dir_name]) . "format.");
	}
	//Create Folder
	if ($dir_name !== '') {
		$save_path .= $dir_name . "/";
		$save_url .= $dir_name . "/";
		if (!file_exists($save_path)) {
			mkdir($save_path);
		}
	}
	$ymd = date("Ymd");
	$save_path .= $ymd . "/";
	$save_url .= $ymd . "/";
	if (!file_exists($save_path)) {
		mkdir($save_path);
	}
	//New file name
	$new_file_name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . $file_ext;
	//Move file
	$file_path = $save_path . $new_file_name;
	if (move_uploaded_file($tmp_name, $file_path) === false) {
		alert("File upload failed.");
	}
	@chmod($file_path, 0644);
	$file_url = $save_url . $new_file_name;

	header('Content-type: text/html; charset=UTF-8');
	$json = new Services_JSON();
	echo $json->encode(array('error' => 0, 'url' => $file_url));
	exit;
}

function alert($msg) {
	header('Content-type: text/html; charset=UTF-8');
	$json = new Services_JSON();
	echo $json->encode(array('error' => 1, 'message' => $msg));
	exit;
}
