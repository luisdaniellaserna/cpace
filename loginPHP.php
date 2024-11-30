<?php
	session_start();
	$username = $_POST['username'];
	$password = $_POST['password'];

	$conn = new mysqli('localhost','root','','cpa');
	if($conn->connect_error){
		die("Failed to connect : ".$con->connect_error);
	}else{
		$stmt = $conn->prepare("select * from accounts where Account_ID = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$stmt_result = $stmt->get_result();
		if($stmt_result->num_rows > 0) {
			$data = $stmt_result->fetch_assoc();
			if($data['password'] == $password) {
                if($data["Categories"]=="teacher"){
                    $_SESSION['username'] = $username;
                    echo "<h2>Login Successfully</h2>";
                    header("Location: teacherHP.php");
                }
                elseif($data["Categories"]=="student"){
                    $_SESSION['username'] = $username;
                    echo "<h2>Login Successfully</h2>";
                    header("Location: hp.php");
                }
				
		}else{
			echo "<h2>Invalid user or password</h2>";
			header("Location: index.php");
		}
	} else {
		echo "<h2>Invalid username or password</h2>";
		header("Location: index.php");
		}
	}
?>