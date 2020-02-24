<?php
class DB_Functions{
	private $conn;
	
	//constructor
	function __construct()
	{
		require_once 'db_connect.php';
		$db = new DB_Connect();
		$this -> conn = $db->connect();
	}
	
	//destructor
	function __destruct()
	{
		
	}
	//store new user
	public function storeUser($name, $email,$password)
	{
		$uuid = uniqid('', true);
		$hash = $this->hashSSHA($password);
		$encrypt_password = $hash["encrypted"]; // encrypted password
		$salt = $hash["salt"];
		$stmt = $this -> conn -> prepare("INSERT INTO users(unique_id,name,email,encrypted_password, salt, created_at) VALUES (?,?,?,?,?,NOW())");
		$stmt -> bind_param("sssss",$uuid,$name,$email,$encrypt_password,$salt);
		$result = $stmt->execute();
		$stmt->close();
		
		//check for success store
		if($result)
		{
			$stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
			$stmt->bind_param("s",$email);
			$stmt->execute();
			$user = $stmt->get_result()->fetch_assoc();
			$stmt->close();

			return $user;
		}
		else{
			return false;
		}

	}

	//get user by email and password
	public function getUser($email,$password)
	{
		$stmt = $this->conn->prepare("SELECT * FROM users WHERE email=?");
		$stmt->bind_param("s",$email);

		if($stmt->execute())
		{
			$user = $stmt->get_result()->fetch_assoc();
			$stmt->close();

			//verifying user password
			$salt = $user['salt'];
			$encrypt_password = $user['encrypted_password'];
			$hash = $this->checkhashSSHA($salt,$password);
			//check for password
			if($encrypt_password == $hash)
				return $user;
		}
		else{
			return NULL;
		}
	}
	
	//check if user exist or not
	public function isUserExisted($email)
	{
		$stmt = $this->conn->prepare("SELECT email from users WHERE email = ?");
		$stmt->bind_param("s",$email);
		$stmt->execute();
		$stmt->store_result();
		
		if($stmt->num_rows > 0)
		{
			$stmt->close();
			return false;
		}
	}

	//encrypte password
	public function hashSSHA($password)
	{
		$salt = sha1(rand());
		$salt = substr($salt,0,10);
		$encrypt = base64_decode(sha1($password.$salt,true).$salt);
		$hash = array("salt"=>$salt,"encrypted"=>$encrypt);

		return $hash;

	}

	//decrypte password
	public function checkhashSSHA($salt,$password)
	{
		$hash = base64_decode(sha1($password.$salt, true).$salt);
		return $hash;
	}



}

?>