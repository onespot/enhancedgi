<?php require_once('../control/issues.php');class XmlController extends IssuesController{	public $xmlKeyMap;	function XmlController(){		parent::IssuesController();		// Create a map of xml ids and issue ids (keys)		$this->xmlKeyMap=array();		$id=0;		foreach($this->issuesByUser as $key => $val){ 			$id++;			foreach($val as $issue){ 				$id++;				$this->xmlKeyMap[$issue->id]=$id;			}		}		header("Content-type: text/xml"); 		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";	}}function xmlEscape($string) {    return str_replace(array('&', '<', '>', '\'', '"'), array('&amp;', '&lt;', '&gt;', '&apos;', '&quot;'), $string);}