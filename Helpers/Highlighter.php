<?php 

/**
*
* Search and highlights phrases
* @package Highlighter
* @author asamoah Pasty <pastyasamoah13@gmail.com> +233 546116102
* @version 1.0
* @since 3rd Feb, 2020
*
*
*/

class Highlighter

{

	/**
	*
	* @var string. Highlighting coloe
	* 
	* @access private
	*
	*/

	private $Color = null;

	/**
	*
	* @var string. The text to highligh
	* 
	* @access private
	*
	*/

	private $StringToHighlight = "";

	/**
	*
	* @var string. Text from which you want to highligh
	* 
	* @access private
	*
	*/

	private $StringPool = "";

	/**
	*
	* @var array. Highlighted string
	* 
	* @access private
	*
	*/

	private $HighlightedSentenceArray = array();


	/**
	*
	* @var string. constructor
	*
	* @access  public
	*
	* @param string. text from which to highlight, string. color
	*
	* @return object
	*
	*/
	public function __construct($Sentence, $Color = "yellow")

	{

		$this->Color = strtolower(trim($Color));
		$this->StringPool = strtolower(trim($Sentence));
		return $this;

	}


	/**
	*
	* @var string. finds the text to highligh
	*
	* @access  public
	*
	* @param string. the actual text to highlight
	*
	* @return object
	*
	*/

	public function Find($String)

	{

		$this->StringToHighlight = strtolower(trim($String));

		return $this;

	}


	/**
	*
	* @var string. highlight
	*
	* @access  public
	*
	* @param null
	*
	* @return object
	*
	*/

	public function Highlight()

	{
		// \b is the exact boundary match

		if($this->ParamsExist())

		{
			
			$Highlighted = $this->SetHighlightParams();

			$Caught = 0;

			$Result = preg_replace("/\b$this->StringToHighlight\b/",$Highlighted,$this->StringPool, -1, $Caught);

			$this->HighlightedSentenceArray[] = [$Result,$Caught];

			return $this;

		}
		else
		{
			die("No sentence to check against. Use the Find(str) method");
		}
		

	}

	/**
	*
	* @var string. Get highlighted text and the number of elements found
	*
	* @access  public
	*
	* @param null
	*
	* @return highlighted text array
	*
	*/
	public function Get()

	{

		return $this->HighlightedSentenceArray;

	}

	/**
	*
	* @var string. sets the highligh parameters
	*
	* @access  private
	*
	* @param null
	*
	* @return html
	*
	*/

	private function SetHighlightParams()

	{

		return "<span style=background-color:".$this->Color.">".$this->StringToHighlight."</span>";

	}


	/**
	*
	* @var string. check if string to highligh exists
	*
	* @access  private
	*
	* @param null
	*
	* @return bool
	*
	*/

	private function ParamsExist()

	{
		return ($this->StringToHighlight !="") ? true:false;
		
	}


}









// Usage

/*$text_to_highlight = "understand";

$Sentence = "In the beginning was the word. understand In the hello world was java. In th was me beginning
So this is how we began our story. so it was not easy at all, So this is how we began our story. so it was not easy at all. So lorem ipsum was the beginning of the entire article before you tried to copy it
from pasty asamoah's account. word Any fool can know. The point is to understand ~ Albert Einstein";


$Highlighter = new Highlighter($Sentence, "blue");

$Result = $Highlighter->find($text_to_highlight)->Highlight()->Get();

print_r($Result);*/






?>