<html>
	<head>
		<title>OurChoice</title>																							<!--Title of the Portal-->
		<script type="text/javascript">
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
			
			function validate_password(){																			//To check password and confirm password
				var pswd = document.getElementById('pswd').value;
				var cpswd = document.getElementById('cpswd').value;
				if(pswd!=cpswd){
					document.getElementById('error_cpass').innerHTML="These passwords don't match. Try again?";
					return false;
				}
				else{
					document.getElementById('error_cpass').innerHTML="";
					return true;
				}
			}
			function check_password(){																				//To check password constraints
				var pswd = document.getElementById('pswd').value;
				if(pswd.length<8){
					document.getElementById('error_pass').innerHTML="Try one with at least 8 characters.";
					return false;
				}/*																							//Conditions for Capital,number and special symbol
				else if(!pswd.match(/[A-Z]/g)){
					document.getElementById('error_pass').innerHTML="Try one with at least 1 capital character.";
					return false;
				}
				else if(!pswd.match(/[0-9]/g)){
					document.getElementById('error_pass').innerHTML="Try one with at least 1 digit";
					return false;
				}
				else if(!pswd.match(/[!@#$%^&*()_+]/g)){
					document.getElementById('error_pass').innerHTML="Try one with at least 1 special symbols";
					return false;
				}*/
				else{
					document.getElementById('error_pass').innerHTML="";
					return true;
				}
			}
		</script>
	</head>
	<body>
		<h1 align="center">Our Choice</h1>
		<form name=login method="POST" action="register.php" onsubmit="return (validate_password() && check_password() && check_username())">
			<?php
				if(isset($_POST['insert'])){													//If Insert action has performed
					$servername = "localhost";
					$username = "root";															//Servername,Username,Password
					$password = "password";
					
					$uname = $_POST['uname'];
					$psswd = $_POST['pswd'];
					$encpsswd = md5($psswd);													//Encrypted password
					$sucess=0;
					$conn = mysqli_connect($servername,$username,$password);
					if(!$conn){
						die("Connection failed:".mysqli_connect_error());
						header('Location:error.php?msg='.mysqli_connect_error().'');			//If any error arises,go to error page 
					}
					mysqli_select_db($conn,"ambox");
					$sql = "select * from users where username = '".$uname."';";				//Checking for same credentials
					if(mysqli_num_rows(mysqli_query($conn,$sql))>=1){
						echo "<script>alert('Sorry!User exit with these credentials');</script>";
						$sucess = 2;
					}
					if($sucess !=2 ){															//If no user found with entered credentials then proceed
						$sql = "insert into users (user_id,username,password) values (NULL,'".$uname."','".$encpsswd."')";
						if(!mysqli_query($conn,$sql)){											//Insert id(AUTO_INCREMENT,primary key),username,encrypted password into users table
							header('Location:error.php?msg='.mysqli_error($conn).'');
						}
						$sucess = 1;														//Make success =1 to give successful message
						mysqli_close($conn);
					}
				}
			?>
			<fieldset float=left align=center id=field>
				<legend><b>Register</b></legend>
				<br/>
				<br/>
				<input type=text name=uname id=uname size=30 placeholder="User name" required="required" onblur="check_username()"><br/>
				<span type=hidden id=error_uname style="color:red"></span><br/><br/>
				<input type=password name=pswd id=pswd size=30 placeholder="Password" required="required" onblur="check_password()"><br/>
				<span type=hidden id=error_pass style="color:red"></span><br/><br/>
				<input type=password name=cpswd id=cpswd size=30 placeholder="Confirm Password" required="required" onblur="validate_password()"><br/>
				<span type=hidden id=error_cpass style="color:red"></span><br/><br/>
				<input type=submit name=insert id=insert value=Register align=center><br/>
			
				<br/>
				<a href="index.php">Login</a><br/><br/>
				
			</fieldset>
			<?php 
				if(isset($_POST['insert'])){
					if($sucess==1){																//If everything is fine give successful message
						echo "<p align='center'>Your account is created successfully!</p>";
					}
				}
			?>
		</form>
		
	</body>
</html>