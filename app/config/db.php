<?php

use config;

class db{
    // Properties
    private $pdo = null;
    private $result = null;

    // Connect
    public function connect($constr){ return $this->__construct($constr); }
    
    public function __construct($constr=''){
        if(empty($constr)) $connect_str = "mysql:host=".config::dbhost.";dbname=".config::dbname.";charset=utf8";
        $this->pdo = new PDO($connect_str, config::dbuser, config::dbpass);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $this->pdo;
    }
    
    //public function sql($sql) { return $this->result= $this->pdo->query($sql); }

    public function sql($sql) {
    	$this->query= $this->pdo->query($sql);
    	$this->result= array();
        $this->myQuery = $sql; // Pass back the SQL
        if(!is_bool($this->query)){ 
            // If the query returns >= 1 assign the number of rows to numResults 
            $this->numResults = $this->query->rowCount();
            $this->result= $this->array();
            return true; // Query was successful 
        }else{ 
            array_push($this->result,$this->result->errorInfo()); 
            return false; // No rows where returned 
        } 
        
        /*$query = @mysqli_query($GLOBALS["___mysqli_ston"], $sql); 
        $this->myQuery = $sql; // Pass back the SQL 
        if(!is_bool($query)){ 
            // If the query returns >= 1 assign the number of rows to numResults 
            $this->numResults = mysqli_num_rows($query); 
            $this->result= array();
            // Loop through the query results by the number of rows returned 
            for($i = 0; $i < $this->numResults; $i++){ 
                $r = mysqli_fetch_array($query); 
                   $key = array_keys($r); 
                   for($x = 0; $x < count($key); $x++){ 
                       // Sanitizes keys so only alphavalues are allowed 
                       if(!is_int($key[$x])){ 
                           if(mysqli_num_rows($query) >= 1){ 
                               $this->result[$i][$key[$x]] = $r[$key[$x]]; 
                        }else{ 
                            $this->result = null; 
                        } 
                    } 
                } 
            } 
            return true; // Query was successful 
        }else{ 
            array_push($this->result,mysqli_error($GLOBALS["___mysqli_ston"])); 
            return false; // No rows where returned 
        } 
*/        
    }
    
    
    
    public function get() { return $this->query->fetch(PDO::FETCH_OBJ); }
    
    //public function all() { return $this->query->fetchAll(PDO::FETCH_OBJ); }
    public function all() { return $this->result; }

    public function array() { return $this->query->fetchAll(PDO::FETCH_ASSOC); }
    
    
    public function getCount($table) { 
        $query = @mysqli_query($GLOBALS["___mysqli_ston"], 'SELECT COUNT(*) FROM '.$table); 
        $this->myQuery = $sql; // Pass back the SQL 
        if($query){ 
            $this->result = mysqli_fetch_array($query)[0]; 
            return true; // Query was successful 
        }else{ 
            array_push($this->result,mysqli_error($GLOBALS["___mysqli_ston"])); 
            return false; // No rows where returned 
        } 
    } 
     
    // Public function to return the data to the user 
    public function getResult(){ 
        $val = $this->result;
        $this->result = array(); 
        return $val; 
    } 

    //Pass the SQL back for debugging 
    public function getSql(){ 
        $val = $this->myQuery; 
        $this->myQuery = array(); 
        return $val; 
    } 

    //Pass the number of rows back 
    public function numRows(){ 
        $val = $this->numResults; 
        $this->numResults = array(); 
        return $val; 
    } 

