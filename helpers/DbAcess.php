<?php

class DbAcess {

    private $conn;

    public function __construct() {
        $db_url = getenv('DATABASE_URL');

        if ($db_url) {
            $parsed = parse_url($db_url);
            $hostname = $parsed['host'];
            $username = $parsed['user'];
            $password = $parsed['pass'];
            $database = ltrim($parsed['path'], '/');
            $port = isset($parsed['port']) ? $parsed['port'] : 5432;
        } else {
            $hostname = "localhost";
            $username = "postgres";
            $password = "";
            $database = "loandisk";
            $port = 5432;
        }

        try {
            $dsn = "pgsql:host=$hostname;port=$port;dbname=$database";
            $this->conn = new PDO($dsn, $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function update($table, $set = array(), $where = array()) {
        $q = "UPDATE $table SET ";
        $params = array();
        $i = 0;
        
        if ($set) {
            foreach ($set as $key => $value) {
                $q .= "$key = :set_$i,";
                $params[":set_$i"] = $value;
                $i++;
            }
            $q = substr($q, 0, -1) . " WHERE ";
        }
        
        $j = 0;
        foreach ($where as $key1 => $value1) {
            $q .= "$key1 = :where_$j AND ";
            $params[":where_$j"] = $value1;
            $j++;
        }
        $q = substr($q, 0, -4);

        try {
            $stmt = $this->conn->prepare($q);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function insert($table, $data = array()) {
        $cols = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $q = "INSERT INTO $table ($cols) VALUES ($placeholders) RETURNING id";

        try {
            $stmt = $this->conn->prepare($q);
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['id'];
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function select($table, $cols = array(), $where = FALSE) {
        $q = "SELECT ";
        if (empty($cols)) {
            $q .= "*";
        } else {
            $q .= implode(',', $cols);
        }
        $q .= " FROM $table";

        $params = array();
        $orderby = "";

        if ($where) {
            $wherestr = "";
            $i = 0;
            foreach ($where as $key => $value) {
                if ($key == "order by") {
                    $orderby = " ORDER BY $value";
                } else {
                    $wherestr .= " $key = :where_$i AND";
                    $params[":where_$i"] = $value;
                    $i++;
                }
            }
            $wherestr = substr($wherestr, 0, -3);
            if ($wherestr && strlen($wherestr) > 3) {
                $q .= " WHERE " . $wherestr;
            }
            $q .= $orderby;
        }

        try {
            $stmt = $this->conn->prepare($q);
            $stmt->execute($params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($result) == 1) {
                return $result[0];
            } else {
                return $result;
            }
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
            return FALSE;
        }
    }

    public function selectQuery($sql) {
        try {
            $stmt = $this->conn->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($result) == 1) {
                return $result[0];
            } else {
                return $result;
            }
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
            return FALSE;
        }
    }

    private function isValidEmail($string) {
        return filter_var($string, FILTER_VALIDATE_EMAIL) !== false;
    }

}
