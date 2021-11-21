<?php 
	include('vars.php');

	// Все визиты за сегодня
	$this_day_visits = mysqli_fetch_assoc(mysqli_query($connect, "SELECT `id` FROM `visits` WHERE `date` = '$this_day' LIMIT 1"));

	// если они были, т.е. если посещаемости не было значит выходной
	if ($this_day_visits) {

		// все группы
		$groups = mysqli_fetch_all(mysqli_query($connect, "SELECT `id` FROM `groups`"));


		// Для каждой группы проверяем посещаемость
		foreach($groups as $group){
			// счетчик
			$this_day_visits_counter = 0;
			// id текущей группы
			$group_id = $group[0];
			// если сегодня эту группу еще не учитывали
			if (!mysqli_fetch_assoc(mysqli_query($connect, "SELECT `id` FROM `daily_group_statistics` WHERE `group_id` = '$group_id' AND `date` = '$this_day'"))) {
				// Все студенты этой группы
				$students_from_group = mysqli_fetch_all(mysqli_query($connect, "SELECT `id` FROM `students` WHERE `group_id` = '$group_id'"));

				//Проверяем посещаемость каждого студента 
				foreach($students_from_group as $student){

					$is_visit = mysqli_fetch_all(mysqli_query($connect, "SELECT `id` FROM `visits` WHERE `student_id` = '$student[0]' AND `date` = '$this_day'"));

					if ($is_visit[0][0]) {
						$this_day_visits_counter++;
					}
				}
				
				// если студенты есть, то вычисляем посещаемость этой группы
				if ($students_from_group[0][0]) {
					$this_day_group_passing = $this_day_visits_counter / count($students_from_group) * 100;
					
					// записываем в БД
					mysqli_query($connect, "INSERT INTO `daily_group_statistics` (`id`, `group_id`, `date`, `percent`) VALUES (NULL, '$group_id', '$this_day', '$this_day_group_passing') ");
				}	
			}
		}
	}


 ?>