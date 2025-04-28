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
        $this->connection = new mysqli($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
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
        $q = "DELETE FROM `$tbl`";
        $q .= " WHERE ";
        foreach ($where as $key => $value) {
            $q .= "`$key`='$value' AND";
        }
        $q = rtrim($q, " AND ");
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
            $result = $this->connection->query($q);
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
        $q = "UPDATE `$tbl` SET ";
        foreach ($set as $key => $val) {
            $q .= " `$key` = '$val' ,";
        }
        $q = rtrim($q, ',');
        $q = $q . " WHERE ";

        foreach ($where as $key => $val) {
            $q .= " `$key` = '$val'";
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
