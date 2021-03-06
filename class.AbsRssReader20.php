<?php
/**
* class AbsRssReader20
*
* Parse an RSS 2.0 xml feed and retrieve the result as an associative array.
*
* @package    Abs_Xml_Rss
* @category   XML, RSS
* @author     Costin Trifan <costintrifan@yahoo.com>
* @copyright  2009 Costin Trifan
* @licence    http://en.wikipedia.org/wiki/MIT_License   MIT License
* @version    1.0
* 
* Copyright (c) 2009 Costin Trifan <http://june-js.com/>
* 
* Permission is hereby granted, free of charge, to any person
* obtaining a copy of this software and associated documentation
* files (the "Software"), to deal in the Software without
* restriction, including without limitation the rights to use,
* copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the
* Software is furnished to do so, subject to the following
* conditions:
* 
* The above copyright notice and this permission notice shall be
* included in all copies or substantial portions of the Software.
* 
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
* EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
* OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
* NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
* HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
* WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
* FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
* OTHER DEALINGS IN THE SOFTWARE.
*/
class AbsRssReader20
{
	private function __clone(){}

	// constructor
	public function __construct(){}


	/**
	* Holds the reference to the instance of the DOMDocument class
	* @type object
	*/
	protected static $_doc = null;

	/**
	* Whether or not the xml document has been loaded.
	* @type bool
	* @access private
	*/
	protected static $_loaded = FALSE;



	/**
	* Load the xml document
	* @param string $filePath  The path to the xml document
	* @return void
	*/
	final public function Load( $filePath )
	{
		if (is_null($filePath) or strlen($filePath) < 1)
			exit("Error in ".__CLASS__.'::'.__FUNCTION__.'<br/>The path to the rss file is missing!');

		// LOAD XML DOCUMENT
		self::$_doc = new DOMDocument();
		if (@self::$_doc->load($filePath))
			self::$_loaded = TRUE;
		else exit("Error: The rss file could not be opened!");
	}

	/**
	* Get the channel tags as an associative array
	* @return array
	*/
	final public function GetChannelTags()
	{
		$result = array();
		if ( ! self::$_loaded) return $result;

		$ch = self::$_doc->getElementsByTagName('channel')->item(0);
		if ( ! is_null($ch))
		{
			if ($ch->hasChildNodes())
			{
				foreach ($ch->getElementsByTagName('*') as $tag)
				{
					// do not select item tags
					if ($tag->hasChildNodes() and ($tag->tagName <> 'item'))
						$result[$tag->tagName] = html_entity_decode($tag->nodeValue, ENT_QUOTES, 'UTF-8') ;
				}
			}
		}
		return $result;
	}

	/**
	* Get the channel items as an associative array
	*
	* @param int $maxLimit  The maximum number of channel items to retrieve from the document.
	* If $maxLimit = 0 all records will be retrieved.
	* @return array
	*/
	final public function GetItems( $maxLimit = 10 )
	{
		$result = array();
		if ( ! self::$_loaded) return $result;

		$ch = self::$_doc->getElementsByTagName('channel')->item(0);
		if ( ! is_null($ch))
		{
			if ($ch->hasChildNodes())
			{
				$i = 0;
				foreach ($ch->getElementsByTagName('item') as $tag)
				{
					$result['item_'.$i] = array();
					
					foreach ($tag->getElementsByTagName('*') as $item)
						$result['item_'.$i][$item->tagName] = html_entity_decode($item->nodeValue, ENT_QUOTES, 'UTF-8') ;

					$i++;
					if ($maxLimit == $i) break;
				}
			}
		}
		return $result;
	}

	/**
	* Get all data from the rss feed as an associative array
	* @return array
	*/
	final public function GetAll()
	{
		$result = array();
		if ( ! self::$_loaded) return $result;

		$channelTags = $this->GetChannelTags();
		//$channelItems = $this->GetItems();

		 //$result = array_merge($channelTags, $channelItems);
		 
		 $result = $channelTags;

		return $result;
	}

}
// >> END
?>