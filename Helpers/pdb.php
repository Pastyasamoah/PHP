<?php


/**
*
* Performing CRUD functionalities
* @package PDB
* @author asamoah Pasty <pastyasamoah13@gmail.com> +233 546116102
* @version 1.0
* @since 4th Dec, 2019
*
*
*/


class PDB

{
	/**
	*
	* @var connection. Holds the connection
	* 
	* @access private
	*
	*/

	private $_conn; 

	/**
	*
	* @var string. Database name
	* 
	* @access private
	*
	*/

	private $_db_name;

	/**
	*
	* @var string. Database username
	* 
	* @access private
	*
	*/
	
	private $_db_username;

	/**
	*
	* @var string. Database password
	* 
	* @access private
	*
	*/
	
	private $_db_password;


	/**
	*
	* @var table column names
	* 
	* @access private
	*
	*/

	private $_columns = [];

	/**
	*
	* @var table rows
	* 
	* @access private
	*
	*/

	private $_rows = [];

	/**
	*
	* @var values to insert into the table
	* 
	* @access private
	*
	*/

	private $_values = [];

	/**
	*
	* @var query to update table
	* 
	* @access private
	*
	*/

	private $_updateQueryString = "";

	/**
	*
	* @var where condition
	* 
	* @access private
	*
	*/

	private $_where = "";

	/**
	*
	* @var accessing from
	* 
	* @access private
	*
	*/
	private $_from = 0;

	/**
	*
	* @var accessing to
	* 
	* @access private
	*
	*/

	private $_to = 0;

	/**
	*
	* @var grouping table values
	* 
	* @access private
	*
	*/

	private $_group_by = "";

	/**
	*
	* @var ordering table values
	* 
	* @access private
	*
	*/

	private $_order_by = "";

	/**
	*
	* @var table name
	* 
	* @access private
	*
	*/

	private $_table = "users";

	/**
	*
	* @var sql query
	* 
	* @access private
	*
	*/

	private $_sql_query = "";
	

	/**
	*
	* @var constructor
	*
	* @access public
	*
	* @param string database name, string database username with default root, string database password with default null
	*
	* @return null
	*
	*/

	public function __construct($db_name,$db_username='root',$db_password="")
	
	{
		$this->_db_name = $db_name;
		$this->_db_username = $db_username;
		$this->_db_password = $db_password;
		$this->connect($this->_db_name,$this->_db_username,$this->_db_password);
	}


	/**
	*
	* @var connect. Connect to database
	*
	* @access public
	*
	* @param string database name, username and database password
	*
	* @return 
	*/

	private function Connect($db_name,$db_username,$db_password)

