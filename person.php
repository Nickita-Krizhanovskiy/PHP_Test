<?php
class Person {
    private $id;
    private $firstName;
    private $lastName;
    private $birthDate;
    private $gender;
    private $birthCity;
    private $conn;

    public function __construct($dbConnection, $id = null, $firstName = null, $lastName = null, $birthDate = null, $gender = null, $birthCity = null) {
        $this->conn = $dbConnection;

        if ($id) {
            $this->loadFromDB($id);
        } else {
            $this->firstName = $firstName;
            $this->lastName = $lastName;
            $this->birthDate = $birthDate;
            $this->gender = $gender;
            $this->birthCity = $birthCity;

            if ($this->validate()) {
                $this->saveToDB();
            }
        }
    }

    private function validate() {
        return preg_match('/^[A-Za-zА-Яа-я]+$/', $this->firstName) &&
            preg_match('/^[A-Za-zА-Яа-я]+$/', $this->lastName) &&
            in_array($this->gender, [0, 1]);
    }

    private function loadFromDB($id) {
        $stmt = $this->conn->prepare("SELECT * FROM person WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->id = $row['id'];
            $this->firstName = $row['first_name'];
            $this->lastName = $row['last_name'];
            $this->birthDate = $row['birth_date'];
            $this->gender = $row['gender'];
            $this->birthCity = $row['birth_city'];
        } else {
            throw new Exception("Person not found");
        }
    }

    public function saveToDB() {
        if ($this->id) {
            $stmt = $this->conn->prepare("UPDATE person SET first_name = ?, last_name = ?, birth_date = ?, gender = ?, birth_city = ? WHERE id = ?");
            $stmt->bind_param("sssisi", $this->firstName, $this->lastName, $this->birthDate, $this->gender, $this->birthCity, $this->id);
        } else {
            $stmt = $this->conn->prepare("INSERT INTO person (first_name, last_name, birth_date, gender, birth_city) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssiss", $this->firstName, $this->lastName, $this->birthDate, $this->gender, $this->birthCity);
        }
        $stmt->execute();
        if (!$this->id) {
            $this->id = $this->conn->insert_id;
        }
    }

    public function deleteFromDB() {
        if ($this->id) {
            $stmt = $this->conn->prepare("DELETE FROM person WHERE id = ?");
            $stmt->bind_param("i", $this->id);
            $stmt->execute();
        }
    }

    public static function birthDateToAge($birthDate) {
        $birthDate = new DateTime($birthDate);
        $currentDate = new DateTime();
        $age = $currentDate->diff($birthDate)->y;
        return $age;
    }

    public static function genderToText($gender) {
        return $gender == 0 ? 'муж' : 'жен';
    }

    public function format($withAge = false, $withGenderText = false) {
        $formattedPerson = new stdClass();
        $formattedPerson->id = $this->id;
        $formattedPerson->firstName = $this->firstName;
        $formattedPerson->lastName = $this->lastName;
        $formattedPerson->birthDate = $this->birthDate;
        $formattedPerson->gender = $withGenderText ? self::genderToText($this->gender) : $this->gender;
        $formattedPerson->birthCity = $this->birthCity;
        $formattedPerson->age = $withAge ? self::birthDateToAge($this->birthDate) : null;
        return $formattedPerson;
    }
}

