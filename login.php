<?php
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

	$server = "localhost";
	$username = "root";
	$password = "";
	$database = "dbUser";
	$mysqli = mysqli_connect($server, $username, $password, $database);

	$email = isset($_POST["loginEmail"]) ? $_POST["loginEmail"] : false;
	$pass = isset($_POST["loginPass"]) ? $_POST["loginPass"] : false;	
	// if email and/or pass POST values are set, set the variables to those values, otherwise make them false
?>

<!DOCTYPE html>
<html>
<head>
	<title>IMY 220 - Assignment 2</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Llewellyn de Vries">
	<!-- Replace Name Surname with your name and surname -->
</head>
<body>
	<div class="container">
		<?php
			if($email && $pass){
				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				if($row = mysqli_fetch_array($res)){
					// Print User Details
					echo	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>";
				
					echo 	"<form action='login.php' method='post' enctype='multipart/form-data'>
								<div class='form-group'>
									<input type='file' class='form-control' name='picToUpload[]' multiple='multiple' />
									<input type='hidden' name='loginEmail' value= '" . $row['email'] . "'/>
									<input type='hidden' name='loginPass' value= '" . $row['password'] . "'/>
									<br/>
									<input type='submit' class='btn btn-dark' value='Upload Image' name='submit' />	
								</div>
							</form>";
					
					echo "<h1>Image Gallery</h1>
						<div class='row imageGallery'>";
					
						$query = "SELECT * FROM tbgallery WHERE user_id = $row[user_id]";
						$galleryRes = $mysqli->query($query);
						if ($galleryRes->num_rows > 0) {
							// output data of each row
							while($fileRow = $galleryRes->fetch_assoc()) {
								echo "<div class='col col-3' style='background-image: url(gallery/" . $fileRow["filename"] . ")'></div>";
							}
						}
					echo "</div>";
					
					//upload
					if (isset($_FILES["picToUpload"])){
						$target_dir = "gallery/";
						$uploadImg = $_FILES["picToUpload"];
						$numOfFiles = count($uploadImg["name"]);
						for ($i=0; $i < $numOfFiles; $i++) { 
							$target_file = $target_dir . basename($uploadImg["name"][$i]);
							$validUpload = 1;
							$imageFileType = $uploadImg["type"][$i];
							// Check file size
							if ($uploadImg["size"][$i] > 1000000) {
								echo '<div class="alert alert-danger mt-3" role="alert">
									Sorry, your file is too large. Limit is 1MB.
								</div>';
								$validUpload = 0;
							}
							// Check format
							if( $imageFileType != "image/jpeg") {
								echo '<div class="alert alert-danger mt-3" role="alert">
									Sorry, only JPG/JPEG files are allowed.
								</div>';
								$validUpload = 0;
							}
							// Check if $validUpload is set to 0 by an error
							if ($validUpload == 0) {
								echo '<div class="alert alert-danger mt-3" role="alert">
									Sorry, your file was not uploaded. Check the format and size.
								</div>';
							// if everything is ok, try to upload file
							} else {
								if (move_uploaded_file($uploadImg["tmp_name"][$i], $target_file)) {
									$name = $uploadImg["name"][$i];
									$query = "INSERT INTO tbgallery (user_id, filename) VALUES ('$row[user_id]', '$name')";
									$res = $mysqli->query($query);
									echo '<div class="alert alert-success" role="alert">
											The file ' . basename($uploadImg["name"][$i]). ' has been uploaded.
										</div>';
								} else {
									echo '<div class="alert alert-danger mt-3" role="alert">
										Sorry, there was an error uploading your file.
									</div>';
								}
							}
						}
					}
				}
				else{
					echo	'<div class="alert alert-danger mt-3" role="alert">
								You are not registered on this site!
							</div>';
				}
			} 
			else{
				echo 	'<div class="alert alert-danger mt-3" role="alert">
							Could not log you in
						</div>';
			}
		?>
	</div>
</body>
</html>