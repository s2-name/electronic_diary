<?php
	$page_title = 'Журнал'; 
	include($_SERVER['DOCUMENT_ROOT']."/vars.php");
	session_start();
	if($_SESSION['authorization']){
		$authorization = $_SESSION['authorization'];
		if($authorization['type'] != "teacher"){
			header('location: ../');
		}
	}else{
		header('location: ../login.php');
	}

	$teacher_name = $_SESSION['authorization']['full_name'];
	$teacher_id = $_SESSION['authorization']['id'];

	$disciplines = mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `disciplines` WHERE `teacher_id` = $teacher_id"));

	include($_SERVER['DOCUMENT_ROOT']."/header.php");
 ?>


<div class="container">
	<div class="content">
		<div class="select-object">
		 	<div class="header">
				<span> <?= $teacher_name; ?> </span>
				<span> (Преподаватель) | </span>
				<span class="out"><a href="/diary/login.php?out=1">Выйти</a></span>
			</div>
			<?php 
				foreach($disciplines as $discipline){
					
					$group = mysqli_fetch_all(mysqli_query($connect, "SELECT `name` FROM `groups` WHERE `id` = $discipline[2]"))[0][0];
			 ?>
			<div class="object">
				<a href="discipline?id=<?= $discipline[0] ?>" class="discipline">
					<div class="title"><?= $discipline[1]; ?></div>
					<div class="group"><?= $group; ?></div>
				</a>
			</div>
			<?php } ?>
		</div>
	</div>
</div>





 <?php include($_SERVER['DOCUMENT_ROOT']."/footer.php"); ?>