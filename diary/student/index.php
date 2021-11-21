<?php
	$page_title = 'Девник'; 
	include($_SERVER['DOCUMENT_ROOT']."/vars.php");
	session_start();

	// Если авторизации нет, то редиректим на логин, и если пользователь не студент, то редиректим на diary/
	if($_SESSION['authorization']){
		$authorization = $_SESSION['authorization'];
		if($authorization['type'] != "student"){
			header('location: ../');
		}
	}else{
		header('location: ../login.php');
	}

	// Достаём данные из сессии
	$student_name = $_SESSION['authorization']['full_name'];
	$group = $_SESSION['authorization']['group'];
	$student_id = $_SESSION['authorization']['id'];
	// Все студенты из группы
	$students_from_group = mysqli_query($connect, "SELECT `id`, `full_name` FROM `students` WHERE `group_id` = $group");
	$students_from_group = mysqli_fetch_all($students_from_group, MYSQLI_ASSOC);


	// проверяем посещаемость группы
	// Генерируем строку, где через запятую идут id всех студентов группы
	$students = '';
	foreach($students_from_group as $student){
		$students = $students."'".$student['id']."',";
	}
	$students = substr_replace($students, '', -1); //удаляем последнюю ","
	// Получаем количество посещений за этот день, у которых student_id один из тех, что мы достали из таблицы
	$query = "SELECT COUNT(*) FROM `visits` WHERE `date` = '$this_day' AND `student_id` IN ($students)";
	$this_day_visits = mysqli_fetch_assoc(mysqli_query($connect, $query));
	// кол-во студентов в группе
	$total_students = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) FROM `students` WHERE `group_id` = '$group'"));
	// посещаемость группы за сегодня
	$this_day_passing = $this_day_visits['COUNT(*)'] / $total_students['COUNT(*)'] * 100;

	// аналогично
	$dates = [];
	$percents = [];

	for ($i=1; $i <= 31; $i++) {
		if ($i < 10){
			$day = "0$i".$selected_month;
		}else{
			$day = "$i".$selected_month;
		}
		$selected_day_passing = mysqli_query($connect, "SELECT * FROM `daily_group_statistics` WHERE `group_id` = $group AND `date` = '$day' ");
		$selected_day_passing = mysqli_fetch_all($selected_day_passing, MYSQLI_ASSOC);
		if($selected_day_passing[0]){
			array_push($dates, $day);
			array_push($percents, $selected_day_passing[0]['percent']);
		}
	}
	if($selected_month == $this_month){
		array_push($dates, $this_day);
		array_push($percents, $this_day_passing);	
	}

	// Все предметы, которые есть у группы
	$disciplines = mysqli_query($connect, "SELECT * FROM `disciplines` WHERE `group_id` = '$group'");
	$disciplines = mysqli_fetch_all($disciplines, MYSQLI_ASSOC);


	include($_SERVER['DOCUMENT_ROOT']."/header.php");
 ?>

<div class="container">
	<div class="info">
		<span> <?= $student_name; ?> </span>
		<span> (Студент) | </span>
		<span class="out"><a href="/diary/login.php?out=1">Выйти</a></span>
	</div>
	<div class="content">

		<div class="bot">
			<p>Выбрать месяц</p>
			<select name="select_month" id="select_month" class="bt">
				<?php 
					foreach($months as $month){
				 ?>
				<option value="<?= $month ?>" <?php if ($month == $selected_month) {echo 'selected';} ?>>
					<?= $month ?>	
				</option>
				<?php 
					}
				?>
			</select>
		</div>

		
		<canvas id="grafica"></canvas>

		<div class="scroll">
			<table>
				<caption>Оценки за <?= $selected_month; ?></caption>
				<thead>
					<tr>
						<th rowspan="2">Предметы:</th>
						<th colspan="<?= count($dates);//кол-во элементов в массиве ?>">Числа:</th>
						<th rowspan="2">Ср. балл:</th>	
					</tr>
					<tr>
						<?php 

						foreach($dates as $date){
							$day = substr($date, 0, 2);
							$is_visit = mysqli_fetch_all(mysqli_query($connect, "SELECT `id` FROM `visits` WHERE `student_id` = '$student_id' AND `date` = '$date'"));
							if($is_visit){
								$is_visit_class = 'visited';
							}else{
								$is_visit_class = '';
							}
							echo "<th class='$is_visit_class'>$day</th>";
						}

						 ?>
					</tr>
				</thead>
				<tbody>
					<?php 
						foreach($disciplines as $discipline){
							$title = $discipline['title'];
							$disciplineID = $discipline['id'];
							$amount_scores = 0;
							$scores_counter = 0;
							$average_score = '';
							echo "<tr> <td> $title </td>";
							foreach($dates as $date){
								$score = mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `scores` WHERE `discipline_id` = '$disciplineID' AND `student_id` = '$student_id' AND `date` = '$date'"));
								$score = $score[0][1];
								echo "<td>$score</td>";
								if ($score) {
									$amount_scores = $amount_scores + $score;
									$scores_counter++;
								}
							}
							if ($scores_counter) {
								$average_score = round($amount_scores / $scores_counter, 2);
							}
							echo "<td>$average_score</td></tr>";
						}
					 ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
	const $grafica = document.querySelector("#grafica");
	const tags = [ <?php foreach ($dates as $key => $value) {
		echo "'$value',";
	} ?> ]
	const data = [<?php foreach ($percents as $key => $value) {
		echo "'$value',";
	} ?> ]
	const passing = {
	    label: "Посещаемость группы за <?=$selected_month; ?>",
	    data: data,
	    backgroundColor: 'rgba(54, 162, 235, 1)', 
	    borderColor: 'rgba(54, 162, 235, 1)', 
	    borderWidth: 1,
	};
	new Chart($grafica, {
	    type: 'line',
	    data: {
	        labels: tags,
	        datasets: [
	            passing,
	        ]
	    },
	    options: {
	    	tension: 0.4
	    }
	});
</script>
 <?php include($_SERVER['DOCUMENT_ROOT']."/footer.php"); ?>