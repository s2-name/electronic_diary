<?php 
	include($_SERVER['DOCUMENT_ROOT']."/vars.php");

	// Если данные есть
	if($_POST){

		// Оценка
		$score = $_POST['score'];

		// Если нужно добавить
		if($_POST['type'] == 'add'){

			$date = $_POST['date'];
			$student = $_POST['student'];
			$discipline = $_POST['discipline'];

			// Добавляем
			mysqli_query($connect, "INSERT INTO `scores` (`id`, `score`, `discipline_id`, `student_id`, `date`) VALUES (NULL, '$score', '$discipline', '$student', '$date')");

			// получаем id вставленной строки
			$last_id = mysqli_insert_id($connect);

		}elseif($_POST['type'] == 'set'){   // Если нужно изменить

			// Изменяем
			$score_id = $_POST['id'];
			mysqli_query($connect, "UPDATE `scores` SET `score` = '$score' WHERE `scores`.`id` = '$score_id'; ");
		
		}elseif($_POST['type'] == 'del'){   // Если нужно удалить

			// удаляем строку, где такой id
			$score_id = $_POST['id'];
			mysqli_query($connect, "DELETE FROM `scores` WHERE `scores`.`id` = $score_id");
		}
		// Если случилась ошибка говорим об этом
		if (mysqli_error($connect)) {

			$status = error;
			$error_text = mysqli_error($connect);

		}else{

			// Иначе все хорошо 
			$status = 'Ok!';
			$error_text = '';
		}

		if (!$last_id) {
			$last_id = "";
		}

		// Выводим структуру JSON-объекта
		echo "{ ";
		echo "'status': '$status', ";
		echo "'error_text': '$error_text', ";
		echo "'last_id': '$last_id' ";
		echo "}";
	}

 ?>