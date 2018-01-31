<?php
    require_once "functions.php";
    extract($_POST);

    //Set message
    $message = "<html>
                    <body>
                        Name: $firstname $lastname<br>
                        Email: <a href='mailto:$email'>$email</a><br><br>

                        Message:<br>
                        <p>$message</p><br><br>

                        <p><em>Sent from BeautyHub Contact Page Form</em></p>
                    </body>
                </html>";

    //Mail message
    if(mailMessage($message,$subject)) echo "true";
?>