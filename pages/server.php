<?php 
	
	session_start();

	// variable declaration
	$username = "";
	$email    = "";
	$errors = array(); 
	$_SESSION['success'] = "";

	// connect to database
	$db = mysqli_connect('webdb1.ipax.at',"k003196_30","xGWUvM5N3Bz3",'k003196_30_logdrive');

	// REGISTER USER
	if (isset($_POST['reg_user'])) {
		
		// receive all input values from the form
		$username = mysqli_real_escape_string($db, $_POST['username']);
		$email = mysqli_real_escape_string($db, $_POST['email']);
		$password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
		$password_2 = mysqli_real_escape_string($db, $_POST['password_2']);
	
		// form validation: ensure that the form is correctly filled
		if (empty($username)) { array_push($errors, "Username is required"); }
		if (empty($email)) { array_push($errors, "Email is required"); }
		if (empty($password_1)) { array_push($errors, "Password is required"); }

		if ($password_1 != $password_2) {
			array_push($errors, "The two passwords do not match");
		}
		

		// register user if there are no errors in the form
		if (count($errors) == 0) {
			$password = password_hash($password_1,PASSWORD_DEFAULT);//encrypt the password before saving in the database
		
			$query = "INSERT INTO user (username, email, Password) 
					  VALUES('$username', '$email', '$password')";

			mysqli_query($db, $query);
		

			$_SESSION['username'] = $username;
			$_SESSION['success'] = "You are now logged in";
			header('location: dashboard.html');
		}

	}

	// LOGIN USER
	if (isset($_POST['login_user'])) {
		$username = mysqli_real_escape_string($db, $_POST['username']);
		$password = mysqli_real_escape_string($db, $_POST['password']);

		if (empty($username)) {
			array_push($errors, "Username is required");
		}
		if (empty($password)) {
			array_push($errors, "Password is required");
		}

		if (count($errors) == 0) {
			
			$query = "SELECT * FROM user WHERE username='$username'";
		
			if ($result = mysqli_query($db, $query)) {

				
				if (mysqli_num_rows($result) == 1) {
								/* fetch associative array */
					while ($row = mysqli_fetch_row($result)) {
						$password_server =  $row[3];

						if(password_verify($password, $password_server ))
						{
						
							$_SESSION['username'] = $username;
							$_SESSION['success'] = "You are now logged in";
							header('location: dashboard.html');
						}
						else
						{
							array_push($errors, "Wrong username/password combination");
						}
					}
	
				}else {
					array_push($errors, "Wrong username/password combination");
				}

				/* free result set */
				mysqli_free_result($result);
			}
		
	
	
		}
	}

?>