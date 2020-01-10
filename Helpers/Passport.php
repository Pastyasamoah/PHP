<?php 

/**
*
* Validating and authenticating user inputs
* @package Passport
* @author asamoah Pasty <pastyasamoah13@gmail.com> +233 546116102
* @version 1.0
* @since 8th Jan, 2020
*
*
*/

class Passport

{
	/**
	*
	* @var string. The input name in the form
	* 
	* @access private
	*
	*/

	private $Variable = "";

	/**
	*
	* @var string .user current input when filling form
	* 
	* @access private
	*
	*/

	private $UserInput = "";

	/**
	*
	* @var array. stores all user inputs
	* 
	* @access private
	*
	*/

	private $UserInputs = array();

	/**
	*
	* @var array. Collection of errors
	* 
	* @access private
	*
	*/

	private $Errors = array();

	/**
	*
	* @var int. Number of random characters to generate 
	* 
	* @access private
	*
	*/

	private static $NumberOfChar = 20;



	/**
	*
	* @var string. set input field name to validate
	*
	* @access  public
	*
	* @param string. Input field name
	*
	* @return object
	*
	*/

	public function Use(String $Variable)
	
	{

		if(!preg_match("/[^a-zA-Z0-9_-]/", $Variable)){ $this->Variable = $Variable;}else{ die("Invalid Variable Name (". $Variable.')');}

		$this->Sanitize();
		
		return $this;

	}

	/**
	*
	* @var string. set the string length
	*
	* @access public
	*
	* @param int. Minimum number of chars, int. Max number of chars, string. Error message 
	*
	* @return 
	*
	*/

	public function Length(int $Min, int $Max, string $ErrorMessage)

	{

		$StringLength = ( self::VariableExist() == true ) ? strlen(trim($_POST[$this->Variable])) : self::VariableNotExistBreak();

		$Result = ( ( $StringLength >= $Min) && ($StringLength <=$Max ) ) ? self::Stamp() : $ErrorMessage; 

		return Self::StampOrErrorThenChain($Result, $ErrorMessage);

	}


	/**
	*
	* @var string. Set whether input is a required field
	*
	* @access public
	*
	* @param string. error message
	*
	* @return 
	*
	*/

	public function Required(string $ErrorMessage)

	{

		$VariableExist = ( self::VariableExist() == true ) ? true : self::VariableNotExistBreak();

		$Result = (trim($_POST[$this->Variable]) != "") ? self::Stamp() : $ErrorMessage; 

		return Self::StampOrErrorThenChain($Result, $ErrorMessage);

	}


	/**
	*
	* @var string. Check if two texts are the same
	*
	* @access public
	*
	* @param string. Text to check agains, string. error message
	*
	* @return 
	*
	*/

	public function Match($Name, string $ErrorMessage)

	{
		
		$VariableExist = ( self::VariableExist() == true ) ? true : self::VariableNotExistBreak();

		$Result = ($_POST[$this->Variable] == $_POST[$Name]) ? self::Stamp() : $ErrorMessage; 

		return Self::StampOrErrorThenChain($Result, $ErrorMessage);

	}

	/**
	*
	* @var string. Regular expression to match user input
	*
	* @access  public
	*
	* @param  string. User defined regular expression, string. error message
	*
	* @return 
	*
	*/

	public function Regex($Rule, $ErrorMessage)

	{

		$VariableExist = ( self::VariableExist() == true ) ? true : self::VariableNotExistBreak();

		$Result = ( preg_match($Rule,trim($_POST[$this->Variable])) )? self::Stamp():$ErrorMessage;

		return Self::StampOrErrorThenChain($Result, $ErrorMessage);

	}


	/**
	*v
	* @var string. Validated email
	*
	* @access public
	*
	* @param string. Error message.
	*
	* @return 
	*
	*/

	public function Mail($ErrorMessage)

	{

		$VariableExist = ( self::VariableExist() == true ) ? true : self::VariableNotExistBreak();

 		$Result = (preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z-]{2,15}+.[a-zA-Z0-9]{2,4}$/",trim($_POST[$this->Variable])) )? self::Stamp():$ErrorMessage;

		return Self::StampOrErrorThenChain($Result, $ErrorMessage);

	}


	/**
	*
	* @var string. Check if user input exist on in a column of the table
	*
	* @access public
	*
	* @param object. table model, string. column on the table to check against, string. error message
	*
	* @return 
	*
	*/
	public function Exists( $Model, $Column, $ErrorMessage)

	{

		$VariableExist = ( self::VariableExist() == true ) ? true : self::VariableNotExistBreak();

		$this->Sanitize(); // set user input and sanitize it

		$Results = $Model->Where( [$Column => $this->UserInput] )->Get();

		$Result = (count($Results) < 1 ) ? self::Stamp():$ErrorMessage;

		return Self::StampOrErrorThenChain($Result, $ErrorMessage);

	}



	/**
	*
	* @var string. Check if there are errors
	*
	* @access public
	*
	* @param null
	*
	* @return boolean
	*
	*/

	public function AnyErrors()

