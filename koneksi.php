<?php
class SQLiteDatabase extends SQLite3{
    function __construct($db_name){
        $this->open($db_name);
        global $dbname;		
		$dbname = $db_name;		
		$this->close();		
	}
	function close(){		
		parent::close();		
	}
	function add_data($table_name,$args){
		global $dbname;
		if($dbname != null && $table_name != null){
			$values = "";
			$count = count($args);
			$i = 0;
			foreach($args as $column_name => $value){
				if($i == ($count - 1)){
					$values .= "'".$value."'";
				}
				else{
					$values .= "'".$value ."',";
				}
				$i++;
			}
			if($this->is_table_exist($table_name)){
				$this->open($dbname);
				$q=$this->prepare("insert into ".$table_name." values(".$values.")") or die("error");
				$q->execute();
				$this->close();
				return true;
			}
			else{
				return false;
			}
		}
	}
	function update_data($table_name,$args_data=null,$args_condition=null){		
		global $dbname;		
		if($dbname != null && $table_name != null && $args_data != null){			
			$values = "";			
			$condition = "";			
			$count_data = count($args_data);			
			$count_condition = count($args_condition);			
			$i = 0;			
			foreach($args_data as $column_name => $value){				
				if($i == ($count_data - 1)){
					$values .= $column_name."='".$value."'";					
				}
				else{					
					$values .= $column_name."='".$value ."',";					
				}				
				$i++;				
			}
			$i = 0;			
			foreach($args_condition as $column_name => $value){				
				if($count_condition > 1){					
					if($i == ($count_condition - 1)){						
						$condition .= $column_name."='".$value."'";						
					}
					else{						
						$condition .= $column_name."='".$value."' AND ";						
					}					
				}
				else{					
					$condition .= $column_name."='".$value."'";					
				}				
				$i++;				
			}			
			if($this->is_table_exist($table_name)){				
				$this->open($dbname);				
				if($count_condition > 0){					
					$q=$this->prepare("update ".$table_name." set ".$values." WHERE ".$condition) or die("error");			
				}
				else{					
					$q=$this->prepare("update ".$table_name." set ".$values) or die("error");					
				}				
				$q->execute() or die('error');				
				$this->close();				
				return true;				
			}			
		}		
		return false;		
	}
	
	function get_data($table_name,$args = null,$order = null,$orderby = null){		
		global $dbname;		
		if($dbname != null && $table_name != null){			
			$this->open($dbname);			
			if($args == null){				
				$result = $this->query("SELECT * FROM ".$table_name);				
				$array = $result->fetchArray();				
				$this->close();				
				return $array;				
			}
			else{				
				$condition = "";				
				$count = count($args);				
				$i = 0;				
				foreach($args as $column_name => $value){					
					if($count > 1){						
						if($i == ($count - 1)){							
							$condition .= $column_name."='".$value."'";							
						}
						else{							
							$condition .= $column_name."='".$value."' AND ";							
						}						
					}
					else{						
						$condition .= $column_name."='".$value."'";						
					}					
				}				
				if($order != null && $orderby != null){					
					$result = $this->query("SELECT * FROM ".$table_name." WHERE ".$conditions." ORDER BY ".$orderby." ".$order);					
				}
				else{					
					$result = $this->query("SELECT * FROM ".$table_name." WHERE ".$conditions);					
				}				
				$array = $result->fetchArray();				
				$this->close();				
				return $array;				
			}						
		}		
	}	
	function delete_data($table_name,$args = null){		
		global $dbname;		
		if($dbname != null && $table_name != null){			
			$this->open($dbname);			
			if($args == null){				
				$result = $this->query("DELETE FROM ".$table_name);				
				$this->close();				
				return true;				
			}
			else{				
				$condition = "";				
				$count = count($args);				
				$i = 0;				
				foreach($args as $column_name => $value){					
					if($count > 1){						
						if($i == ($count - 1)){							
							$condition .= $column_name."='".$value."'";							
						}
						else{							
							$condition .= $column_name."='".$value."' AND ";							
						}						
					}
					else{						
						$condition .= $column_name."='".$value."'";						
					}					
				}				
				$result = $this->query("DELETE FROM ".$table_name." WHERE ".$conditions);				
				$this->close();				
				return true;				
			}			
		}		
		return false;		
	}	
	function get_table_count(){		
		global $dbname;		
		if($dbname != null){			
			$this->open($dbname);			
			$result = $this->query("SELECT * FROM sqlite_master WHERE type='table' ") or die("error");						
			$array = $result->fetchArray();			
			$count = count($array[0]);			
			$this->close();			
			return $count;			
		}		
	}	
	function get_rows_count($table_name){		
		global $dbname;		
		if ($table_name == "" || $dbname == null){			
			return 0;		
		}		
		if($this->is_table_exist($table_name) && $dbname != null){			
			$this->open($dbname);			
			if($result = $this->query("SELECT Count(*) FROM ".$table_name)){				
				$array = $result->fetchArray();				
				$this->close();				
				return count($array[0]);				
			}
			else{				
				$this->close();				
				return 0;				
			}			
		}		
		return 0 ;		
	}
	
	function is_table_exist($table_name){		
		global $dbname;		
		if($dbname != null){			
			$this->open($dbname);			
			$result = $this->query("SELECT * FROM sqlite_master WHERE name='".$table_name."' and type='table'") or die("error");			
			$array = $result->fetchArray();			
			$count = count($array[0]);			
			$this->close();			
			if($count > 0){				
				return true;				
			}
			else{				
				return false;				
			}			
		}		
	}	
	function create_table($table_name,$args=array()){		
		global $dbname;		
		$count = count($args);		
		if($count > 0){			
			$columns="";			
			$i = 0;			
			foreach($args as $column_name => $data_type){				
				if($i == ($count - 1)){					
					$columns .= $column_name." ".$data_type;					
				}
				else{					
					$columns .= $column_name." ".$data_type .",";				
				}				
				$i++;			
			}			
			if(!$this->is_table_exist($table_name) && $dbname != null){				
				$this->open($dbname);				
				$q=$this->prepare("CREATE TABLE ".$table_name." (".$columns.")") or die("error");				
				if($q->execute()){					
					$this->close();					
					return true;				
				}
				else{					
					echo "Tidak dapat membuat tabel baru!!";					
					$this->close();					
					return false;					
				}				
			}		
		}		
		return false;		
	}	
}
?>