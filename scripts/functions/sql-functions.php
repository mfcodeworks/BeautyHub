<?php
// Create a new SQL Connection (Don't forget to close in function!)
function sqlConnect()
{
    //Connect to DB
    $conn = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB);
    //Alert if connection failed
    if (!$conn) die(alert("Database Connection Failed"));
    return $conn;
};
// Get Max ID for Specified SQL Table
function getMaxId($table)
{
    //Connect to DB
    $conn = sqlConnect();
    //Set ID 0
    $id=0;
    //Get ID Statement
    $sql=("SELECT MAX(ID) FROM $table;");
    $result = mysqli_query($conn,$sql);
    if(mysqli_num_rows($result)>0) while($row = mysqli_fetch_assoc($result)) $id = $row["MAX(ID)"];
    mysqli_close($conn);
    //Whether or not result was returned, ID +1
    return $id + 1;
};
//Select all in column
function selectAll($column,$table,$id=NULL,$name=NULL)
{
    $conn = sqlConnect();
    if(isset($name)) $sql = "SELECT DISTINCT $column FROM $table WHERE $id=\"$name\" ORDER BY $column ASC;";
    else $sql = "SELECT DISTINCT $column FROM $table;";
    $result = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($result)){
        $data[] = $row["$column"];
    }
    mysqli_close($conn);
    return $data;
};
//Check if specified data exists in column from given table
function sqlExists($data,$column,$table,$user=null)
{
    //Connect to DB
    $conn = sqlConnect();
    if(isset($user))
        //Select count of columns that equal data to be checked with username
        $sql="SELECT count(id) FROM $table WHERE $column = \"$data\" AND username = \"$user\";";
    else
        //Select count of columns that equal data to be checked without username
        $sql="SELECT count(id) FROM $table WHERE $column = '$data';";
    $result = mysqli_query($conn,$sql);
    //Check if column exists with data given
    if($result==true)
    {
        while($row = mysqli_fetch_assoc($result))
        {
            if($row["count(id)"] > 0) {
                mysqli_close($conn);
                return true;
            }
        }
    }
    mysqli_close($conn);
    return false;
};
//Add new info to account
function addNewInfo($data,$column,$table)
{
    //Get user
    $user = $_SESSION['user']->getUsername();
    //Connect to DB
    $conn = sqlConnect();
    //SQL check if exists
    $sql = "UPDATE $table SET $column='$data' WHERE username='$user';";
    if(mysqli_query($conn,$sql)) {
        mysqli_close($conn);
        return true;
    }
    mysqli_close($conn);
    return false;
};
//Add data to column
function expandColumn($id,$data,$column,$table) {
    //Connect to DB
    $conn = sqlConnect();
    $sql = "SELECT $column FROM $table WHERE ID = $id;";
    echo "GET SQL $sql\n\n";
    $result = mysqli_query($conn,$sql);
    if($result) {
        while($row = mysqli_fetch_assoc($result))
            $col = $row[$column];
    }
    else return false;

    //If data exists and contains a comma
    if(isset($col) && strpos(rtrim($col,','),',') != false) $colArray = explode(',',rtrim($col,","));

    //Explode data array
    if(strpos(rtrim($data,','),',') != false) $dataArray = explode(',',rtrim($data,","));

    //If column data already exists and it's for wishlist do check
    if(isset($colArray) && $column == "wishlist") {
        if(!checkWishlist($dataArray,$colArray)) {
            mysqli_close($conn);
            return false;
        }
    }

    //If not for wishlist, check if data is in DB already
    else if(isset($colArray) && in_array($dataArray[0],$colArray)) {
        mysqli_close($conn);
        echo "$dataArray[0] already favourite\n";
        return false;
    }

    //New column = old column + new data
    $newData = $col . $data;
    $sql = "UPDATE $table SET $column = '$newData' WHERE ID = $id;";
    if(mysqli_query($conn,$sql)) {
        mysqli_close($conn);
        return true;
    }
    mysqli_close($conn);
    return false;
};
?>
