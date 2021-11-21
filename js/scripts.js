// Выбираем элемент с id="select_month"
var selectMonth = document.querySelector('#select_month');
// Если этот объект существует (т.е. на странице, где нет выбора месяца этот код не сработает)
if (selectMonth) {
	// добавляем <select id="select_month">...</select> обработчик на изменение (change_month() сработает когда значение в select измениться)
	selectMonth.addEventListener('change', change_mounth);

	// функция изменения месяца выборки
	function change_mounth(){
		// Получаем значение выбранного месяца
		let mounth = selectMonth.value;
		// Получаем текущие GET переменные (?month=11/11/2021)
		var search = document.location.search;

		// Если эти значения существуют, то мы будем их добавлять, чтобы не перетереть уже полученные данные
		if (search) {
			// Получаем индекс, места, где начинается подстрока month=
			let index = search.indexOf('month=');
			// Если эта строка есть (indexOf возвращает -1, если он не нашёл подстроку)
			if (index != -1) {

				// то мы вырезаем из строки с переменными подстроку с месяцем (11/11/2021)
				// slice() возвращает подстроку начиная с 0 до начала month= + 6, т.е. также захватывает слово month= 
				// Затем добавляем значение месяца, и добавляем всё оставшееся
				var search = search.slice(0, index+6) + mounth + search.slice(index+15);
			}else{
				// если подстрока не найдена, то проверяем есть ли в конце символ &. если есть, то не добавляем его, если нет, то добавляем
				if(search[-1] == '&'){
					search = search +'month=' + mounth;
				}else{
					search = search + '&month=' + mounth;
				}
			}
		}else{
			// если "переменных" нет, то просто в лоб добавляем ?month=11/11/2021 
			var search = '?month=' + mounth;
		}

		// Получаем текущий URL (protocol (http: или https:), host (my_damain.com), pathname (/my_page/index.html) )
		// Добавляем к адресу наши сгенерированные "переменные"
		let current_url = document.location.protocol +"//"+ document.location.host + document.location.pathname + search;
		// Перенаправляем пользователя по новому URL (по факту просто добавляем ?month=11/11/2021)
		document.location.href = current_url;
	}
}


// Получаем объект <select id="select_group">...</select>
var selectGroup = document.querySelector('#select_group');

// Если он существует
if (selectGroup) {

	// получаем селект с выбором студента
	var selectStudent = document.querySelector('#select_student');
	// добавляем обработчик на изменение значения
	selectGroup.addEventListener('change', getStudents);


	// получение и обновление данных внутри <select> с выбором студента
	// elem это объект события. elem.target это объект, на которм сработало событие 
	function getStudents(elem){
		// Получаем id группы, из которой нужно выбрать студентов
		let selectedGroupId = elem.target.value;

		// Функция, которая сработает, когда js получит данные с сервера ([{'id':'E5...', 'name': '...'}, {...}, {...}])
		// Request объект запроса, он имеет поле responseText, которое содержит текст, который прислал сервер
		function setStudents(Request){

			// Конвертируем полученный текст в JSON-объект 
			let responce = eval("(" + Request.responseText + ")");
			// Сюда будем записывать <option>...</option>, где будут все студенты
			let students = '';
			// В цыкле обходим JSON и в student попадает каждый студент
			responce.forEach(function(student){
				// Добавляем option студента
				students = students + "<option value='"+ student['id'] +"'>"+ student['name'] +"</option>";
			})
			// Записываем наши <option> в <select> (прошлые удаляются)
			selectStudent.innerHTML = students;
		}

		// Эта функция делает запрост методом POST к файлу getstudentsfromgroup.php и предаём ему аргументы ?id=2 а также передаём обработчик, в который пердаётся ответ сервера
		SendRequest('POST', 'getstudentsfromgroup.php', 'id='+selectedGroupId, setStudents);
	}


	// Получаем кнопку отправки 
	var forgettableSend = document.querySelector('#forgettable_send');
	if (forgettableSend) {
		// обработчик на клик
		forgettableSend.addEventListener('click', registerVisit);
		// Блок, куда выведется сообщение об успехе или ошибке
		let addResult = document.getElementById('addResult');


		// Обработчик события 
		function registerVisit(){
			// id студента
			let student_id = selectStudent.value;

			// Обработчик ответа от сервера
			function registrationSuccess(Request){
				// Если сервак отправил Ok!
				if (Request.responseText == 'Ok') {
					addResult.innerHTML = 'Визит успешно добавлен. id='+student_id;
				}else if(Request.responseText == "Error"){
					addResult.innerHTML = 'Ошибка';
				}
				
			}
			// Аналогично функции выше
			SendRequest("POST", "/checkreq.php", "id="+student_id, registrationSuccess);
		}
	}
}


