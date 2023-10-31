<?php
//login.php

include('database_connection.php');

if(isset($_SESSION['type']))
{
	header("location:index.php");
}

$message = '';

if(isset($_POST["login"]))
{
	$query = "
	SELECT * FROM user_details 
		WHERE user_email = :user_email
	";
	$statement = $connect->prepare($query);
	$statement->execute(
		array(
				'user_email'	=>	$_POST["user_email"]
			)
	);
	$count = $statement->rowCount();
	if($count > 0)
	{
		$result = $statement->fetchAll();
		foreach($result as $row)
		{
			if($row['user_status'] == 'Active')
			{
				if(password_verify($_POST["user_password"], $row["user_password"]))
				{
					//update user_failed_attempts to 0
					$query = "
					UPDATE user_details
					SET user_failed_attempts = '0'
					WHERE user_email = '".$_POST["user_email"]."'
					";
					$statement = $connect->prepare($query);
					$statement->execute();

				
					$_SESSION['type'] = $row['user_type'];
					$_SESSION['user_id'] = $row['user_id'];
					$_SESSION['user_name'] = $row['user_name'];
					header("location:index.php");
				}
				else
				{
					//keep count of failed login attempts
					$query = "
					UPDATE user_details
					SET user_failed_attempts = user_failed_attempts + 1
					WHERE user_email = :user_email
					";
					$statement = $connect->prepare($query);
					$statement->execute(
						array(
								'user_email'	=>	$_POST["user_email"]
							)
					);
					$message = '<label class="text-danger">Wrong Password</label>';
					//after 3rd failed attempt, lock account and send user to error_page.html
					$query = "
					SELECT * FROM user_details 
						WHERE user_email = :user_email
					";
					$statement = $connect->prepare($query);
					$statement->execute(
						array(
								'user_email'	=>	$_POST["user_email"]
							)
					);
					$result = $statement->fetchAll();
					foreach($result as $row)
					{
						if($row['user_failed_attempts'] > 2)
						{
							$query = "
							UPDATE user_details
							SET user_status = 'Inactive'
							WHERE user_email = :user_email
							";
							$statement = $connect->prepare($query);
							$statement->execute(
								array(
										'user_email'	=>	$_POST["user_email"]
									)
							);
							header("location:error_page.html");
						}
					}
					
				}
			}
			else
			{
				$message = "<label>Your account is disabled, Contact Master</label>";
			}
		}
	}
	else
	{
		$message = "<label>Wrong Email Address</labe>";
	}
}

?>

<!DOCTYPE html>
<html>
	<head>
		<title>CS-4783 Inventory Management System </title>		
		<script src="js/jquery-1.10.2.min.js"></script>
		<link rel="stylesheet" href="css/bootstrap.min.css" />
		<script src="js/bootstrap.min.js"></script>
	</head>
	<body>
		<br />
		<div class="container">
			<h2 align="center">CS-4783 Inventory Management System</h2>
			<br />
			<div class="panel panel-default">
				<div class="panel-heading">Login</div>
				<div class="panel-body">
					<form method="post">
						<?php echo $message; ?>
						<div class="form-group">
							<label>User Email</label>
							<input type="text" name="user_email" class="form-control" required />
						</div>
						<div class="form-group">
							<label>Password</label>
							<input type="password" name="user_password" class="form-control" required />
						</div>
						<div class="form-group">
							<input type="submit" name="login" value="Login" class="btn btn-info" />
						</div>
					</form>
				</div>
			</div>
			<h3 align="center"><a href="https://github.com/wgalindo1453">by William Galindo</a>
		</div>
	</body>
</html>