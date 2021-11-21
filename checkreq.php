<?php 
	include('vars.php');

	// Если нам передали id
	if ($_POST['id']) {
		$id = $_POST['id'];

		// проверяем есть ли вообще такой человек
		if (mysqli_fetch_assoc(mysqli_query($connect, "SELECT `id` FROM `students` WHERE `id` = '$id'"))) {
			
			// Если он сегодня еще не отмечался
			if (!mysqli_fetch_assoc(mysqli_query($connect, "SELECT `id` FROM `visits` WHERE `student_id` = '$id' AND `date` = '$this_day'"))) {

				// добавляем его визит в таблицу
				mysqli_query($connect, "INSERT INTO `visits` (`id`, `student_id`, `date`) VALUES (NULL, '$id', '$this_day')");
			}
			// Говорим, что всё ок
			echo "Ok";
		}else{
			echo "Error";
		}
	}

 ?>