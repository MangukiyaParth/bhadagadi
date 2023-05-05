<?php
global $debug_mode, $db, $gh, $last_query;

class MysqliDB
{
    protected $_mysqli;

    public function __construct($host, $username, $password, $db, $port = NULL)
    {
        if ($port == NULL)
            $port = ini_get('mysqli.default_port');

        $this->_mysqli = new mysqli($host, $username, $password, $db, $port)
        or die('There was a problem connecting to the database');

        $this->_mysqli->set_charset('utf8mb4');
        $this->_mysqli->prepare("SET time_zone = '+5:30';")->execute();
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    }

    public function __destruct()
    {
        $this->_mysqli->close();
    }

    public function execute($query)
    {
        $output = array();
        try 
        {
            global $gh, $last_query;
            $query = str_replace("\0", "", $query);
            $last_query = $query;
            $query_start = $gh->LogQueryStart($query);
            $result = array();
            $stmt = $this->_mysqli->prepare($query);
            if ($stmt) {
                $stmt->execute();  
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc())
                {
                    $output[] = $row;
                }
                $stmt->close();
            } else {
                $this->error($this->_mysqli->error);
            }
            $gh->LogQueryEnd($query_start);
            $gh->LogQueryResult($output);
        
        } catch (Exception $e) {
            $gh->Log($e);
        }
        
