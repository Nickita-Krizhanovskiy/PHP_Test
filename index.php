<?php

require_once 'Person.php';
require_once 'List.php';

// Подключение к базе данных
$conn = new mysqli("my_project", "root", "", "PHP_Test");

// Проверка подключения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Подключение класса Person
require_once 'Person.php';

// Получение человека по id (например, id=1)
try {
    $person = new Person($conn, 1);
    $formattedPerson = $person->format(true, true);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}




// Создание экземпляра класса PersonList
$conditions = array(
    'first_name' => 'John',
    'gender' => 1,
    'birth_date >=' => '1990-01-01'
);
$personList = new PersonList($conn, $conditions);

// Получение массива людей и их вывод
$people = $personList->getPeople();

if (is_array($people)) {
    foreach ($people as $person) {
        // Вывод информации о человеке
    }
} else {
    echo "Ошибка: Неверный формат данных для перебора.";
}

// Удаление людей из БД
$personList->deletePeople($conn);


?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Данные человека</title>
</head>
<body>
<?php if (isset($formattedPerson)): ?>
    <h1>Информация о человеке</h1>
    <p><strong>Имя:</strong> <?php echo htmlspecialchars($formattedPerson->firstName); ?></p>
    <p><strong>Фамилия:</strong> <?php echo htmlspecialchars($formattedPerson->lastName); ?></p>
    <p><strong>Дата рождения:</strong> <?php echo htmlspecialchars($formattedPerson->birthDate); ?></p>
    <p><strong>Возраст:</strong> <?php echo htmlspecialchars($formattedPerson->age); ?> лет</p>
    <p><strong>Пол:</strong> <?php echo htmlspecialchars($formattedPerson->gender); ?></p>
    <p><strong>Город рождения:</strong> <?php echo htmlspecialchars($formattedPerson->birthCity); ?></p>
<?php else: ?>
    <p>Человек не найден.</p>
<?php endif; ?>
</body>
</html>