	{
		$DSN = "mysql:dbname=$db_name"; // the driver and database name
		
		try
		{
			$Conn = new PDO($DSN,$db_username,$db_password, array(PDO::ATTR_PERSISTENT => true) ); // database connection
			
			$Conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION); 
			
			$this->_conn = $Conn;

		}
		catch(PDOException $e)
		{

			echo $e->getMessage();  // catching the exception thrown, if it returns true
		
		}

	}



	/**
	*
	* @var Use
	*
	* @access public
	*
	* @param string Table Name. Selects table for ooperation
	*
	* @return null
	*
	*/

	public function Use($TableName)

	{

		$this->_table = $TableName;

		return clone $this;

	}


	/**
	*
	* @var SQL
	*
	* @access public
	*
	* @param string SQL Query
	*
	* @return null
	*
	*/

	public function Query($SQLQuery)

	{

		if(preg_match("/^[Ss]/", trim($SQLQuery)) )

		{

			return $this->Fetch($SQLQuery);

		}

		return $this->Execute($SQLQuery);

	}


	/**
	*
	* @var Insert. Insert into table
	*
	* @access public
	*
	* @param null 
	*
	* @return int 1 - success else failed
	*/

	public function Insert()

	{

		$this->ExtractInsertColumns(); // extract and assign columns array to column variable
		$this->ExtractInsertValues(); // extract and assign values array to values variable

		$SQL = "INSERT INTO `".$this->_table."` ".$this->_columns." VALUES ".$this->_values; // query string

		try
		{

			return $this->Execute($SQL); // execute

		}
		catch(PDOException $Error)
		{

			echo $Error->getMessage();  // error

		}

	}



	/**
	*
	* @var Update
	*
	* @access public
	*
	* @param null. Update the table
	*
	* @return int
	*/

	public function Update()

	{

		$this->ExtractUpdateColumnsAndValues();

		$SQL = "UPDATE `".$this->_table."` SET ".$this->_updateQueryString." WHERE ".$this->_where;

		try
		{
			
			return $this->Execute($SQL);

		}
		catch(PDOException $Error)
		{

			echo $Error->getMessage();  

		}

	}



	/**
	*
	* @var Delete
	*
	* @access public
	*
	* @param null. delete from the table
	*
	* @return int-success/ string - fail
	*/

	public function Delete()

	{


		if( $this->_where == "" )
		
		{

			$SQL = "DELETE FROM `".$this->_table."`";

		}
		else

		{

			if( ($this->_from > 0) AND ($this->_to > 0) )

			{

				$SQL = "DELETE FROM `".$this->_table."` WHERE `".$this->_where."` BETWEEN '".$this->_from."' AND '".$this->_to."'";

			}
			elseif ( ($this->_from > 0) AND ($this->_to == 0) ) 
			{
				
				$SQL = "DELETE FROM `".$this->_table."` WHERE `".$this->_where."` >= '".$this->_from."'";
			
			}
			else

			{

				$SQL = "DELETE FROM `".$this->_table."` WHERE ".$this->_where;

			}
			

		}

		try
		{

			return $this->Execute($SQL);

		}
		catch(PDOException $Error)
		{

			echo $Error->getMessage();  

		}


	}



	/**
	*
	* @var Get
	*
	* @access public
	*
	* @param int (Limit)
	*
	* @return json
	*/

	public function Get(int $Limit=0)

	{
		$limit = ($Limit > 0)? "LIMIT ".$Limit."":"";
	

		if(count($this->_columns)==0){$this->_columns="*";}else{$this->ExtractSelectColumns();}


		if( $this->_where == "" )
		
		{

			$SQL = "SELECT ".$this->_columns." FROM `".
							 $this->_table."` ".
							 $this->_order_by." ".
							 $this->_group_by." ".
							 $limit;

		}
		else

		{


			if( ($this->_from > 0) AND ($this->_to > 0) )

			{
				
				$SQL = "SELECT ".$this->_columns." FROM `".
							     $this->_table."`  WHERE ".
							     $this->_where." BETWEEN '".
							     $this->_from."' AND '".
							     $this->_to."'".
							     $this->_order_by." ".
							     $this->_group_by." ".
							     $limit;

			}
			elseif ( ($this->_from > 0) AND ($this->_to == 0) ) 
			{
				

			     $SQL = "SELECT ".$this->_columns." FROM `".
							      $this->_table."` WHERE ".
							      $this->_where." >= '".
							      $this->_from."'".
							      $this->_order_by." ".
							      $this->_group_by." ".
							      $limit;
			
			}
			else

			{
			
			    $SQL = "SELECT ".$this->_columns." FROM `".
								 $this->_table."`  WHERE ".
								 $this->_where." ".
								 $this->_order_by." ".
							     $this->_group_by." ".
							     $limit;
			}



		}
		try
		{

			return $this->Fetch($SQL);

		}
		catch(PDOException $Error)
		{

			echo $Error->getMessage();  

		}


	}




	/**
	*
	* @var Fetch
	*
	* @access private
	*
	* @param sql query 
	*
	* @return json
	*/

	private function Fetch($sql)

	{

		$result = $this->_conn->prepare($sql);

		$result->execute();

		$this->UnsetVariables(); // remove all set variables

		return json_decode(json_encode($result->fetchAll(PDO::FETCH_ASSOC))); // convert array to json

	}


	/**
	*
	* @var execute
	*
	* @access private
	*
	* @param sql query 
	*
	* @return int
	*/

	private function Execute($sql)

	{

		$this->UnsetVariables(); // remove all set variables
		
		return $this->_conn->prepare($sql)->execute();

	}



	/**
	*
	* @var columns. Set columns
	*
	* @access public
	*
	* @param null 
	*
	* @return object
	*/

	public function Columns()

	{

		$this->_columns = func_get_args();
		
		return $this;

	}


	/**
	*
	* @var values. Set values
	*
	* @access public
	*
	* @param null 
	*
	* @return object
	*/


	public function Values()

	{

		$this->_values = func_get_args();

		return $this;

	}



	/**
	*
	* @var From
	*
	* @access public
	*
	* @param int
	*
	* @return object
	*/
	
	public function From($from)

	{

		$this->_from = (int)$from;

		return $this;

	}


	/**
	*
	* @var To
	*
	* @access public
	*
	* @param int
	*
	* @return object
	*/

	public function To($to)

	{

		$this->_to = (int)$to;

		return $this;
	}


	/**
	*
	* @var Where. Assumes the operator is = 
	*
	* @access public
	*
	* @param string/array (condition), array (Operators : AND, OR, XOR, etc.)
	*
	* @return object
	*/

	public function Where($Conditions, $LogicalOperators=[])

	{

		$WhereConditionString = "";

		$Keys = [];

		if(is_array($Conditions)) // check if is array, then extract otherwise is a string

		{

			foreach($Conditions as $key => $Condition)
			
			{
			
				$Keys[] = $key;
			
			}

			for($i = 0; $i <= count($Conditions)-1; $i++)

			{
				if( count($Conditions) > 1)

				{
					if(count($LogicalOperators) == 0){ die("Missing an Array Args of : AND / OR");}

					$WhereConditionString .= " (`".$Keys[$i]."` = '".$Conditions[$Keys[$i]]."') ".@$LogicalOperators[$i]; 

				}

				else

				{

					$WhereConditionString .= " (`".$Keys[$i]."` = '".$Conditions[$Keys[$i]]."') "; 

				}
				

			}

			$this->_where = rtrim($WhereConditionString,end($LogicalOperators));

		}

		else

		{

			$this->_where = $Conditions; // This is the column name only

		}

		return $this;

	}


	/**
	*
	* @var WhereCondition. Does not assume an operator. Specify to meet your want
	*
	* @access public
	*
	* @param string/array (condition), array (Operators : AND, OR, XOR, etc.), Logical Operators (=, !=, >, <, etc.)
	*
	* @return object
	*/

	public function WhereCondition(Array $Conditions, Array $Operators, Array $LogicalOperators=[])

	{

		$WhereConditionString = "";

		$Keys = [];

		foreach($Conditions as $key => $Condition)
		
		{
		
			$Keys[] = $key;
		
		}

		for($i = 0; $i <= count($Conditions)-1; $i++)

		{
			if( count($Conditions) > 1)

			{
				if(count($LogicalOperators) == 0){ die("Missing an Array Args of : AND / OR");}

				$WhereConditionString .= " (`".$Keys[$i]."` ".$Operators[$i]." '".$Conditions[$Keys[$i]]."') ".@$LogicalOperators[$i]; 

			}

			else

			{

				$WhereConditionString .= " (`".$Keys[$i]."` ".$Operators[$i]." '".$Conditions[$Keys[$i]]."') "; 

			}
			

		}

		$this->_where = rtrim($WhereConditionString,end($LogicalOperators));

		return $this;


	}




	/**
	*
	* @var ExtractInsertColumns
	*
	* @access public
	*
	* @param null. Extract insert columns array and convert to string in query friendly form 
	*
	* @return null
	*/

	public function ExtractInsertColumns()

	{

		$Cols = " "; // array keys (database field name)

		foreach ($this->_columns as $Column)
		{
			$Cols.="`".$Column."`,";
		}

		$this->_columns = "(".rtrim($Cols,',').")"; // removes last comma {,}

	}


	/**
	*
	* @var ExtractInsertValues
	*
	* @access public
	*
	* @param null. Extract insert values array and convert to string in query friendly form 
	*
	* @return null
	*/

	public function ExtractInsertValues()

	{

		$Cols = " "; // array keys (database field name)

		foreach ($this->_values as $Column)
		{
			$Cols.="'".$Column."',";
		}

		$this->_values = "(".rtrim($Cols,',').")"; // removes last comma {,}

	}

	
	/**
	*
	* @var ExtractSelectColumns
	*
	* @access public
	*
	* @param null. Extract select values array and convert to string in query friendly form 
	*
	* @return null
	*/

	public function ExtractSelectColumns()

	{

		$Cols = " "; // array keys (database field name)

		foreach ($this->_columns as $Column)
		{
			$Cols.="`".$Column."`,";
		}

		$this->_columns = rtrim($Cols,','); // removes last comma {,}

	}


	/**
	*
	* @var ExtractUpdateColumnsAndValues
	*
	* @access public
	*
	* @param null. Extract update columns and values array and convert to string in query friendly form 
	*
	* @return null
	*/

	public function ExtractUpdateColumnsAndValues()

	{

		$UpdateString = "";

		for($i = 0; $i <= count($this->_columns)-1; $i++)

		{

			$UpdateString .= "`".$this->_columns[$i]."` = '".$this->_values[$i]."',"; 

		}

		$this->_updateQueryString = rtrim($UpdateString,',');


	}


	/**
	*
	* @var Group. Set the groupby variable
	*
	* @access public
	*
	* @param string. column to group by
	*
	* @return object
	*/

	public function Group($columnName)

	{

		$this->_group_by = "GROUP BY `".$columnName.'`';
		return $this;

	}


	/**
	*
	* @var Order. Set the orderbt variable
	*
	* @access public
	*
	* @param string. column to order by
	*
	* @return object
	*/


	public function Order($columnName, $order = "A")

	{
		$order = ($order=="A") ? "ASC":"DESC";

		$this->_order_by = "ORDER BY `".$columnName."` ".$order;

		return $this;

	}


	/**
	*
	* @var Unset. unset all variables to the default values
	*
	* @access private
	*
	* @param null
	*
	* @return null
	*/

	private function UnsetVariables()

	{

		$this->_columns = [];
		$this->_rows = [];
		$this->_values = [];
		$this->_where = "";
		$this->_from = 0;
		$this->_to = 0;
		$this->_group_by = "";
		$this->_order_by = "";
		$_table = "users";

	}

	/**
	*
	* @var destructor
	*
	* @access private
	*
	* @param null
	*
	* @return null
	*/

	public function __destruct()
	{

		$this->UnsetVariables(); // remove all set variables

	}


}




