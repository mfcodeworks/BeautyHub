<?php
//Check valid image for uploading
function checkUploadImage($name,$tmpName)
{
    //Check count files
    if(!isset($tmpName)) return false;
    else {
        //Upload check var
        $uploadCheck = true;
        //Allowed var
        $allowedFileTypes = array("jpg","jpeg","png","gif","mp4","m4a","mov");
        //Current target file
        $baseName = basename($name);
        //Get file type
        $fileType = pathinfo($baseName,PATHINFO_EXTENSION);

        // Check if image file is an actual image or fake image
        $check = getimagesize($tmpName);
        if($check) {
            //echo "File is an image - " . $check["mime"] . ".\n\n";
            $uploadCheck = true;
        }
        else {
            echo "File is not an image.";
            $uploadCheck = false;
        }
        // Allow certain file formats
        $fileType = strtolower($fileType);
        if(!in_array($fileType,$allowedFileTypes)) {
            echo "Only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadCheck = false;
        }
    }
    return $uploadCheck;
};
//Give file unique name
function getUniqueName($target)
{
    if(isset($target) && $target != '') {
        //Get file type
        $fileType = pathinfo($target,PATHINFO_EXTENSION);
        //Unique file name
        $newFileName = date('Y-m-d-H-i-s') . '_' . uniqid() . '.' . $fileType;
        return $newFileName;
    }
};
function getFileTarget($file,$targetDir = NULL)
{
    //Get file name
    $targetNewName = getUniqueName($file);

    //Set target
    if(isset($targetDir) && $targetDir != "" && $targetDir != " ") {

        //Check user directory exists
        if( !file_exists(IMAGE_DIR . $targetDir) ) {
            mkdir(IMAGE_DIR . $targetDir, 0775, true);
        }

        $target = IMAGE_DIR . $targetDir . $targetNewName;
    }

    else $target = IMAGE_DIR . $targetNewName;

    //Check if file exists
    if(file_exists($target)) $target = getFileTarget($file,$userPhoto);

    return $target;
};
//Try to upload/check upload for file
function uploadFile($source,$target)
{
    if (move_uploaded_file($source, $target)) return true;
    else return false;
};
?>
