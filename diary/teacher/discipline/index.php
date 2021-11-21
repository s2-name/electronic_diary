<?php
	$page_title = 'Журнал | дисциплина'; 
	include($_SERVER['DOCUMENT_ROOT']."/vars.php");
	session_start();

	if($_SESSION['authorization']){
		$authorization = $_SESSION['authorization'];
		if($authorization['type'] != "teacher"){
			header('location: /diary/');
		}
	}else{
		header('location: /diary/login.php');
	}

	$teacher_name = $_SESSION['authorization']['full_name'];
	$teacher_id = $_SESSION['authorization']['id'];

	if($_GET['id']){
		$discipline_id = $_GET['id'];
		$discipline = mysqli_fetch_all(mysqli_query($connect, "SELECT `group_id`, `title` FROM `disciplines` WHERE `id` = $discipline_id"));

		$group_id = $discipline[0][0];
		$discipline_title = $discipline[0][1];

		$students_from_group = mysqli_query($connect, "SELECT `id`, `full_name` FROM `students` WHERE `group_id` = '$group_id'");
		$students_from_group = mysqli_fetch_all($students_from_group, MYSQLI_ASSOC);
		$group_name = mysqli_fetch_all(mysqli_query($connect, "SELECT `name` FROM `groups` WHERE `id` = $group_id"))[0][0];
	}else{
		header('location: ../');
	}
	
	$students = '';
	foreach($students_from_group as $student){
		$students = $students."'".$student['id']."',";
	}
	$students = substr_replace($students, '', -1);

	$query = "SELECT COUNT(*) FROM `visits` WHERE `date` = '$this_day' AND `student_id` IN ($students)";
	$this_day_visits = mysqli_fetch_assoc(mysqli_query($connect, $query));
	$total_students = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) FROM `students` WHERE `group_id` = '$group_id'"));

	$this_day_passing = $this_day_visits['COUNT(*)'] / $total_students['COUNT(*)'] * 100;

	$dates = [];
	$percents = [];

	for ($i=1; $i <= 31; $i++) {
		if ($i < 10){
			$day = "0$i".$selected_month;
		}else{
			$day = "$i".$selected_month;
		}
		$selected_day_passing = mysqli_query($connect, "SELECT * FROM `daily_group_statistics` WHERE `group_id` = $group_id AND `date` = '$day' ");
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

	include($_SERVER['DOCUMENT_ROOT']."/header.php");
 ?>



<div class="container">
	<div class="content">
		<!-- <div class="contentteacher"> -->
	 		<div class="header">
	 			<!-- <div class="teacher"> -->
	 				<div>
				 		<a href="/diary/teacher/" class="back"> <!-- <img src="/img/back2.png"> --></a> 
						<span> | <?= $teacher_name; ?> </span>
						<span> (Преподаватель) | </span>
						<span class="out"><a href="/diary/login.php?out=1">Выйти</a></span>
					</div>
					<div class="discipline-wraper">
						<span id="discipline" style="display: none;" data-discipline="<?= $discipline_id; ?>"></span>
						<span class="discipline">Дисциплина: <?= $discipline_title; ?> | </span>
						<span class="group">Группа: <?= $group_name; ?></span>
					</div>
				<!-- </div> -->
			<div>
		<!-- </div> -->
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
						<th rowspan="2">Студенты:</th>
						<th colspan="<?= count($dates); ?>">Числа:</th>	
						<th rowspan="2">Ср. балл:</th>	
					</tr>
					<tr>
						<?php 

						foreach($dates as $date){
							$day = substr($date, 0, 2);
							echo "<th>$day</th>";
						}

						 ?>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach($students_from_group as $student){
							$name = $student['full_name'];
							$student_id = $student['id'];
							$amount_scores = 0;
							$scores_counter = 0;
							$average_score = '';
							echo "<tr> <td class='table_row'> $name </td>";
							foreach($dates as $date){
								$is_visit = mysqli_fetch_assoc(mysqli_query($connect, "SELECT `date` FROM `visits` WHERE `date` = '$date' AND `student_id` = '$student_id'"));
								if($is_visit){
									$is_visit_class = 'visited';
								}else{
									$is_visit_class = '';
								}
								$scoreDB = mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `scores` WHERE `discipline_id` = '$discipline_id' AND `student_id` = '$student_id' AND `date` = '$date'"));
								$score = $scoreDB[0][1];
								$score_id = $scoreDB[0][0];
		 						echo "<td><input class='score_input $is_visit_class' type='text' value='$score' data-score-id='$score_id' data-student-id='$student_id' data-date='$date'></td>";
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