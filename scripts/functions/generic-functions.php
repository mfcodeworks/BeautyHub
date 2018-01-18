<?php
// Set page URL
 function headerLocation($location)
{
    header("Location: ". $location);
    exit;
};
// Print any errors as Javascript alert
function alert($message)
{
    //CHANGED loadJS("alert('$message');");
    loadJS("echoAlert($message);");
};
// Load any JS actions
function loadJS($js)
{
    echo "<script type='text/javascript'>$js</script>";
};
// Print any errors as Javascript console log
function logConsole($data)
{
    loadJS("console.log('$data');");
};
// Mail message to administrative email
function mailMessage($message, $subject = NULL, $to = ADMIN_EMAIL)
{
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=ISO-8859-1' . "\r\n";
    $headers .= "From: " . WEBMASTER_EMAIL . "\r\n".
                'X-Mailer: PHP/' . phpversion();

    if(mail($to,$subject,$message,$headers))
        return true;
    else
        error_log("Couldn't send Email!\nTo: $to\nSubject: $subject\nMessage: $message");
};
// Return todays date as yyyy-mm-dd
function getDateNow($tz = NULL)
{
    //Get Date
    if(isset($tz)) date_default_timezone_set($tz);
    $date = date('Y-m-d', time());
    return $date;
};
// Format date for standardization
function formatDate($date, $tz = NULL)
{
    //Format date
    if(isset($tz)) date_default_timezone_set($tz);
    $date = date('Y-m-d', $date);
    return $date;
};
//Remove duplicate/null values from one-dimensional arrays
function uniqueArray($array) {
    //Create new array without gaps or duplicates
    $array2 = array();
    foreach ($array as $row) {
        if ($row !== null && !in_array($row,$array2))
        $array2[] = $row;
    }
    return $array2;
};
?>
