<?php
	/**
	 * An implementation of a simple stack.
	 */
	class Stack{
		var $stack;
		
		/**
		 * class instance constructor
		 */
		function __construct(){
			$this->stack = array();
		}
		
		/**
		 * Push an element onto the top of the stack.
		 * @param element	element to push on
		 */
		function push($element){
			array_push($this->stack, $element);
		}
		
		/**
		 * Pop a single element from the top of the stack.
		 * @return the item from the top of the stack
		 */
		function pop(){
			return array_pop($this->stack);
		}
		
		/**
		 * View the item at the top of the stack, but do not modify it.
		 * @return the item from the top of the stack
		 */
		function peek(){
			return $this->stack[count($this->stack) - 1];
		}
		
		/**
		 * Dump the contents of this stack, formatted as an HTML table.
		 */
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