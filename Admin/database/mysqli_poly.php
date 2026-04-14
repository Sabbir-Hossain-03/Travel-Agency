<?php

if (!method_exists('mysqli_stmt', 'get_result')) {
    if (!function_exists('mysqli_stmt_get_result')) {
        function mysqli_stmt_get_result($stmt) {
        $metadata = $stmt->result_metadata();
        if (!$metadata) {
            return false;
        }
        $fields = $metadata->fetch_fields();
        $params = array();
        $row = array();
        foreach ($fields as $field) {
            $params[] = &$row[$field->name];
        }
        call_user_func_array(array($stmt, 'bind_result'), $params);
        
        $results = array();
        while ($stmt->fetch()) {
            $copy = array();
            foreach ($row as $key => $val) {
                $copy[$key] = $val;
            }
            $results[] = $copy;
        }
        
        return new DummyResult($results);
    }
}
}

if (!class_exists('DummyResult')) {
    class DummyResult {
        private $data;
        private $pointer = 0;
        public $num_rows;

        public function __construct($data) {
            $this->data = $data;
            $this->num_rows = count($data);
        }

        public function fetch_assoc() {
            if ($this->pointer < $this->num_rows) {
                return $this->data[$this->pointer++];
            }
            return null;
        }
        
        public function fetch_all($mode = MYSQLI_ASSOC) {
            return $this->data;
        }
    }
}

if (!function_exists('safe_get_result')) {
    function safe_get_result($stmt) {
        if (method_exists($stmt, 'get_result')) {
            return $stmt->get_result();
        }
        // Fallback to polyfill if the statement has result metadata
        // but not the native get_result method (common on older/limited MySQLi)
        if (function_exists('mysqli_stmt_get_result')) {
            return mysqli_stmt_get_result($stmt);
        }
        return false;
    }
}
?>
