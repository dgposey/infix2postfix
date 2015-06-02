<?php
	class Stack{
		var $stack;
		
		function __construct(){
			$this->stack = array();
		}
		
		function push($element){
			array_push($this->stack, $element);
		}
		
		function pop(){
			return array_pop($this->stack);
		}
		
		function peek(){
			return $this->stack[count($this->stack) - 1];
		}
		
		function dump(){
			$ret = "<table>";
			
			for($i = count($this->stack) - 1 ; $i >= 0 ; $i--){
				$ret .= "<tr><td>{$this->stack[$i]}</td></tr>";
			}
			
			$ret .= "</table>";
			return $ret;
		}
	}
?>