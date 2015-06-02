<?php
	require_once "Stack.php";
	
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
	
	
	class Operator{
		private $operator;
		private $precedence;
		
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
		
		function get_operator(){
			return $this->operator;
		}
		
		function get_precedence(){
			return $this->precedence;
		}
	}
	
	
	class InvalidOperatorException extends Exception {
		function __construct($operator){
			parent::__construct("Invalid operator: " . $operator);
		}
	}
?>