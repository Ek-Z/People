<?php

include_once 'db.php';
include_once 'people.php';

// получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// создадим экземпляры классов Person и People
$person = new Person($db, 100, 'Anna', 'Bright', '1988-12-10', 1, 'London');
$people = new People($db, '<', 10);
