<?php
    /**
     * Check for high user reports to report to admins
     */

    if(!defined("ABSPATH")) define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );
    require_once ABSPATH . "scripts/functions.php";

    //Connect to DB
    $conn = sqlConnect();

    //Get users with high reports where admin haven't been alerted
    $sql = "SELECT ID,username
            FROM users
            WHERE reports > 2
            AND user_reports_reported = 0;
            ";
    $result = mysqli_query($conn,$sql);

    //Create username/ID array
    while($row = mysqli_fetch_assoc($result)) {
        $users[] = array( 
            'id' => $row['ID'], 
            'username' => $row['username'] 
        );
    }

    //If users with high violations are found, email admin to investigate
    if(isset($users)) {
        $message = "
        <html>
            <body>
                <div style='text-align: center;'>
                    <h1>
                        Users reported 3+ times<br>
                        <strong>" . date("l d, F Y") . "</strong>
                    </h1>
                </div>
                <p>";
        foreach($users as $user) {
            $message .= $user['username'] . " ID: " . $user['id'] . "<br>";
        }
        $message .= "
                </p>
            </body>
        </html>";
        if(mailMessage($message, "<BeautyHub> User Violations " . date("l d, F Y") )) {
            error_log("User violation email sent - ".date("l d, F Y"));
        }
        else {
            error_log("Failed to send user violation email - ".date("l d, F Y"));
        }
    }

?>