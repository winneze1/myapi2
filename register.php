<?php
require_once 'db_function.php';
$db = new DB_Functions();

//json response
$response = array("error"=>FALSE);
if(isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password']))
{
    //recieving the post
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if($db->isUserExisted($email))
    {
        $response["error"] = TRUE;
        $response["error_msg"] = "User already existed with ".$email;
        echo json_decode($response);
    }
    else{
        //craete new user
        $user = $db->storeUser($name,$email,$password);
        if($user){
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
            $response["error_msg"]  = "Unknow error occured in registration !";
            echo json_encode($response);
        }
    }

}
else{
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameter (name,email,password) is missing ";
    echo json_encode($response);
}

?>