        return $output;
    }

    function error($message, $level=E_USER_ERROR) { 
        global $gh;
        
        $caller = @next(debug_backtrace()); 
        $message = $message.' in '.$caller['function'].' called from '.$caller['file'].' on line '.$caller['line'].'';
        $message = $message.PHP_EOL.PHP_EOL;
        $message = $message.print_r($caller, true);
        
        //$gh->Log($e);
        $gh->Log($message);

        trigger_error($message."\n<br />error handler", $level);
    }

    public function execute_query($query)
    {
        global $gh, $last_query;
        $cnt = -1;
        $try_cnt = 0;
        $continue = true;

        while( $continue && $try_cnt < 3 ) {
            try 
            {
                if (strlen($query) <= 10) return -2;

                $is_select_query = strtolower(substr(trim($query), 0, 6)) == "select";
                $is_insert_query = strtolower(substr(trim($query), 0, 6)) == "insert";
                $is_update_query = strtolower(substr(trim($query), 0, 6)) == "update";
                $is_delete_query = strtolower(substr(trim($query), 0, 6)) == "delete";
                $is_drop_query = strtolower(substr(trim($query), 0, 14)) == "drop temporary";

                if ($is_select_query) {
                    return $this->execute($query);
                }

                $query = str_replace("\0", "", $query);
                $last_query = $query;
                $query_start = $gh->LogQueryStart($query);

                // explicitly begin DB transaction
                $this->_mysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

                $result = $this->_mysqli->query($query);

                if ($is_insert_query) {
                    $cnt = $this->_mysqli->insert_id;
                    $gh->LogQueryResult(array("insert_id" => $cnt));
                } else if ($is_update_query) {
                    if ($result == true || $result > 0) {
                        $cnt = $this->_mysqli->affected_rows;
                    }
                    $gh->LogQueryResult(array("affected_rows" => $cnt));
                } else if ($is_delete_query || $is_drop_query) { // may be delete query..
                    if(isset($result) && is_object($result) && isset($result->num_rows)){
                        $cnt = $result->num_rows;
                    }
                    $gh->LogQueryResult(array("result->num_rows"=>$cnt));
                }
                $gh->LogQueryEnd($query_start);

                // commit changes
                $this->_mysqli->commit();
                $continue = false;
            }
            
             catch (Exception $e) {
                $try_cnt++;
                $gh->Log(array($try_cnt." Query Error: ", $query));
                $this->_mysqli->rollback();
                $gh->Log($e);
                //print_r($e);
            }
        }
        return $cnt;
    }

    public function execute_scalar($query)
    {
        $query = str_replace("\0", "", $query);
        $result = $this->execute($query);
        $obj = null;
        if (isset($result) && count($result) > 0){
            $obj = array_values($result[0])[0];
        }
        return $obj;
    }

    public function get_row_count($table, $whereData)
    {
        if (is_array($whereData)) {
            $where = "1=1";
            foreach ($whereData as $column => $value) {
                $where .= " AND `$column`='" . $this->_mysqli->real_escape_string($value) . "'";
            }
        } else {
            $where = $whereData;
        }
        $query = "SELECT count(*) as cnt FROM " . $table . " WHERE " . $where;
        $result = $this->execute_scalar($query);
        if (is_array($result) && isset($result["cnt"])) {
            $result = $result["cnt"];
        }
        return $result;
    }

    public function select($columns, $table, $whereData)
    {
        $where = "1=1";
        if (is_array($whereData)) {
            foreach ($whereData as $column => $value) {
                $where .= " AND `$column`='" . $this->_mysqli->real_escape_string($value) . "'";
            }
        } else {
            $where = $whereData;
        }
        $query = " SELECT " . $columns . " FROM " . $table . " WHERE " . $where . "";
        return $this->execute($query);
    }

    public function insert($table, $tableData)
    {
        global $gh;
        $columns = "";
        $values = "";
        foreach ($tableData as $column => $value) {

            $sub_query = "";
            if(is_array($value)){
                if(isset($value["sub_query"])){
                    $sub_query = $value["sub_query"];
                }
            }

            $columns .= ($columns == "") ? "" : ", ";
            $columns .= '`' . ($column) . '`';
            $values .= ($values == "") ? "" : ", ";

            if (is_null($value)) {
                $values .= "null";
            } else if (is_string($value) && $this->is_mysql_func($value)) {
                $values .= $value;
            } else if (is_array($value)) {
                $values .= "( ".$sub_query." )";
            } else {
                $values .= "'" . $this->_mysqli->real_escape_string($value) . "'";
            }
        }
        $query = "insert IGNORE into $table ($columns) values ($values)";
        return $this->execute_query($query);
    }

    public function update($table, $tableData, $whereData)
    {
        global $gh;
        $columns_values = "";
        foreach ($tableData as $column => $value) {
            $columns_values .= ($columns_values == "") ? "" : ", ";

            $sub_query = "";
            if(is_array($value)){
                if(isset($value["sub_query"])){
                    $sub_query = $value["sub_query"];
                }
            }

            if (is_null($value)) {
                $columns_values .= "`$column`= null ";
            } else if (is_string($value) && $this->is_mysql_func($value)) {
                $columns_values .= "`$column`=$value";
            } else if (is_array($value)) {
                $columns_values .= "`$column` = ( ".$sub_query." )";
            } else {
                $columns_values .= "`$column`='" . $this->_mysqli->real_escape_string($value) . "'";
            }
        }

        $where = "";
        if (is_array($whereData)) {
            $where = "1=1";
            foreach ($whereData as $column => $value) {
                $where .= " AND ";

                $sub_query = "";
                if(is_array($value)){
                    if(isset($value["sub_query"])){
                        $sub_query = $value["sub_query"];
                    }
                }

                if (is_string($value) && $this->is_mysql_func($value)) {
                    $where .= "`$column`=$value";
                } else if (is_array($value)) {
                    $where .= "`$column` = ( ".$sub_query." )";
                } else if (is_string($column) && $this->is_mysql_func($column)){
                    $where .= "$column='" . $this->_mysqli->real_escape_string($value) . "'";
                }
                else {
                    $where .= " `$column`='" . $this->_mysqli->real_escape_string($value) . "'";
                }
            }
        } else {
            $where = $whereData;
        }
        $query = "UPDATE $table SET " . $columns_values . " WHERE " . $where;
        return $this->execute_query($query);
    }

    public function delete($table, $whereData)
    {
        global $gh;
        if (is_array($whereData)) {
            $where = "1=1";
            foreach ($whereData as $column => $value) {
                $where .= " AND `$column`='" . $this->_mysqli->real_escape_string($value) . "'";
            }
        } else {
            $where = $whereData;
        }
        $gh->Log("query:delete:table=> ". $table .", where=> ". $where);
        $query = "DELETE FROM " . $table . " WHERE " . $where;
        return $this->execute_query($query);
    }

    public function is_mysql_func($value)
    {
        $pos = strpos($value, "TIMESTAMP");
        if (isset($pos) && $pos > -1) return true;

        $pos = strpos($value, "TIMESTAMPDIFF(");
        if (isset($pos) && $pos > -1) return true;
        
        $pos = strpos($value, "MD5");
        if (isset($pos) && $pos > -1) return true;

        return false;
    }
}