##USAGE ##################################################################
 SIMPLE TUTORIAL ON PDB 	                      
#########################################################################


# $Database = new PDB("babyjay"); // Connecting to Database
# $Students = $Database->Use("Students"); // Selecting Students Table
# $Marks = $Database->Use("Marks"); // Selecting Marks Table
# $Countries = $Database->Use("countries"); // Selecting countries Table



# $Database = new PDB("babyjay");
# $AllStudents = $Database->Use("Students")->get(); // gets all from students table
# $AllBooks = $Database->Use("Books")->get(); // get all from books table



# $AllStudents = $Students->Get();
# $AllMarks = $Marks->Get();



# Examples : Inserting into table

# $Students->Values("","Pasty BBB ","Male",12)->Insert();



# Examples : Updating a table values

# $Students->Columns("Gender","Name")->Values("F","Yaa")->Where(["studentid"=>20])->Update();

# $Students->Columns("Gender","Name")
#	       ->Values("F","Babina")
#	       ->Where( ["studentid"=>16, "Gender"=>"F"], ["AND"] )
#	       ->Update();

# $Students->Columns("Gender","Name")
#	      ->Values("F","Babina")
#	      ->WhereCondition( ["studentID"=>14, "Gender"=>"F"], [">=","="] ,["AND"] )
#	      ->Update();



