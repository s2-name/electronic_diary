<?php 
	session_start();
	$page_title = "Авторизация";
	include($_SERVER['DOCUMENT_ROOT']."/vars.php");

	// Если пользователь хочет выйти удаляем сессию авторизации
	if($_GET['out'] == '1'){
		$_SESSION['authorization'] = '';
	}

	// Если пользователь авторизован, то редиректим на index
	if($_SESSION['authorization']){
		header('location: /diary/');
	}else{

		// Проверяем есть ли данные для авторизации
		if($_POST['login'] and $_POST['password']){

			$login = $_POST['login'];
			$password = md5($_POST['password']); //md5 шифрует данные (хеширует)
			// Достаём студента, у которого логин и пароль соответствуют введённым
			$authorization = mysqli_query($connect, "SELECT `id`, `full_name`, `group_id` FROM `students` WHERE `login` = '$login' AND `password` = '$password'");
			$authorization = mysqli_fetch_all($authorization, MYSQLI_ASSOC);

			// Если такой пользователь нашёлся, то делаем ему сессию авторизации и редиректим
			if($authorization[0]){
				$_SESSION['authorization'] = array(
					'id' => $authorization[0]['id'],
					'full_name' => $authorization[0]['full_name'],
					'group' => $authorization[0]['group_id'],
					'type' => "student"
				);
				header('location: /diary/');
			
			}else{

				//если такого студента нет, то ищем его в преподавателях
				$authorization = mysqli_query($connect, "SELECT `id`, `full_name` FROM `teachers` WHERE `login` = '$login' AND `password` = '$password'");
				$authorization = mysqli_fetch_all($authorization, MYSQLI_ASSOC);

				if($authorization[0]){
					$_SESSION['authorization'] = array(
						'id' => $authorization[0]['id'],
						'full_name' => $authorization[0]['full_name'],
						'type' => 'teacher'
					);
					header('location: /diary/');
				}
			}
		}
	}


	include($_SERVER['DOCUMENT_ROOT']."/header.php");
 ?>

<section>
	<div class="container">
		<div class="auth">
			<form autocomplete="on" method="post">
				<h1>Авторизация</h1>
				<p class="login_fields">
					<label for="username" class="uname"> Логин: </label>
					<input id="username" class="logininput" name="login" required="required" type="text" placeholder="login" autocomplete="off">
				</p>
				<p class="login_fields">
					<label for="password" class="youpasswd" data-icon="p"> Пароль: </label>
					<input id="password" class="logininput" name="password" required="required" type="password" placeholder="password">
				</p>
				<p class="login_button_wraper"><input type="submit" value="Войти" class="sign"></p>
            </form>
        </div>
    </div>
</section>



 <?php include($_SERVER['DOCUMENT_ROOT'].'/footer.php'); ?>