<?php
	require_once "Stack.php";
	
	/**
	 * An extension of Stack specifically for arithmetical operators, represented by Operator.
	 */
	class OperatorStack extends Stack{
		function push($operator){
			$op = new Operator($operator);
			parent::push($op);
		}
		
		function dump(){
			$ret = "<table>";
			
			for($i = count($this->stack) - 1 ; $i >= 0 ; $i--){
				$ret .= "<tr><td>" . $this->stack[$i]->get_operator() . "</td><td>" . $this->stack[$i]->get_precedence() . "</td></tr>";
			}
			
			$ret .= "</table>";
			return $ret;
		}
	}
	
	
	/**
	 * An object that represents a single operator.
	 */
	class Operator{
		private $operator;
		private $precedence;
		
		/**
		 * class instance constructor
		 * sets precedence based on input
		 */
		function __construct($operator){
			$this->operator = $operator;
			
			$prec = 0;
			switch($operator){
				case "EOI":
					$prec = 0;
					break;
				case "(":
					$prec = 1;
					break;
				case "+":
					$prec = 2;
					break;
				case "-":
					$prec = 2;
					break;
				case "*":
					$prec = 3;
					break;
				case "/":
					$prec = 3;
					break;
				default:
					throw new InvalidOperatorException($operator);
					break;
			}
			
			$this->precedence = $prec;
		}
		
		/**
		 * @return operator
		 */
		function get_operator(){
			return $this->operator;
		}
		
		/**
		 * @return operator
		 */
		function get_precedence(){
			return $this->precedence;
		}
	}
	
	
	/**
	 * An exception thrown when an invalid text representation of an arithmetical operator is passed into Operator's constructor.
	 */
	class InvalidOperatorException extends Exception {
		function __construct($operator){
			parent::__construct("Invalid operator: " . $operator);
		}
	}
?>