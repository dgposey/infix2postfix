<?php
	header("Content-type: text/json");
	require_once "OperatorStack.php";
	
	$debug = false;
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
	//$expr = "3 + 2 * (4 - (5 + 4)) - 3 * 2";
	
	$ret = new stdClass();
	$ret->infix = $expr;
	$ret->postfix = "";
	
	$ret->userError = new StdClass();
	$ret->userError->isError = false;
	$ret->userError->message = "";
	
	$ret->sysError = new StdClass();
	$ret->sysError->isError = false;
	$ret->sysError->message = "";
	
	$op_stack = new OperatorStack();
	$op_stack->push("EOI");
		
	try{
		if($debug) echo "<div class='debug-box'>INITIAL: {$op_stack->dump()}</div>";
		
		for($i = 0 ; $i < strlen($expr) ; $i++){
			$char = $expr[$i];
			if(is_numeric($char)){
				$ret->postfix .= $char . " ";
			}else if(preg_match("/\s/", $char)){
				continue;
			}else if($char == ")"){
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
				$temp_op = new Operator($char);
				if($op_stack->peek()->get_precedence() < $temp_op->get_precedence()){
					$op_stack->push($char);
				}else{
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
	function sys_error_handler($errcode, $errstr, $errfile, $errline){
		global $debug;
		global $ret;
		
		if($debug) echo "<strong>Error:</strong> " . $errstr . " in " . $errfile . " on line " . $errline . "<br />";
		
		$ret->sysError->isError = true;
		$ret->sysError->message = "We're terribly sorry, but we seem to have experiened an error on our end.  How embarrasing.";
		
		echo json_encode($ret);
		
		die();
	}
	
	function user_error_handler($errstr){
		global $debug;
		global $ret;
		
		if($debug) echo "<strong>Exception:</strong> " . $errstr . "<br />";
		
		$ret->userError->isError = true;
		$ret->userError->message .= $errstr . "<br />";
		
		echo json_encode($ret);
		
		die();
	}
	
	function fatal_error_handler(){
		//echo ":(";
		die();
	}
	
	class InvalidExpressionException extends Exception{
		function __construct($message){
			parent::__construct("Invalid infix expression: " . $message);
		}
	}
?>