	{

		$Result = count($this->Errors) > 0 ? true:false;

		return $Result;

	}

	/**
	*
	* @var string. Get all errors
	*
	* @access public
	*
	* @param null
	*
	* @return array of errors
	*
	*/

	public function Errors()

	{

		if(Self::AnyErrors()){ return $this->Errors; }

	}

	/**
	*
	* @var string. Gets all user inputs
	*
	* @access public
	*
	* @param null
	*
	* @return json. All user inputs
	*
	*/

	public function Output()
	
	{
		
		if(count($this->UserInputs) > 0) { return json_decode(json_encode($this->UserInputs)); }
		
	}

	/**
	*
	* @var string. Sanitize user inputs
	*
	* @access private
	*
	* @param null
	*
	* @return 
	*
	*/

	private function Sanitize()
	
	{

		$this->UserInput  = addslashes(htmlentities(trim($_POST[$this->Variable])));

	}

	/**
	*
	* @var string. Check if developer set input field name
	*
	* @access private
	*
	* @param null
	*
	* @return 
	*
	*/

	private function VariableNotExistBreak()

	{

		die("No Variable or Name set for this Passport. Hint: Apply Use( str Argument ) method");

	}


	/**
	*
	* @var string. Check if the test pass
	*
	* @access private
	*
	* @param null
	*
	* @return int
	*
	*/

	private function Stamp()

	{

		return 1;

	}


	/**
	*
	* @var string. Check if developer set input field name
	*
	* @access private
	*
	* @param null
	*
	* @return boolean
	*
	*/

	private function VariableExist()

	{

		$Result = (trim($this->Variable) != "") ? true : false;

		return $Result;

	}


	/**
	*
	* @var string
	*
	* @access private
	*
	* @param int. result of the check, string. error message
	*
	* @return 
	*
	*/

	private function StampOrErrorThenChain($Result, $ErrorMessage)

	{

		if($Result == self::stamp())
		{
			$this->UserInputs[$this->Variable] = $this->UserInput;
		}
		else
		{ 
			$this->Errors[] = $ErrorMessage; 
		} 

		return $this;

	}

	/**
	*
	* @var string
	*
	* @access public
	*
	* @param null
	*
	* @return string
	*
	*/

	public static function ID() 

	{
		 @session_start();
			 $RandomCharacters = bin2hex(openssl_random_pseudo_bytes(self::$NumberOfChar));
			 $HTTP_USER_AGENT = md5($_SERVER['HTTP_USER_AGENT']);
			 $SERVER_ADDR = md5($_SERVER['SERVER_ADDR']);
			 $ID =  md5($HTTP_USER_AGENT.'-'.$RandomCharacters.'-'.$SERVER_ADDR);
			 $_SESSION['PASSPORT_ID'] = $ID;
			 $_SESSION['PASSPORT_AGENT'] = $HTTP_USER_AGENT;
			 $_SESSION['PASSPORT_ADDRESS'] = $SERVER_ADDR;
			 $_SESSION['PASSPORT_CHAR'] = $RandomCharacters;
		 return $ID;

	}

	/**
	*
	* @var string
	*
	* @access private
	*
	* @param string. hidden input value
	*
	* @return null
	*
	*/

	public static function Verify($_ID)

	{
		 @session_start();
		  
		 $ID =  md5($_SESSION['PASSPORT_AGENT'].'-'.$_SESSION['PASSPORT_CHAR'].'-'.$_SESSION['PASSPORT_ADDRESS']);

		 $Result = ($ID == $_ID) ? true:false;

		 if($Result == false) { session_destroy();}

	}

}

?>








<?php


##USAGE ################################################################ 
 SIMPLE TUTORIAL ON PDB 							
########################################################################

/*if(isset($_POST['submit']))

{
	require_once "pdb.php";
	$DB = new PDB("babyjay");
	$StudentsTable = $DB->Use("students");


	$Passport = new Passport();
	

	$Passport->Use("email")
			 ->Mail("Input Correct Email")
			 ->Exists( $StudentsTable, "name", " Sorry email exists. choose another");

	$Passport->Use("email2")
			 ->Mail("Input Correct Email")
			 ->Match("email", "Sorry emails do not match");


	$Passport->Use("username")
			 ->Required( "Username is required")
			 ->Length(3,10, "Username must be between 3 and 10")
			 ->Regex("/[a-zA-Z0-9 ]/", "Sorry we only accept a-zA-Z0-9 and white space");


	if($Passport->AnyErrors())
	{
		echo "<pre>";
		print_r($Passport->Errors());
	}
	else
	{
		$Input = $Passport->Output();
		echo "Email : ". $Input->email. "<br>";
		echo "Email Repeat : ". $Input->email2. "<br>";
		echo "Username : ". $Input->username. "<br>";
	}

}
*/

?>

<!-- <form action="" method="post">
	<input type="text" name="email" placeholder="Email ">
	<input type="text" name="email2" placeholder="Repeat Email">
	<input type="text" name="username" placeholder="username">
	<input type="hidden" name="PassportID" value="<?php //echo Passport::ID();?>">
	<input type="submit" name="submit">
</form> -->



