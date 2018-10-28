<?php

namespace  Db;

use PDO;

class Connection
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = new PDO('pgsql:host=postgres;dbname=berrytest;', 'postgres', 'postgres', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    public function get()
    {
        return $this->pdo;
    }
}
