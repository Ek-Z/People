<?php

include_once 'db.php';

/**
класс Person
1. метод save сохраняет поля экземпляра класса в БД;
2. метод delete удаляет человека из БД в соответствии с id объекта;
3. static метод age преобразовывает дату рождения в возраст (полных лет);
4. static метод gender преобразовывает пол из двоичной системы в текстовую (муж,
жен);
5. Конструктор класса либо создает человека в БД с заданной
информацией, либо берет информацию из БД по id;
6. Метод format производит форматирование человека с преобразованием возраста и (или) пола
в зависимотси от параметров (возвращает новый экземпляр
StdClass со всеми полями изначального класса).
 */
class Person
{
    // подключение к базе данных
    public $conn;

    // свойства объекта
    private $id;
    private $name;
    private $surname;
    private $birth_date;
    private $gender;
    private $city_of_birth;

    public function __construct($db, $id, $name, $surname, $birth_date, $gender, $city_of_birth)
    {
        if (!ctype_alpha($name)) {
            exit('Имя должно содержать только латинские буквы');
        }

        if (!ctype_alpha($surname)) {
            exit('Фамилия должна содержать только латинские буквы');
        }

        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])$/", $birth_date)) {
            exit('Дата должна быть в формате YYYY-MM-DD');
        }

        if (!preg_match("/[0-1]/", $gender)) {
            exit('Пол должен быть указан как 0 или 1');
        }

        $this->conn = $db;
        $this->id = $id;
        $this->name = $name;
        $this->surname = $surname;
        $this->birth_date = $birth_date;
        $this->gender = $gender;
        $this->city_of_birth = $city_of_birth;

        $query = "SELECT * FROM people WHERE id = '$id'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result == false) {
            $this->save($name, $surname, $birth_date, $gender, $city_of_birth);
        } else {
            return $result;
        }
    }

    private function save($name, $surname, $birth_date, $gender, $city_of_birth)
    {
        $query = "INSERT INTO people (name, surname, birth_date, gender, city_of_birth) VALUES ('$name', '$surname', '$birth_date', '$gender', '$city_of_birth')";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function delete($id)
    {
        $query = "DELETE FROM people WHERE id = '$id'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public static function age($birth_date)
    {

        $d1 = date('Y', strtotime($birth_date));
        $d2 = date("Y");
        $age = $d2 - $d1;

        return $age;
    }

    public static function gender($gender)
    {
        if ($gender == 0) {
            $gender = 'муж';
        } else {
            $gender = 'жен';
        }

        return $gender;
    }

    public function format($id, $formatAge = false, $formatGender = false)
    {
        //получаем данные человека по id
        $query = "SELECT * FROM people WHERE id = '$id'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_LAZY);

        //создаем новый экземпляр StdClass
        $newPerson = new stdClass;
        $newPerson->name = $row['name'];
        $newPerson->surname = $row['surname'];

        if ($formatAge == true) {
            $newPerson->age = self::age($row['birth_date']);
        } else {
            $newPerson->age = $row['birth_date'];
        };

        if ($formatGender == true) {
            $newPerson->gender = self::gender($row['gender']);
        } else {
            $newPerson->gender = $row['gender'];
        };

        $newPerson->city_of_birth = $row['city_of_birth'];

        return $newPerson;
    }
}
