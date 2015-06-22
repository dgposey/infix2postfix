<?php
	// MIME type returned is text/json
	header("Content-type: text/json");
	// relies on OperatorStack type
	require_once "OperatorStack.php";
	
	$debug = false;		// set to true for helpful output
	set_error_handler("sys_error_handler");
	register_shutdown_function("fatal_error_handler");
	
	if($debug){
?>
		<style type="text/css">
			table {
				border: 1px solid #000000;
			}
			td {
				border: 1px dotted #000000;
				margin: 1px;
				min-width: 100px;
				min-height: 20px;
			}
			.debug-box {
				border: 1px solid red;
				margin: 3px 0;
			}
		</style>
<?php
	}
	
	$expr = $_GET["expr"];
	
	// build a new object to be returned using stdClass
	$ret = new stdClass();
	$ret->infix = $expr;
	$ret->postfix = "";
	
	// set up user error in return object
	$ret->userError = new StdClass();
	$ret->userError->isError = false;
	$ret->userError->message = "";
	
	// set up system error in return object
	$ret->sysError = new StdClass();
	$ret->sysError->isError = false;
	$ret->sysError->message = "";
	
	// start new OperatorStack
	$op_stack = new OperatorStack();
	$op_stack->push("EOI");
	
	// parse expression; handle errors gracefully
	try{
		if($debug) echo "<div class='debug-box'>INITIAL: {$op_stack->dump()}</div>";
		
		// iterate through each character of input string
		for($i = 0 ; $i < strlen($expr) ; $i++){
			$char = $expr[$i];
			if(is_numeric($char)){
				// add numbers directly to output string as they're encountered
				$ret->postfix .= $char . " ";
			}else if(preg_match("/\s/", $char)){
				// ignore whitespace
				continue;
			}else if($char == ")"){
				// end of parenthetical expression; pop operators and append them to output string until a matching opening parenthesis is found
				while(true){
					$op = $op_stack->pop();
					if($op->get_operator() == "("){
						break;
					}else if($op->get_operator() == "EOI"){
						throw new InvalidExpressionException("mismatched parentheses -- more ')' than '('");
					}
					$ret->postfix .= $op->get_operator() . " ";
				}
			}else{
				// this is an operator other than a closing parenthesis
				$temp_op = new Operator($char);
				if($op_stack->peek()->get_precedence() < $temp_op->get_precedence()){
					// this has higher precedence than top of stack, so push it on
					$op_stack->push($char);
				}else{
					// this has lower precedence than top of stack, so pop top of stack first (and append it to output string), then push this one onto stack
					$ret->postfix .= $op_stack->pop()->get_operator() . " ";
					$op_stack->push($char);
				}
			}
			if($debug) echo "<div class='debug-box'>char: $char<br />postfix: {$ret->postfix} {$op_stack->dump()}</div>";
		}
		
		// pop any remaning operators off the stack
		while(true){
			$op = $op_stack->pop();
			if($op->get_operator() == "EOI"){
				break;
			}else if($op->get_operator() == "("){
				throw new InvalidExpressionException("mismatched parentheses -- more '(' than ')'");
			}
			$ret->postfix .= $op->get_operator() . " ";
			
			if($debug) echo "<div class='debug-box'>cleaning stack... {$op_stack->dump()}</div>";
		}
	}catch(InvalidOperatorException $e){
		user_error_handler($e->getMessage());
	}catch(InvalidExpressionException $e){
		user_error_handler($e->getMessage());
	}catch(Exception $e){
		sys_error_handler($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
	}
	
	echo json_encode($ret);
	
	
	
	
	
	/********************/
	/**
	 * Handle system errors.
	 */
	function sys_error_handler($errcode, $errstr, $errfile, $errline){
		global $debug;
		global $ret;
		
		if($debug) echo "<strong>Error:</strong> " . $errstr . " in " . $errfile . " on line " . $errline . "<br />";
		
		$ret->sysError->isError = true;
		$ret->sysError->message = "We're terribly sorry, but we seem to have experiened an error on our end.  How embarrasing.";
		
		echo json_encode($ret);
		
		die();
	}
	
	/**
	 * Handle user errors.
	 */
	function user_error_handler($errstr){
		global $debug;
		global $ret;
		
		if($debug) echo "<strong>Exception:</strong> " . $errstr . "<br />";
		
		$ret->userError->isError = true;
		$ret->userError->message .= $errstr . "<br />";
		
		echo json_encode($ret);
		
		die();
	}
	
	/**
	 * Handle fatal error.
	 */
	function fatal_error_handler(){
		die();
	}
	
	/**
	 * Exception thrown when an input expression is malformed.
	 */
	class InvalidExpressionException extends Exception{
		function __construct($message){
			parent::__construct("Invalid infix expression: " . $message);
		}
	}
?>