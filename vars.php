<?php 
    // переменные, которые используются на многих страницах
    // пеменная, содержащая подключение к БД
	$connect = mysqli_connect('localhost', 'user', 'password', 'database');
    // корень сайта относительно корня файловой системы (не http://... а /var/www/ или что-то подобное)
	$site_root = $_SERVER['SERVER_PROTOCOL'].'//'.$_SERVER['HTTP_HOST'];
    // текущий день (11/11/2021)
    $this_day = date("d/m/Y");
    // Текущий месяц (отрезаем у дня первые 2 символа)
    $this_month = substr($this_day, 2);
    // получаем месяцы из БД
    $months_db = mysqli_query($connect, "SELECT * FROM `months`");
    $months = [];
    foreach($months_db as $current_month){
        array_push($months, $current_month['month']);
    }
    // Если пользоветель передал конкретный месяц через GET (?month=/10/2021)
    if($_GET['month']){
        $selected_month = $_GET['month'];
    }else{
        // иначе, выбираем последний месяц из БД
        $selected_month = end($months);
    }
 ?>
