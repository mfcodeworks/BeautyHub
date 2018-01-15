<?php
//Check valid image for uploading
function checkUploadImage($name,$tmpName)
{
    //Check count files
    if(!isset($tmpName)) return false;
    else {
        //Upload check var
        $uploadCheck = 1;
        //Current target file
        $baseName = basename($name);
        //Get file type
        $fileType = pathinfo($baseName,PATHINFO_EXTENSION);
        // Check if image file is an actual image or fake image
        $check = getimagesize($tmpName);
        if($check != false) {
            echo "File is an image - " . $check["mime"] . ".\n\n";
            $uploadCheck = 1;
        }
        else {
            echo "File is not an image.";
            $uploadCheck = 0;
        }
        // Allow certain file formats
        if($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg" && $fileType != "gif" ) {
            echo "Only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadCheck = 0;
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
function getFileTarget($file)
{
    $targetNewName = getUniqueName($file);
    $target = IMAGE_DIR . $targetNewName;
    if(file_exists($target)) $target = getFileTarget($file);
    return $target;
};
//Try to upload/check upload for file
function uploadFile($source,$target)
{
    if (move_uploaded_file($source, $target)) return true;
    else return false;
};
?>
