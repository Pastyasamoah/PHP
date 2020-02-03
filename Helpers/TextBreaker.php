<?php 

/**
*
* Breaking sentences down based on the number of words specified
* @package TextBreaker
* @author asamoah Pasty <pastyasamoah13@gmail.com> +233 546116102
* @version 1.0
* @since 3rd Feb, 2020
*
*
*/


class TextBreaker

{
	/**
	*
	* @var array. Text initially broken down
	* 
	* @access private
	*
	*/

	private $BrokenDownTextArray = [];

	/**
	*
	* @var array. Final breakdown 
	* 
	* @access private
	*
	*/

	private $BrokenDownText = [];

	/**
	*
	* @var string. Text to break down 
	* 
	* @access private
	*
	*/

	private $Text = "";


	/**
	*
	* @var string. constructor
	*
	* @access  public
	*
	* @param string. Text to break down
	*
	* @return object
	*
	*/

	public function __construct($Text)

	{

		$this->Text = $Text; // text to break down

		return $this;

	} 


	/**
	*
	* @var string
	*
	* @access  public
	*
	* @param int. Number of syllables needed 
	*
	* @return object
	*
	*/

	public function Break($NumberOfSyllables = 3)

	{

		$ArrayOfWords = explode(" ", $this->Text); 
		$this->BrokenDownTextArray = array_chunk($ArrayOfWords, $NumberOfSyllables);
		$this->LoopBrokenDownTextArray(); // loop through broken down array
		return $this;

	}


	/**
	*
	* @var string
	*
	* @access  public
	*
	* @param null. 
	*
	* @return array
	*
	*/

	public function Get()

	{

		return array_unique($this->BrokenDownText);

	}


	/**
	*
	* @var string
	*
	* @access  private
	*
	* @param null. 
	*
	* @return null
	*
	*/

	private function LoopBrokenDownTextArray()

	{
		foreach ($this->BrokenDownTextArray as $value) 
		{
			$this->BrokenDownText[] = implode(" ", $value);
		}

	}




}






// Usage





/*$TextToBreakDown = "Hello world, am Pasty Asamoah from Ghana. I love to code. At least, it gets me thinking!";

$Breaker = new TextBreaker($TextToBreakDown);

$Result = $Breaker->Break(3)->Get(); // group in 3 words

print_r($Result);*/






/*Array
(
    [0] => Hello world, am 
    [1] => Pasty Asamoah from
    [2] => Ghana. I love
    [3] => to code. At
    [4] => least, it gets
    [5] => me thinking!
)
*/