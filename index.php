<?php 
	$page_title = 'Главная';
	include('header.php');

	// получаем все визиты за сегодняшний день
	$this_day_visits = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) FROM `visits` WHERE `date` = '$this_day'"));
	// Получаем количество студентов в нашей БД
	$total_students = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) FROM `students`"));
	// Вычисляем посещаемость за сегодня
	$this_day_passing = $this_day_visits['COUNT(*)'] / $total_students['COUNT(*)'] * 100;
	
	// Даты и проценты этих дат
	$dates = [];
	$percents = [];

	// Проходим в цикле по дням с 1 по 31
	for ($i=1; $i <= 31; $i++) {
		// если чило < 10, то добавляем ему 0 (01, 02, 03)
		// Добавляем выбранный месяц, получая конкретное число
		if ($i < 10){
			$day = "0$i".$selected_month;
		}else{
			$day = "$i".$selected_month;
		}

		// Сумма процентов
		$current_day_ammount_percent = 0;
		// Количество обработанных групп
		$current_day_group_counter = 0;
		// Получаем из базы посещаемости всех групп по текущему числу
		$selected_day_passing = mysqli_query($connect, "SELECT * FROM `daily_group_statistics` WHERE `date` = '$day'");
		// обрабатываем полученные данные в цикле
		foreach($selected_day_passing as $current_group){
			// Прибавляем процент посещаемости и увеличиваем счётчик
			$current_day_ammount_percent += $current_group['percent'];
			$current_day_group_counter++;
		}

		// Если в этот день была посещаемость, вычисляем её и добаляем в массивы
		if($current_day_group_counter > 0){
			$current_day_passing_percent = $current_day_ammount_percent / $current_day_group_counter;
			array_push($dates, $day);
			array_push($percents, $current_day_passing_percent);
		}
	}

	// Если выбранный месяц является текущим, то добавляем посещаемость за сегодня
	if($selected_month == $this_month){
		array_push($dates, $this_day);
		array_push($percents, $this_day_passing);	
	}
 ?>

<div class="container">
	<div class="content">
		<div class="bot">
			<p>Выбрать месяц</p>
			<select name="select_month" id="select_month" class="bt">
				<?php 
				// В цикле проходим месяцы и каждый месяц добавляем в  <option>
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
		<!-- Здесь будет график -->
		<canvas id="grafica"></canvas>

	</div>
</div>

<!-- Скрипт генерации графика посещаемости -->
<script>
	const $grafica = document.querySelector("#grafica");
	const tags = [ <?php foreach ($dates as $key => $value) {
		echo "'$value',";
	} ?> ]
	const data = [<?php foreach ($percents as $key => $value) {
		echo "'$value',";
	} ?> ]
	const passing = {
	    label: "Посещаемость за <?=$selected_month; ?>",
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
	    	tension: 0.4,
	    	defaultFontColor: '#000',
	    }
	});
</script>


 	
<?php 
	include('footer.php');
 ?>