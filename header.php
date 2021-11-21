<!-- подключаем к странице данные из файла vars.php -->
<?php include_once('vars.php'); ?>



<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $page_title; ?> | Статистика</title>
<link rel="stylesheet" href="/css/style.css?v=1.0">
<link rel="stylesheet" href="/css/main.css?v=1.0">
<link href="https://fonts.googleapis.com/css2?family=Oswald&amp;display=swap" rel="stylesheet">
<link rel="shortcut icon" href="/img/favicon.png">


	<!-- Charts.js from CDN -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.6.0/chart.min.js" integrity="sha512-GMGzUEevhWh8Tc/njS0bDpwgxdCJLQBWG3Z2Ct+JGOpVnEmjvNx6ts4v6A2XJf1HOrtOsfhv3hBKpK9kE5z8AQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<body>
<header class="header" id="header">
  <div class="nav">
  <img src="/img/logo.png" class="logo">
    <ul class="menu">
    <li>
      <a href="/">
        Главная
      </a>
    </li>
    <li>
      <a href="/diary/">
        Электронный дневник
      </a>
    </li>
    </ul>
  </div>
</header>