var scores = document.querySelectorAll('.score_input');
if (scores) {


	scores.forEach(function(score){
		score.addEventListener('change', change_score);
	});


	function change_score(elem){
		let el = elem.target;
		let student_id = el.getAttribute('data-student-id');
		let date = el.getAttribute('data-date');
		let id = el.getAttribute('data-score-id');
		let discipline_id = document.querySelector('#discipline').getAttribute('data-discipline');
		let value = el.value;

		if (value >= 2 & value <= 5) {
			if (id) {
				setScore(id, value);
			}else{
				// date, student, score, discipline, el
				addScore(date, student_id, value, discipline_id, el);
			}
		}else if (value == ''){
			if (id) {
				delScore(id, el);
			}
		}else{
			alert('Недопустимое значение!');
			el.value = '';
		}

	}


	function addScore(date, student, score, discipline, el){
		let args = 'date='+date+'&student='+student+'&score='+score+'&discipline='+discipline+'&type=add';
		var handler = function(Request){
			// console.log(eval("(" + Request.responseText + ")") );
			let responce = eval("(" + Request.responseText + ")");
			let status = responce['status'];

			if (status == 'error') {
				alert('Ошибка выполнения запроса, проверьте данные или попробуйте снова.');
			}else{
				el.setAttribute('data-score-id', responce['last_id']);
			}
		}
		SendRequest("POST", 'setscore.php', args, handler);
	}


	function setScore(id, score){
		let args = 'id='+id+'&score='+score+'&type=set';
		var handler2 = function(Request){

			let responce = eval("(" + Request.responseText + ")");
			let status = responce['status'];

			if (status == 'error') {
				alert('Ошибка выполнения запроса, проверьте данные или попробуйте снова.');
			}else{
				
			}
		}
		SendRequest("POST", 'setscore.php', args, handler2);
	}


	function delScore(id, el){
		let args = 'id='+id+'&type=del';
		var handler3 = function(Request){

			let responce = eval("(" + Request.responseText + ")");
			let status = responce['status'];

			if (status == 'error') {
				alert('Ошибка выполнения запроса, проверьте данные или попробуйте снова.');
			}else{
				el.setAttribute('data-score-id', "");
			}
		}
		SendRequest("POST", 'setscore.php', args, handler3);
	}
}

// Добавление границы для активной строки студента
// получаем все строки со студентами, по факту получаем <td> с имененм студента
var table_rows = document.querySelectorAll('.table_row');
if (table_rows) {
	// Вешаем обработчик
	table_rows.forEach(function(table_row){
		table_row.addEventListener('click', add_border);
	});


	// Удаляем класс у всех, у кого он есть
	function rem_border(){
			document.querySelectorAll('.student_active_row').forEach(function(el){
				el.classList.remove('student_active_row');
			}); 
	}


	// Добавляем у нужного
	function add_border(elem){
		// Если класс уже есть, то просто удаляем у всех, иначе еще и добавляем текущему
		if (elem.target.parentElement.classList.contains('student_active_row')) {
			rem_border();
		}else{
			rem_border();
			elem.target.parentElement.classList.add('student_active_row');
		}
	}
}

// Функция создания объекта Request для AJAX-запросов к серверу
function CreateRequest(){
    var Request = false;

    if (window.XMLHttpRequest)
    {
        //Gecko-совместимые браузеры, Safari, Konqueror
        Request = new XMLHttpRequest();
    }
    else if (window.ActiveXObject)
    {
        //Internet explorer
        try
        {
             Request = new ActiveXObject("Microsoft.XMLHTTP");
        }    
        catch (CatchException)
        {
             Request = new ActiveXObject("Msxml2.XMLHTTP");
        }
    }
 
    if (!Request)
    {
        alert("Проблемы с выполнением запроса, перезагрузите браузер или попробуйте другой");
    }
    
    return Request;
} 


/*
Функция посылки запроса к файлу на сервере
r_method  - тип запроса: GET или POST
r_path    - путь к файлу
r_args    - аргументы вида a=1&b=2&c=3...
r_handler - функция-обработчик ответа от сервера
*/
function SendRequest(r_method, r_path, r_args, r_handler){
    //Создаём запрос
    var Request = CreateRequest();
    
    //Проверяем существование запроса еще раз
    if (!Request)
    {
        return;
    }
    
    //Назначаем пользовательский обработчик
    Request.onreadystatechange = function()
    {
        //Если обмен данными завершен
        if (Request.readyState == 4)
        {
            //Передаем управление обработчику пользователя
            r_handler(Request);
        }
    }
    
    //Проверяем, если требуется сделать GET-запрос
    if (r_method.toLowerCase() == "get" && r_args.length > 0)
    r_path += "?" + r_args;
    
    //Инициализируем соединение
    Request.open(r_method, r_path, true);
    
    if (r_method.toLowerCase() == "post")
    {
        //Если это POST-запрос
        
        //Устанавливаем заголовок
        Request.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=utf-8");
        //Посылаем запрос
        Request.send(r_args);
    }
    else
    {
        //Если это GET-запрос
        
        //Посылаем нуль-запрос
        Request.send(null);
    }
} 
