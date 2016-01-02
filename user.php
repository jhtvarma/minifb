<html>
	<head>
		<title>OurChoice</title>
		<style>																			<!-- CSS Part -->
		#heading{
			display:inline;
			padding-left:2%;
		}
		#frds{
			float :right;
			padding-right:1%; 
		}
		.right{
			float :right;
			padding-right:1%; 
		}
		#updates{
			width:550px;
			float:left;
			padding-left:2%; 
			padding-right:1%; 
			overflow-y: auto;
			height: 400px;
		}
		textarea {
			resize: none;
		}
		form{
			padding-left:50%;
		}
		</style>
		<script type="text/javascript">
			function redirect_friends(){												//function to redirect to friends page 
				window.location='friends.php';
			}
			function redirect_logout(){													//function to redirect to logout page 
				window.location='logout.php';
			}
			function check_post(){
				var len = document.getElementById('msg').value;
				if(len==0){
					alert('Please enter post to update');
					return false;
				}
				else{
					return true;
				}
			}
		</script>
	</head>
	<body>
		<?php 
			session_start();
			if (isset($_SESSION['message'])) {											//Get the user_id from session
				$id = $_SESSION['message'];
			}
			else{																		//Trying to access directly without login ...redirect to login page
				echo "<script>alert('Please login with valid credentials!!');</script>";
				header('Location:index.php');
			}
		?>

		<?php
			$servername = "localhost";
			$username = "root";
			$password = "password";
			$conn = mysqli_connect($servername,$username,$password);
			if(!$conn){
				die("Connection failed:".mysqli_connect_error());
				header('Location:error.php?msg='.mysqli_connect_error().'');			//If any error arises,go to error page
			}
			mysqli_select_db($conn,"ambox");
			if(isset($_POST['insert'])){												//Insert post_id(Primary key),user post,user_id,time into updates (posts) table
				$sql = "insert into posts values (NULL,'".$_POST['msg']."',".$id.",".time().");";
				if(!mysqli_query($conn,$sql)){
					header('Location:error.php?msg='.mysqli_error($conn).'');			//If any error arises,go to error page 
				}
			}
			$sql = "select username from users where user_id = '".$id."';";
			$result = mysqli_query($conn,$sql);
			$row = mysqli_fetch_assoc($result);											//Get Username to print in page
			
		?>
		<h1 align="center" style="font-size:35px">OurChoice</h1>
		<div><a href="user.php"><b style="font-size:30px" id="heading" ><?php echo $row['username']?></b></a>
		<input type=button id="heading" class="right" value="Logout" style="width: 80px; height: 25px" onclick="redirect_logout()"/>       <!--Buttons to Friends and logout pages-->
		<input type=button id="heading" class="right" value="Friends" style="width: 80px; height: 25px" onclick="redirect_friends()"/></div><br/>
		<h3 style="padding-left: 2%">Updates</h3>
		<div id="updates">
			<?php
				$frdsid = array();
				$i = 0;
				$sql = "select one,two from friends where ((one = ".$id." or two = ".$id.") and status = '1');";
				
				$result = mysqli_query($conn,$sql);
				if($result!=false){
				if(mysqli_num_rows($result)>0){
					while($row = mysqli_fetch_assoc($result)){				
						if($row['one']!=$id){
							array_push($frdsid,$row['one']);
						}
						if($row['two']!=$id){
							array_push($frdsid,$row['two']);
						}
					}
				}																			//Get all friends id from friends table and store it in frdsid array;			
				}
				//print_r($frdsid);
				$sql = "select username,post from users u,posts p where p.user_id_fk = u.user_id and user_id_fk in (".$id."";	//select posts from user friends
				while ($i<count($frdsid)){	
					$sql .= ",".$frdsid[$i]."";												//Including friends id
					$i = $i +1;	
				}
				$sql .= ") order by created DESC limit 20;";								//Get posts based on time and only 20 results
				$result = mysqli_query($conn,$sql);
				if(mysqli_num_rows($result)>0){
					while($row = mysqli_fetch_assoc($result)){
						echo "<h4>".$row['username']."</h4><p>".$row['post']."</p>";		//Dislaying the post
					}
				}
				mysqli_close($conn);														//Closing the connection
			?>
		</div>
		
		<form name="new_post" method="POST"  action="user.php" onsubmit="return check_post()">									<!--Form with textarea of fixed size to post update-->
			Post a new Update:<br/><br/>
			<textarea id="msg" name="msg" rows="4" cols="50" maxlength="200" placeholder="Type your status update here...(max 200 chars)"></textarea><br/><br/>
			<input type="submit" value="Post" name="insert" id="insert">
		</form>
	</body>
</html>