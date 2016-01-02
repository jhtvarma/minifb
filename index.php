<html>
	<head>
		<title>OurChoice</title>																				<!--Title of the Portal-->
		<script type="text/javascript">
			function check_password(){																			//Function to check constraints of password
				var pswd = document.getElementById('pswd').value;
				if(pswd.length<8){
					document.getElementById('error_pass').innerHTML= "Password must be minimum 8 characters";		//Displaying error message in hidden item
					return false;
				}
				else{
					document.getElementById('error_pass').innerHTML="";
					return true;
				}
			}
			function check_username(){
				var uname = document.getElementById('uname').value;
				if(uname.length<4){
					document.getElementById('error_uname').innerHTML= "Username mst be greater than 4 characters";
					return false;
				}
				else{
					document.getElementById('error_uname').innerHTML="";
					return true;
				}
			}
		</script>
	</head>
	<body>
		<h1 align="center">Our Choice</h1>
		<?php
			session_start();
			if(isset($_POST['login'])){														//If login action is performed
				$servername = "localhost";
				$username = "root";
				$password = "password";
				$sucess = 0;
				$uname = $_POST['uname'];													//Get username and password to check in database
				$psswd = $_POST['pswd'];
				$encpsswd = md5($psswd);
				
				$conn = mysqli_connect($servername,$username,$password);
				if(!$conn){
					die("Connection failed:".mysqli_connect_error());
					header('Location:error.php?msg='.mysqli_connect_error().'');			//If any error arises,go to error page 
				}
				mysqli_select_db($conn,"ambox");											//Connecting to database
				
				$sql = "select user_id from users where username = '".$uname."' and password = '".$encpsswd."'";
				$result=mysqli_query($conn,$sql);
				$row = mysqli_fetch_assoc($result);
				$_SESSION['message'] = $row["user_id"];										//****Store id in session for remaining pages
				if(mysqli_num_rows($result)==1){											//If no of rows = 1 -> valid user,redirect to users page
					header("location: user.php");
					$sucess = 1;
				}
				else{																		//IF no of rows !=1 -> Invalid Credentials ,stay in same page
					if($sucess == 0){
						echo "<script>alert('No User Found!!');</script>";
					}

					$sucess = 0;
				}
				
			}
		?>
		<form name=login method="POST" action="index.php" onsubmit="return (check_password() && check_username())">			
			<fieldset float=left align=center id=field>
				<legend><b>Login</b></legend>												<!-- Fieldset Title  -->
				<br/>
				<br/>
				<input type=text name=uname id=uname size=20 placeholder="User name" required="required" onblur="check_username()	"><br/>
				<span type=hidden id=error_uname style="color:red"></span><br/><br/>
				<input type=password name=pswd id=pswd size=20 placeholder="Password" required="required" onblur=check_password()><br/>
				<span type=hidden id=error_pass style="color:red"></span><br/><br/>				<!-- Hidden element to display only error messages -->
				<input type=submit value=Login name=login id=login align=center><br/>
				<br/>
				<a href="register.php">Register</a><br/><br/>
				
			</fieldset>
		</form>

	</body>
</html>