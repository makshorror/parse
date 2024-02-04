<?php
require_once 'db_config.php';

class Database
{
    public $connect;

    public function databaseConnect()
    {
        try {
            $this->connect = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        } catch (Exception $exception){
            return die("Ошибка подключения к Базе данных: " . $exception->getMessage());
        }

        return $this->connect;

    }

    public function databaseConnectClose()
    {
        $this->connect->close();
        return $this->connect;
    }

    public function createTable()
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS Parse (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, article VARCHAR(60) NULL, product_name VARCHAR(60) NULL, price INT(6) NULL, balance INT(2) NULL)";
            $this->connect->query($sql);
        } catch (Exception $exception) {
            echo "Ошибка создания таблицы: " . $exception->getMessage();
        }
        return $this->connect;
    }

    public function dropTable()
    {

        try {
            $sql = "DROP TABLE IF EXISTS `Parse`";
            $this->connect->query($sql);
        } catch (Exception $exception) {
            echo "Ошибка удаления таблицы: " . $exception->getMessage();
        }

        return $this->connect;
    }

    public function pushInDB($data) {
        foreach ($data as $row) {
            $article = $row['0'];
            $row['1'] ? $product_name = $row['1'] : $product_name = null;
            $row['2'] ? $price = intval(str_replace(",", "", $row['2'])) : $price = 0;
            $row['3'] ? $balance = intval($row['3']) : $balance = 0;

            $sql = "INSERT INTO Parse (product_name, article ,price, balance) VALUES ('$product_name', '$article' ,'$price', '$balance')";
            if ($this->connect->query($sql) === false) echo "Ошибка: " . $this->connect;
        }
    }
}