# Examples : Deleting from a table

# $Students->Where(["studentid"=>4])->Delete();
# $Students->Where(["studentid"=>4,"Name"=>"Ama"],["AND"])->Delete();
# $Students->WhereCondition( [ "id"=>1, "Name"=>"Hello" ], [ ">", "=" ], [ "AND" ] )->Delete();
# $Students->Where("studentID")->From(12)->To(13)->Delete();
# $Students->Where("studentID")->From(6)->Delete();



#Examples : Fetching from a table

// $Result = $Students->Order("Name")->Get();
// $Result = $Students->Where("StudentID")->From(1)->To(13)->Get();
// $Result = $Students->Where(['StudentID'=>"1"])->Get();
// $Result = $Students->WhereCondition(["StudentID"=>3], [">"])->Group("Gender")->Get();
// $Result = $Students->Columns("Name","Gender")->Order("Gender","D")->get(5);
// $Result = $Students->Where("StudentID")->From(1)->To(13)->Get();
// $Result = $Students->Where(["Gender"=>"F", "Age"=>"15"],["AND"] )->Order("Name")->Get();


# Examples: User Defined Queries


# $SQL = "SELECT * FROM `Students`";
# $FetchResult = $Database->Query($SQL);


# $SQL = "DELETE FROM `Students` WHERE `StudentID`=20";
# $Result = $Database->Query($SQL);


#Query method is able to differentiate between when you want to fetch or not
#If it is not a fetch query, it returns 1 or 0 for success and failed respectively
