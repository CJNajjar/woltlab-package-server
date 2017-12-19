<?php
namespace libs;

class XML extends \DOMDocument
{
	public function __construct()
	{
		parent::__construct("1.0");
		$this->formatOutput = true;
	}
	
	public function createSimpleAttribute($name, $value)
	{
		$attr = $this->createAttribute($name);
		$attr->value = $value;
		return $attr;
	}
}
