<html>
	<head>
		<title>OurChoice</title>
		<style>
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
			width:400px;
			float:left;
			padding-left:1%; 
			padding-right:1%;
			overflow-y:auto;
			overflow-x:auto;
			height: 450px
		}
		</style>
		<script type="text/javascript">
			function redirect_friends(){
				window.location='friends.php';
			}
			function redirect_logout(){
				window.location='logout.php';
			}
			
		</script>
	</head>
	<body>
		<?php 																				//php for session (section-1)
			session_start();
			if (isset($_SESSION['message'])) {											//Get the user_id from session
				$id = $_SESSION['message'];
			}
			else{																		//Trying to access directly without login ...redirect to login page
				echo "<script>alert('Please login with valid credentials!!');</script>";
				header('Location:index.php');
			}
		?>

		<?php																		//php code to accept frd req and sending friend request (section-2)
			$servername = "localhost";
			$username = "root";																//Servername,username,password
			$password = "password";
			$conn = mysqli_connect($servername,$username,$password);
			if(!$conn){
				die("Connection failed:".mysqli_connect_error());
				header('Location:error.php?msg='.mysqli_connect_error().'');				//If any error arises,go to error page
			}
			mysqli_select_db($conn,"ambox");
			$avoid = array();																//An array to add friends id's and to avoid in ALL USERS section
			
			if(isset($_POST['u'])){				//u-user_id   r-requested user_id			//php code for modifying friend requests pending
				$u = intval($_POST['u']);
				$r = intval($_POST['r']);													//Get user id and requested user id
				$sql = "insert into friends values(".$u.",".$r.",'0');";					// 0 ->Friend RequestPending   1->Accepted 2->Same	
				if(!mysqli_query($conn,$sql)){
					echo "<script>alert('Friend request already sent')</script>";			//Alert when sending again
				}
				else{
					echo "<script>alert('Friend request sent!')</script>";					//Successful Notification 
				}
			}
			
			if(isset($_POST['aid'])){														//php code for accepting(Yes) and rejecting(No) friend requests
				if($_POST['accept']=='yes'){												//Accept Case
					$sql = "update friends set status='1' where one=".$_POST['aid']." and two=".$id." ; ";   //1 ->Accepted
					if(!mysqli_query($conn,$sql)){
						header('Location:error.php?msg='.mysqli_error($conn).'');			
					}
					array_push($avoid,$_POST['aid']);
				}
				else{																		//Reject Case
					$sql = "delete from friends where one=".$_POST['aid']." and two=".$id." and status='0' ";	//delete request ->delete '0' from database
					if(!mysqli_query($conn,$sql)){
						header('Location:error.php?msg='.mysqli_error($conn).'');			
					}
				}
			}
			
			$sql = "select username from users where user_id = '".$id."';";					//Get Username 
			$result = mysqli_query($conn,$sql);
			$row = mysqli_fetch_assoc($result);
		?>
		<h1 align="center" style="font-size:35px">OurChoice</h1>
		<a href="user.php"><b style="font-size:30px" id="heading"><?php echo $row['username']?></b></a>
		<input type=button id="heading" class="right" value="Logout" style="width: 80px; height: 25px" onclick="redirect_logout()"/>
		<input type=button id="heading" class="right" value="Friends" style="width: 80px; height: 25px" onclick="redirect_friends()"/><br/><br/>
		<div id="updates" align="center">
			<h3>Friend Requests</h3>												<!-- Friend Requests   -->
			<?php 																	//php code for friend request (section-3)
				$sql = "select user_id,username from users where user_id in (select one from friends where (two=".$id." and status='0'))";
				$result = mysqli_query($conn,$sql);							//Get Id's from friends table where two = id and status='0' (Pending)
				if($result!=false){											//If no friend request are there $result is false	
					if(mysqli_num_rows($result)>0){
						while($row = mysqli_fetch_assoc($result)){
							echo "<p id='heading'>".$row['username']."							
							<form id='heading' method='POST' action='friends.php'>							
								<input type='hidden' name='aid' value='".$row['user_id']."' /> 
								<input type='hidden' name='accept' value='yes' /> 
								<input type='submit' value='Yes'/>
							</form>
							<form id='heading' method='POST' action='friends.php'>
								<input type='hidden' name='aid' value='".$row['user_id']."' /> 
								<input type='hidden' name='accept' value='no' /> 
								<input type='submit' value='No'/>
							</form>
							</p>";											//Print username with YES or NO button (2 Forms for each with requested user id and yes/no values)
							array_push($avoid,$row['user_id']);				//push user_id to avoid array to avoid him all_users section
						}
					}
				}
			?>
		</div>
		<div id="updates" align="center">
			<h3>Friends</h3>														<!-- Friends -->
			<?php																	//php code for accepted friends (section-4)
				$frdsid = array();													//Store friends id's
				$i = 0;
				$sql = "select one,two from friends where ((one = ".$id." or two = ".$id.") and status = '1');";
				
				$result = mysqli_query($conn,$sql);									//Get all friends id's....1->Accepted
				if($result!=false){													
					if(mysqli_num_rows($result)>0){
						while($row = mysqli_fetch_assoc($result)){				
							if($row['one']!=$id){
								array_push($frdsid,$row['one']);
								
							}
							if($row['two']!=$id){
								array_push($frdsid,$row['two']);
								
							}
						}															//Push into friends_id array
					}
				}
				//print_r($frdsid);
				$sql = "select username from users where user_id in(-1";		//Get usernames of those friends 
				while($i<count($frdsid)){
					$sql .= ",".$frdsid[$i]."";
					$i = $i+1;
				}
				$sql .= ")";
				$result = mysqli_query($conn,$sql);
				if($result!=false){
						if(mysqli_num_rows($result)>0){
							while($row = mysqli_fetch_assoc($result)){
								echo "<p>".$row['username']."</p>";				//print those usernames(accepted friends)
							}
						}
				}
				
			?>
		</div>
		<div id="updates" align="center" style="overflow-y:auto;height: 450px">
			<h3>All Users</h3>														<!-- Remaining Users   -->
				<?php 																//php code for remaining users (section-5)
					$sql = "select user_id,username from users where user_id not in (".$id."";
					$i = 0;
					while($i < count($avoid)){
						$sql .= ",".$avoid[$i].""; 
						$i = $i + 1;												//Avoid those who are in friend request pending and accepted friends
					}
					$i = 0;
					while($i < count($frdsid)){
						$sql .= ",".$frdsid[$i].""; 
						$i = $i + 1;
					}
					$sql .= ");";
					
					
					$result = mysqli_query($conn,$sql);
					
					if($result!=false){
						if(mysqli_num_rows($result)>0){
							while($row = mysqli_fetch_assoc($result)){					//form with button to send friend request
								echo "<p id='heading'>".$row['username']."&nbsp;&nbsp;
								<form id='heading' method='POST' action='friends.php'>
									<input type='hidden' name='u' value='".$id."' /> 
									<input type='hidden' name='r' value='".$row['user_id']."' /> 
									<input type=submit value='+'>
								</form>
								</p>";
							}															//It send current user_id and friend user_id to same page.
						}																//It is handled in  php section-2
					}
					mysqli_close($conn);														//Closing the connection
				?>
		</div>
	</body>
</html>