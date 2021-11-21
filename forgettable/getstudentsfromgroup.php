<?php 
	include($_SERVER['DOCUMENT_ROOT']."/vars.php");

	if($_POST['id']){
		$id = $_POST['id'];

		$students = mysqli_fetch_all(mysqli_query($connect, "SELECT `id`, `full_name` FROM `students` WHERE `group_id` = 
			$id"));


		echo "[";
		
		foreach($students as $student){
			$id = $student[0];
			$name = $student[1];
			echo "{'id': '$id', 'name': '$name'}, ";
		}

		echo "]";
	}

 ?>
