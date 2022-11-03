<?php

include_once 'db.php';
include_once 'person.php';

if (!class_exists('Person')) {
    exit('Ошибка: класс Person не найден');
}

/**
класс People
1. Конструктор ведет поиск id людей по всем полям БД (поддерживает
выражения >, <, !=);
2. метод showSelected получает массив экземпляров класса Person из массива с id людей
полученного в конструкторе;
3. метод deleteSelected удаляет людей из БД с помощью экземпляров класса Person в
соответствии с массивом, полученным в конструкторе.
 */
class People extends Person
{
    // подключение к базе данных
    public $conn;

    // свойства объекта
    private $arr;
    public $operator;
    public $num;

    public function __construct($db, $operator, $num)
    {
        $this->conn = $db;

        switch ($operator) {
            case '>':
                $query = "SELECT id FROM people WHERE id > '$num'";
                break;

            case '<':
                $query = "SELECT id FROM people WHERE id < '$num'";
                break;

            default:
                $query = "SELECT id FROM people WHERE id != '$num'";
                break;
        };

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->arr = $arr;
        return $arr;
    }

    public function showSelected()
    {
        foreach ($this->arr as $id) {
            extract($id);
            $query = "SELECT * FROM people WHERE id = $id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result[] = $stmt->fetch(PDO::FETCH_ASSOC);
        };

        return $result;
    }

    public function deleteSelected()
    {
        foreach ($this->arr as $id) {
            extract($id);
            $query = "DELETE FROM people WHERE id = $id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result[] = $stmt->fetch(PDO::FETCH_ASSOC);
        };

        return $result;
    }
}
