<?php

	    session_start();
	    
	    //Change base url with your local URL
	    $base_url = 'http://localhost';

	    //Update database information
		$mysqli = new mysqli("localhost","username","password","db");

		if ($mysqli->connect_errno) {

	  	echo "Failed to connect to MySQL: " . $mysqli->connect_error;

	  	exit();

		}
		// initialize variables
		$name = $err = "";

		if (isset($_REQUEST['submit_btn'])) {

		if(empty($_POST['name'])) : 

		$err  = "* Name is required";

		else:	

		$stmt = $mysqli->prepare("INSERT INTO users (name) VALUES (?)");

		$stmt->bind_param("s", $name);

		$name = $_POST['name'];

		$stmt->execute();

		$_SESSION['message'] = "New record created successfully";

		$stmt->close();

		header('Refresh:1; url='.$base_url.''); 

		endif;
	}

	if (isset($_GET['edit'])) {

		$id 	= $_GET['edit'];

		$update = true;

		$sql 	= "SELECT name FROM users WHERE id=$id";

		$record = $mysqli->query($sql);

		$row 	= $record->fetch_array(MYSQLI_ASSOC);

		if (count($row) >= 1 ) {

			$name = $row['name'];

		}
		
	}

	if (isset($_POST['update'])) {

		if(empty($_POST['name'])) : 

		$err 	= "* Name is required";

		else:	

		$id 	= $_POST['id'];

		$name 	= $_POST['name'];

		$sql 	= "UPDATE users SET name='$name' WHERE id=$id";

		if ($mysqli->query($sql) === TRUE) :

		$_SESSION['message'] = "Record updated successfully!";

	    else:

	    echo "Error updating record: " . $mysqli->error;

		endif;

		header('Refresh:1; url='.$base_url.''); 

		endif;

	}

	if (isset($_GET['del'])) {

		$id  = $_GET['del'];

		$sql = "DELETE FROM users WHERE id=$id";

		if ($mysqli->query($sql) === TRUE) :

			  $_SESSION['message'] = "Record deleted successfully!"; 

		else:

	    echo "Error deleting record: " . $mysqli->error;

		endif;

		header('Refresh:1; url='.$base_url.''); 
	}

?>


<!DOCTYPE html>
<html>
<head>
	<title>PHP</title>
<style type="text/css">
	body {
    font-size: 19px;
}
table{
    width: 50%;
    margin: 30px auto;
    border-collapse: collapse;
    text-align: left;
}

th, td{
    border: 1px solid #ddd;
    text-align: left;
    padding: 15px;
}
tr:hover {
    background: #F5F5F5;
}

form {
    width: 45%;
    margin: 50px auto;
    text-align: left;
    padding: 20px; 
    border: 1px solid #bbbbbb; 
    border-radius: 5px;
}

.input-group {
    margin: 10px 0px 10px 0px;
}
.input-group label {
    display: block;
    text-align: left;
    margin: 3px;
}
.input-group input {
    height: 30px;
    width: 93%;
    padding: 5px 10px;
    font-size: 16px;
    border-radius: 5px;
    border: 1px solid gray;
}
.btn {
    padding: 10px;
    font-size: 15px;
    color: white;
    background: #03a9f4;
    border: none;
    border-radius: 5px;
}
.edit_btn {
    text-decoration: none;
    padding: 2px 5px;
    background: #2E8B57;
    color: white;
    border-radius: 3px;
}

.del_btn {
    text-decoration: none;
    padding: 2px 5px;
    color: white;
    border-radius: 3px;
    background: #ff0000;
}
.message {
    width: 45%;
    margin: 50px auto;
    padding: 10px; 
    border-radius: 5px; 
    color: #3c763d; 
    background: #dff0d8; 
    border: 1px solid #3c763d;
    width: 50%;
    text-align: center;
}
.error {color: #FF0000;}
</style>
</head>
<body>
	<?php if (isset($_SESSION['message'])): ?>
	<div class="message">
		<?php 
			echo $_SESSION['message']; 
			unset($_SESSION['message']);
		?>
	</div>
<?php endif ?>
	<form method="post" action="" >
	<h3><?php echo isset($update) && $update == true ? 'Update Record' : 'Add Record'; ?></h3>
		<div class="input-group">
			<label>Name</label>
			<input type="hidden" name="id" value="<?php echo $id ?? ''; ?>">
			<input type="text" name="name" value="<?php echo $name ?? ''; ?>"><br>
			<span class="error"><?php echo $err;?></span>
		</div>
		<?php if (isset($update) && $update == true): ?>
	<button class="btn" type="submit" name="update" style="background: #e91e63;" >Update</button>
	<a href="<?php echo $base_url;?>" class="btn">Cancel</a>
	<?php else: ?>
	<button class="btn" type="submit" name="submit_btn" >Submit</button>
	<?php endif ?>
	</form>



	<?php   

			$sql = "SELECT * FROM users ORDER BY id DESC";

			$result = $mysqli->query($sql);

		  	if($result && $result->num_rows >= 1) :

			$rows = $result->fetch_all(MYSQLI_ASSOC);
			
	?>
	<hr>
<table>
	<thead>
		<tr>
			<th>Name</th>
			<th colspan="2">Action</th>
		</tr>
	</thead>
	
	<?php foreach($rows as $row) : ?>
		<tr>
			<td><?php echo $row['name']; ?></td>
			<td>
				<a href="?edit=<?php echo $row['id']; ?>" class="edit_btn" >Edit</a>
			</td>
			<td>
				<a href="?del=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete?');" class="del_btn">Delete</a>
			</td>
		</tr>
	<?php endforeach; ?>
</table>
	<?php endif; ?>
</body>
</html>