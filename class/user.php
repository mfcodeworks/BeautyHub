<?php
    if(!defined("ABSPATH")) define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );
    require_once ABSPATH . "scripts/functions.php";

    class user {
        private $id;
        private $username;
        private $email;

        //Login constructor
        public function __construct($username, $password, $email=NULL) {
            if(isset($email)) $this->registerUser($username, $password, $email);
            else $this->checkLogin($username, $password);
        }

        //Destroy
        public function __destroy() {
            if(isset($conn)) mysqli_close($conn);
        }

        //{ get; } functions
        public function getID() { return $this->id; }
        public function getUsername() { return $this->username; }
        public function getEmail() { return $this->email; }

        //Check user login
        private function checkLogin($username, $password) {
            //Connect to DB
            $conn = sqlConnect();

            //Get password hash
            $password = $this->hashPassword($password);

            //Select count of columns that equal data to be checked
            $sql = "SELECT count(ID) 
                    FROM users 
                    WHERE pass = '$password' 
                    AND username = '$username';";

            try {
                $result = mysqli_query($conn,$sql);
            }
            catch(Exception $exc) {
                throw $exc;
            }

            //Check if User/Password combo exists
            if($result)
            {
                $row = mysqli_fetch_assoc($result);
                if($row["count(ID)"] > 0) {
                    //If combo exists get and save user info
                    $sql = "SELECT id,username,email 
                            FROM users 
                            WHERE username = '$username';";
                    $result = mysqli_query($conn,$sql);
                    $row = mysqli_fetch_assoc($result);
                    $this->id = $row['id'];
                    $this->username = $username;
                    $this->email = $row['email'];
                    $this->login();
                }
                else {
                    throw new Exception("User doesn't exist.");
                }
            }

            else {
                throw new Exception("User doesn't exist.");
            }

            //Close DB connection
            mysqli_close($conn);
        }

        //Register new user
        private function registerUser($username, $password, $email) {
            if(!isset($username) || !isset($password) || !isset($email) || $username = "" || $password = "" || $email = "")
                throw new Exception("Register requires Username, password and email.");

            //Connect to DB
            $conn = sqlConnect();

            //Check user doesn't exist
            if($this->userExists($username,$password)) 
                throw new Exception("User already exists");

            //Get new unique ID to give user
            $id = getMaxId('users');

            //One way salted hash for password
            $password = $this->hashPassword($password);

            //Build SQL statement
            $sql = "INSERT INTO users(id,username,pass,email) 
                    VALUES($id,'$username','$password','$email');";

            //Insert post data into DB
            try {
                mysqli_query($conn,$sql);
                $this->id = $id;
                $this->username = $username;
                $this->email = $email;
                $this->login();
            }

            //Catch and throw exception
            catch(Exception $exc) {
                throw $exc;
            }

            //Close DB connection
            mysqli_close($conn);
        }

        //Check user exists
        private function userExists($username,$password)
        {
            //Connect to DB
            $conn = sqlConnect();

            //Hash password
            $password = $this->hashPassword($password);

            $sql = "SELECT count(ID)
                    FROM users
                    WHERE username = '$username'
                    AND pass = '$password';
                    ";
            $result = mysqli_query($conn,$sql);
            while( $row = mysqli_fetch_assoc($result) ) {
                if($row['count(ID)'] > 0) return true; 
            }
            return false;
        }

        //Update users password
        public function changePassword($old,$new)
        {
            //Connect to DB
            $conn = sqlConnect();

            //Hash password
            $old = $this->hashPassword($old);
            $new = $this->hashPassword($new);

            //Get old password
            $sql = "SELECT pass
                    FROM users
                    WHERE ID=".$this->id.";";

            $result = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($result)) {
                $compare = $row['pass'];
            }

            //Check password match
            if($old == $compare) {

                //Build SQL statement
                $sql = "UPDATE users 
                        SET pass='$new' 
                        WHERE ID=".$this->id.";";

                try {
                    mysqli_query($conn,$sql);
                }
                catch(Exception $exc) {
                    throw $exc;
                    return false;
                }

                //Close DB connection
                mysqli_close($conn);
                return true;
            }
            
            //If no password match, throw exception
            else {
                throw new Exception("Password incorrect");
            }
        }

        //Save user and login
        private function login() {
            $_SESSION['user'] = $this;
        }

        //Hash password and return hash
        private function hashPassword($password) {
            //Create password hash
            $salt = sha1(md5($password));
            $hashPassword = md5($password.$salt);
            return $hashPassword;
        }

    }
?>
