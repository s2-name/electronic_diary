<?php 
	$page_title = 'Отметить студента';
	include($_SERVER['DOCUMENT_ROOT']."/header.php");

	$groups = mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `groups`"));




 ?>
<div class="container">
	<div class="content">
		<div class="auth">
			<h2>Отметить студента</h2>
			<div class="group">Группа</div>
			<select id="select_group" class="select_group">
				<?php 
					foreach($groups as $group){
						$title = $group[1];
						$group_id = $group[0];
						echo "<option value='$group_id'>$title</option>";
					}
				 ?>
			</select>
			<div class="student">Студент</div>
			<select id="select_student" class="select_student">
				<?php 
					$group_id = $groups[0][0];
					$students = mysqli_fetch_all(mysqli_query($connect, "SELECT `id`, `full_name` FROM `students` WHERE `group_id` = '$group_id' "));
					foreach($students as $student){
						$student_id = $student[0];
						$name = $student[1];
						echo "<option value='$student_id'>$name</option>";
					}
				 ?>
			</select>
			<div class="send"><button id="forgettable_send" class="btn">Отметить</button></div>
			<div class="addResult" id="addResult"></div>
		</div>
	</div>
</div>







 <?php 
 	include($_SERVER['DOCUMENT_ROOT'].'/footer.php');


  ?>