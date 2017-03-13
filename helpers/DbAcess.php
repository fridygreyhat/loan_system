<?php

//include_once 'db/db.php';

class DbAcess {

    private $conn;

    //$sql = $this->con->prepare("SELECT {$_cols} FROM `{$table}`{$_cls} {$order} {$limit}");
    //put your code here
    public function __construct() {
        // include './connect.php';
        $servername = "localhost";
        $username = "root";
        $password = "readme";

        // Create connection
        $this->conn = new mysqli($servername, $username, $password);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        mysqli_select_db($this->conn, "loandisk");
        // echo "Connected successfully";
    }

    /**
      @param string $table name of the table
      @param Array $set array(key=value,key=>vale)
     */
    public function update($table, $set = array(), $where = array()) {
        //UPDATE `booda`.`users` SET `lname` = 'jumix' WHERE `users`.`id` = 4;
        $q = "UPDATE $table SET ";
        if ($set) {
            foreach ($set as $key => $value) {
                $q.= $key . " ='$value',";
            }

            $q = substr($q, 0, -1) . " where ";
        }
        foreach ($where as $key1 => $value1) {
            $q.="$key1 = '$value1' and";
        }
        $q = substr($q, 0, -3);
        //  echo '///'.$q."/////";
        //$sql = $this->con->prepare("SELECT {$_cols} FROM `{$table}`{$_cls} {$order} {$limit}");
        $query = mysqli_query($this->conn, $q);

        return $query;
    }

    public function insert($table, $data = array()) {
        $q = "insert into $table ";
        $values = "(";
        $cols = "(";
        foreach ($data as $key => $value) {
            $cols.="$key,";
            $values.="'" . mysqli_real_escape_string($this->conn, $value) . "',";
        }

        $cols = substr($cols, 0, -1) . ")";
        $values = substr($values, 0, -1) . ")";
        $q.=$cols . " values $values";
        //echo $q."////";
        $query = mysqli_query($this->conn, $q);
        if ($query) {
            return mysqli_insert_id($this->conn);
        }
        return mysqli_error($this->conn);
    }

    public function select($table, $cols = array(), $where = FALSE) {
        $q = "Select ";
        if (empty($cols)) {
            $q = "select *";
        } else {
            $strCols = "";
            foreach ($cols as $col) {
                $strCols.=$col . ",";
            }

            $strColsfinal = substr($strCols, 0, -1);
            $q = "Select " . $strColsfinal;
        }
        $q.=" from $table";

        if ($where) {
            // $keys=  array_keys($where);
            $wherestr = "";
            $orderbyValue = "";
            $orderby = FALSE;
            foreach ($where as $key => $value) {
                if ($key == "order by") {
                    //echo "True key=$key and ".($key=="order by");
                    $orderbyValue = $value;
                    $orderby = TRUE;
                } else {
                    $checkEmail = $this->isValidEmail($value);
                    if ($checkEmail) {
                        $wherestr.=" $key ='$value' and";
                    } else {
                        $explodable = explode('.', $value);

                        //print_r($explodable);
                        if (count($explodable) > 1) {
                            $wherestr.=" $key =$value and";
                        } else {
                            if ($value[0] == "!") {
                                 $wherestr.=" $key !='$value' and";
                            } else {
                                $wherestr.=" $key ='$value' and";
                            }
                        }
                    }
                }
            }

            $wherestr = substr($wherestr, 0, -3);
            if ($wherestr && strlen($wherestr) > 3) {
                $q.=" where " . $wherestr;
            }

            if ($orderby) {

                $q.=" order by $orderbyValue";
            }
        }

        $query = mysqli_query($this->conn, $q);
        // echo $q;
        $result = array();
        if ($query) {
            while ($row = mysqli_fetch_array($query, MYSQL_ASSOC)) {
                $result[] = $row;
            }
            if (count($result) == 1) {
                return $result[0];
            } else {
                return $result;
            }
        } else {
            echo 'Erorr ' . mysqli_error($this->conn);
        }

        return FALSE;
    }

    public function selectQuery($sql) {
        $query = mysqli_query($this->conn, $sql);
        // echo $q;
        $result = array();
        if ($query) {
            while ($row = mysqli_fetch_array($query, MYSQL_ASSOC)) {
                $result[] = $row;
            }
            if (count($result) == 1) {
                return $result[0];
            } else {
                return $result;
            }
        } else {
            echo 'Erorr ' . mysqli_error($this->conn);
        }

        return FALSE;
    }

    private function isValidEmail($string) {
        if (filter_var($string, FILTER_VALIDATE_EMAIL)) {
            //echo "This ($email_a) email address is considered valid.";
            return TRUE;
        }
        return FALSE;
    }

}
