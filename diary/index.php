<?php 
	// Стартуем сессию
	session_start();
	// Если сессия существует (пользователь авторизован)
	if($_SESSION['authorization']){
		$authorization = $_SESSION['authorization'];
		// Если пользователь учитель, то редиректим его в нужную папку, если студент то в другую
		if($authorization['type'] == "teacher"){
			header('location: teacher');
		}elseif($authorization['type'] == "student"){
			header('location: student');
		}
	}else{
		// если пользователь не авторизован редиректим на авторизацию
		header('location: login.php');
	}

 ?>