    // Escape your string 
    public function escapeString($data){ 
        return $this->PDO->quote($data); 
    }     


//no les utilitze de moment:
/*
    // Function to SELECT from the database 
    public function select($table, $rows = '*', $join = null, $where = null, $order = null, $limit = null){ 
        // Create query from the variables passed to the function 
        $q = 'SELECT '.$rows.' FROM '.$table; 
        if($join != null){ 
            $q .= ' JOIN '.$join; 
        } 
        if($where != null){ 
            $q .= ' WHERE '.$where; 
        } 
        if($order != null){ 
            $q .= ' ORDER BY '.$order; 
        } 
        if($limit != null){ 
            $q .= ' LIMIT '.$limit; 
        } 
        $this->myQuery = $q; // Pass back the SQL 
        // Check to see if the table exists 
        if($this->tableExists($table)){ 
            // The table exists, run the query 
            $query = @mysqli_query($GLOBALS["___mysqli_ston"], $q); 
            if($query){ 
                // If the query returns >= 1 assign the number of rows to numResults 
                $this->numResults = mysqli_num_rows($query); 
                // Loop through the query results by the number of rows returned 
                for($i = 0; $i < $this->numResults; $i++){ 
                    $r = mysqli_fetch_array($query); 
                    $key = array_keys($r); 
                    for($x = 0; $x < count($key); $x++){ 
                        // Sanitizes keys so only alphavalues are allowed 
                        if(!is_int($key[$x])){ 
                            if(mysqli_num_rows($query) >= 1){ 
                                $this->result[$i][$key[$x]] = $r[$key[$x]]; 
                            }else{ 
                                $this->result = null; 
                            } 
                        } 
                    } 
                } 
                return true; // Query was successful 
            }else{ 
                array_push($this->result,mysqli_error($GLOBALS["___mysqli_ston"])); 
                return false; // No rows where returned 
            } 
          }else{ 
              return false; // Table does not exist 
        } 
    } 
     
    // Function to insert into the database 
    public function insert($table,$params=array()){ 
        // Check to see if the table exists 
         if($this->tableExists($table)){ 
             $sql='INSERT INTO `'.$table.'` (`'.implode('`, `',array_keys($params)).'`) VALUES ("' . implode('", "', $params) . '")'; 
            $this->myQuery = $sql; // Pass back the SQL 
            // Make the query to insert to the database 
            if($ins = @mysqli_query($GLOBALS["___mysqli_ston"], $sql)){ 
                array_push($this->result,((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res)); 
                return true; // The data has been inserted 
            }else{ 
                array_push($this->result,mysqli_error($GLOBALS["___mysqli_ston"])); 
                return false; // The data has not been inserted 
            } 
        }else{ 
            return false; // Table does not exist 
        } 
    } 
     
    //Function to delete table or row(s) from database 
    public function delete($table,$where = null){ 
        // Check to see if table exists 
         if($this->tableExists($table)){ 
             // The table exists check to see if we are deleting rows or table 
             if($where == null){ 
                $delete = 'DROP TABLE '.$table; // Create query to delete table 
            }else{ 
                $delete = 'DELETE FROM '.$table.' WHERE '.$where; // Create query to delete rows 
            } 
            // Submit query to database 
            if($del = @mysqli_query($GLOBALS["___mysqli_ston"], $delete)){ 
                array_push($this->result,mysqli_affected_rows($GLOBALS["___mysqli_ston"])); 
                $this->myQuery = $delete; // Pass back the SQL 
                return true; // The query exectued correctly 
            }else{ 
                array_push($this->result,mysqli_error($GLOBALS["___mysqli_ston"])); 
                   return false; // The query did not execute correctly 
            } 
        }else{ 
            return false; // The table does not exist 
        } 
    } 
     
    // Function to update row in database 
    public function update($table,$params=array(),$where){ 
        // Check to see if table exists 
        if($this->tableExists($table)){ 
            // Create Array to hold all the columns to update 
            $args=array(); 
            foreach($params as $field=>$value){ 
                // Seperate each column out with it's corresponding value 
                $args[]=$field.'="'.$value.'"'; 
            } 
            // Create the query 
            $sql='UPDATE '.$table.' SET '.implode(',',$args).' WHERE '.$where; 
            // Make query to database 
            $this->myQuery = $sql; // Pass back the SQL 
            if($query = @mysqli_query($GLOBALS["___mysqli_ston"], $sql)){ 
                array_push($this->result,mysqli_affected_rows($GLOBALS["___mysqli_ston"])); 
                return true; // Update has been successful 
            }else{ 
                array_push($this->result,mysqli_error($GLOBALS["___mysqli_ston"])); 
                return false; // Update has not been successful 
            } 
        }else{ 
            return false; // The table does not exist 
        } 
    } 
     
    // Private function to check if table exists for use with queries 
    private function tableExists($table){ 
        $tablesInDb = @mysqli_query($GLOBALS["___mysqli_ston"], 'SHOW TABLES FROM '.$this->db_name.' LIKE "'.$table.'"'); 
        if($tablesInDb){ 
            if(mysqli_num_rows($tablesInDb)==1){ 
                return true; // The table exists 
            }else{ 
                array_push($this->result,$table." does not exist in this database"); 
                return false; // The table does not exist 
            } 
        } 
    } 
*/
    
}
