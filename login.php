<?php
require_once 'db_function.php';
$db = new DB_Functions();

//json response
$response = array("error"=>FALSE);
if(isset($_POST['email']) && isset($_POST['password']))
{
    //recieving the post
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = $db->getUser($email,$password);
    if($user != false){
        $response["error"] = FALSE;
        $response["uid"] = $user["unique_id"];
        $response["user"]["name"] = $user["name"];
        $response["user"]["email"] = $user["email"];
        $response["user"]["created_at"] = $user["created_at"];
        $response["user"]["updated_at"] = $user["updated_at"];
        echo json_encode($response);
    }
    else{
        $response["error"] = TRUE;
        $response["error_msg"] = "Login information are wrong. please try again";
        echo json_encode($response);
    }

}
else{
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameter (name,email,password) is missing ";
    echo json_encode($response);
}

?>