<?php

session_start();
date_default_timezone_set("Asia/Kolkata");
error_reporting(0);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 0");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

class DataBase
{

    public $connection;

    public function __construct()
    {
        include_once 'constant.php';
        $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        return $this->connection;
    }

    public function INSERT($tbl, $data)
    {

        $k = array_keys($data);
        $v = array_values($data);
        $key = implode("`,`", $k);
        $val = implode("','", $v);
        $q = "INSERT INTO `$tbl` (`$key`) VALUES ('$val')";
        if ($this->connection->query($q)) {
            $result = true;
        } else {
            $result = array(
                "QUERY_ERROR" => $this->connection->error,
                "QUERY_EXECUTE" => $q,
            );
        }
        return $result;
    }

    public function SELECT($tbl, $field = null, $where = null, $op = "AND")
    {

        if (isset($field)) {
            $f = implode("`,`", $field);
            $q = "SELECT `$f` FROM `$tbl`";
        } else {

            $q = "SELECT * FROM `$tbl`";
        }
        if (isset($where)) {
            $q .= " WHERE ";
            foreach ($where as $key => $value) {
                $q .= " `$key` = '$value' $op ";
            }
            $q = rtrim($q, "$op ");
        }

        if ($this->connection->query($q)) {
            $result = $this->connection->query($q);
        } else {
            $result = array(
                "QUERY_ERROR" => $this->connection->error,
                "QUERY_EXECUTE" => $q,
            );
        }
        return $result;
    }

    public function DELETE($tbl, $where)
    {
        $q = "DELETE FROM `$tbl` WHERE ";
        foreach ($where as $key => $value) {
            $q .= "`$key` = '$value' AND ";
        }
        $q = rtrim($q, " AND ");

        if ($this->connection->query($q)) {
            if ($this->connection->affected_rows > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return array(
                "QUERY_ERROR" => $this->connection->error,
                "QUERY_EXECUTE" => $q,
            );
        }
    }

    public function count_record($tbl, $where = null)
    {
        $q = "SELECT COUNT(*) as cn FROM `$tbl`";
        if (isset($where)) {
            $q .= " where ";
            foreach ($where as $key => $value) {
                $q .= " `$key` = '$value' AND ";
            }
            $q = rtrim($q, " AND ");
        }

        $ans = $this->connection->query($q);
        $a = $ans->fetch_object();
        return $a->cn;
    }

    public function ROW_QUERY($q)
    {
        $result = $this->connection->query($q);
        if ($result == true || 1) {
            $result = $result;
        } else {
            $result = array(
                "QUERY_ERROR" => $this->connection->error,
                "QUERY_EXECUTE" => $q,
            );
        }
        return $result;
    }

    public function UPDATE($tbl, $set, $where)
    {
        if (isset($where['id'])) {
            $check_user = $this->SELECT($tbl, null, array("id" => $where['id']));
            if ($check_user && mysqli_num_rows($check_user) == 0) {
                return array(
                    "QUERY_ERROR" => "Record not found",
                    "QUERY_EXECUTE" => "SELECT * FROM `$tbl` WHERE id = " . $where['id'],
                );
            }
        }

        $q = "UPDATE `$tbl` SET ";
        foreach ($set as $key => $val) {
            $q .= " `$key` = '$val',";
        }
        $q = rtrim($q, ',');
        $q .= " WHERE ";

        // FIX: build WHERE clause with AND
        $where_clauses = [];
        foreach ($where as $key => $val) {
            $where_clauses[] = "`$key` = '$val'";
        }
        $q .= implode(" AND ", $where_clauses);

        echo $q;
        if ($this->connection->query($q)) {
            return true;
        } else {
            return array(
                "QUERY_ERROR" => $this->connection->error,
                "QUERY_EXECUTE" => $q,
            );
        }
    }


    public function compress_image($source_url, $destination_url, $quality)
    {
        $info = getimagesize($source_url);
        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($source_url);
        } elseif ($info['mime'] == 'image/gif') {
            $image = imagecreatefromgif($source_url);
        } elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($source_url);
        }
        if ($quality >= 4) {
            $quality = 20;
        } else if ($quality >= 3) {
            $quality = 25;
        } else if ($quality >= 2) {
            $quality = 40;
        } else if ($quality >= 1) {
            $quality = 60;
        } else {
            $quality = 100;
        }
        imagejpeg($image, $destination_url, $quality);
        return $destination_url;
    }

    public function PostValidation($post)
    {
        $allowed_extensions = array('gif', 'png', 'jpg', 'jpeg', 'webp', 'mp4', 'avi', 'flv', 'wmv', 'mov', 'mkv');
        $ext = pathinfo($post, PATHINFO_EXTENSION);
        $result = in_array($ext, $allowed_extensions) == true ? true : false;
        return $result;
    }
}
