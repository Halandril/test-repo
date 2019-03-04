<?php
class ExSimpleXMLElement extends SimpleXMLElement 
{ 
	 /** 
	  * Add CDATA text in a node 
	  * @param string $cdata_text The CDATA value  to add 
	  */ 
  public function addCData($cdata_text) 
  { 
	$node= dom_import_simplexml($this); 
	$no = $node->ownerDocument; 
	$node->appendChild($no->createCDATASection($cdata_text)); 
  } 

  public function removeCData() {
	  	$node= dom_import_simplexml($this); 
		//$no = $node->ownerDocument; 

  		while ($node->hasChildNodes()) {
		    $node->removeChild($node->firstChild);
		 }
  }

  /** 
	* Create a child with CDATA value 
	* @param string $name The name of the child element to add. 
	* @param string $cdata_text The CDATA value of the child element. 
	*/ 
	 public function addChildCData($name,$cdata_text) 
	 { 
		  $child = $this->addChild($name); 
		  $child->addCData($cdata_text); 
	 } 
} 
?>