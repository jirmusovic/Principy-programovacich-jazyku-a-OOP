<?php
include 'instructions.php';
$stderr = fopen('php://stderr', 'w');
$stdin = fopen('php://stdin', "r") or die("Unable to open file!");
$list = array();
global $vars;
ini_set('display_errors', 'stderr');
function err($code = 23, $msg = "Chybny pocet argumentu nebo spatny typ!"){
    fwrite(STDERR, "$msg\n");
    exit($code);
}

while(!feof($stdin)){
    $one_by_one = fgets($stdin);

    $comments = strpos($one_by_one, "#");
    
    if($comments !== false){
        $one_by_one = substr($one_by_one, 0, $comments);
    }

    $trimmed = trim($one_by_one);

    while(str_contains($trimmed, '\t')){
        $trimmed = str_replace("\t", " ", $trimmed);
    }

    while(str_contains($trimmed, '  ')){
        $trimmed = str_replace("  ", " ", $trimmed);
    }
    if(!empty($trimmed)){
        $finished = explode(" ", $trimmed);
        array_push($list, $finished);
    }
    
}

if(strcmp($list[0][0], ".IPPcode23") || count($list[0]) != 1){
    fwrite(STDERR, "Chybejici nebo chybna hlavicka souboru!\n");
    exit(21);
}



\array_splice($list, 0, 1);

//print_r($list);

//regex
$var = "/^((GF)|(LF)|(TF))@([a-z]|[A-Z]|([_$&%*!?])|(-))([_$&%*!?]|[0-9]|[a-z]|[A-Z]|(-))*$/";
$int = "/^(int@([+-]?[1-9]\d*|0))$/";
$bool = "/^bool@(true|false)$/";
$string = "/^string@(([\\\\][0-9]{3})|([^#\\\\]))*$/";
$nil = "/^nil@nil$/";
$label = "/^([a-z]|[A-Z]|([_$&%*!?])|(-))([_$&%*!?]|[0-9]|[a-z]|[A-Z]|(-))*$/";


function is_label($arg){
    global $label;
    if(preg_match($label, $arg) && strcmp($arg, "bool") && strcmp($arg, "string") && strcmp($arg, "int"))
        return true;
    return false;
}
function get_type($arg){
    global $var;
    global $int;
    global $bool;
    global $string;
    global $nil;
    global $label;
    if(preg_match($var, $arg))
        return types::var;
    if(preg_match($int, $arg))
        return types::int;
    if(preg_match($bool, $arg))
        return types::bool;
    if(preg_match($string, $arg))
        return types::string;
    if(preg_match($nil, $arg))
        return types::nil;
    if(is_label($arg))
        return types::label;
    if(!strcasecmp($arg, "int") || !strcasecmp($arg, "bool") || !strcasecmp($arg, "string"))
        return types::type;
    
    return types::nomatch;
}


function is_symbol($arg){
    $type = get_type($arg);
    if($type == types::var || $type == types::int || $type == types::bool || $type == types::string || $type == types::nil)
        return true;

    return false;
}




$list_cnt = count($list);
for($i = 0; $i < $list_cnt; $i++){
    if(in_array(strtoupper($list[$i][0]), $vars, true)){
        $instr = $list[$i][0];
        //porovnej s instrukcemi bez atributu
        if(!strcasecmp($instr, "createframe") || !strcasecmp($instr, "pushframe") || !strcasecmp($instr, "popframe") || !strcasecmp($instr, "return") || !strcasecmp($instr, "break")){
            if(count($list[$i]) != 1){
                err();
            }
        }
        //instrukce s jednou promennou typu var
        elseif(!strcasecmp($instr, "defvar") || !strcasecmp($instr, "pops")){
            if(count($list[$i]) != 2 || !preg_match($var, $list[$i][1])){
                err();
            }                                                      
        }
        //instrukce s jednou promennouo typu label
        elseif(!strcasecmp($instr, "call") || !strcasecmp($instr, "label") || !strcasecmp($instr, "jump")){
            if(count($list[$i]) != 2 || !is_label($list[$i][1])){
                err();
            }    
        }
        //instrukce s jednou promennou typu symbol
        elseif(!strcasecmp($instr, "write") || !strcasecmp($instr, "exit") || !strcasecmp($instr, "pushs") || !strcasecmp($instr, "dprint")){
            if(count($list[$i]) != 2 || !is_symbol($list[$i][1])){
                //print($i);
                err();
            }    
        }
        //instrukce s dvema promennymi typu vas a symbol
        elseif(!strcasecmp($instr, "move") || !strcasecmp($instr, "int2char") || !strcasecmp($instr, "strlen") || !strcasecmp($instr, "type") || !strcasecmp($instr, "not")){
            if(count($list[$i]) != 3 || !is_symbol($list[$i][2]) || !preg_match($var, $list[$i][1])){
                //print("here\n".count($list[$i]).is_symbol($list[$i][2]));
                err();
            }
        }
        elseif(!strcasecmp($instr, "add") || !strcasecmp($instr, "sub") || !strcasecmp($instr, "mul") || !strcasecmp($instr, "idiv") 
        || !strcasecmp($instr, "lt") || !strcasecmp($instr, "gt") || !strcasecmp($instr, "eq") || !strcasecmp($instr, "stri2int") 
        || !strcasecmp($instr, "getchar") || !strcasecmp($instr, "and") || !strcasecmp($instr, "or") 
        || !strcasecmp($instr, "concat") || !strcasecmp($instr, "setchar")){
            if(count($list[$i]) != 4 || !is_symbol($list[$i][2]) || !is_symbol($list[$i][3]) || !preg_match($var, $list[$i][1])){
                err();
            }
        }
        elseif(!strcasecmp($instr, "read")){
            if(count($list[$i]) != 3 || get_type($list[$i][2]) != types::type || !preg_match($var, $list[$i][1])){
                err();
            }
        }
        elseif(!strcasecmp($instr, "jumpifeq") || !strcasecmp($instr, "jumpifneq")){
            if(count($list[$i]) != 4 || !preg_match($label, $list[$i][1]) || !is_symbol($list[$i][2]) || !is_symbol($list[$i][3])){
                err();
            }  
        }
    }
    else{
        err(22, "Neznamy nebo chybny operacni kod!");
    }
}


$xml = new XMLWriter();
$xml->openMemory();
$xml->startDocument('1.0', 'UTF-8');
$xml->setIndent(true);
$xml->startElement('program');
$xml->writeAttribute('language', "IPPcode23");


for($i=0; $i < count($list); $i++){
    $row = $list[$i];
    $xml->startElement('instruction');
    $xml->writeAttribute('order', $i+1);
    $xml->writeAttribute('opcode', strtoupper($row[0]));
    for($j=1; $j < count($row); $j++){
        $xml->startElement('arg'.$j);
        $text;
        switch(get_type($row[$j])){
            case types::int:
                $text = "int";
                $row[$j] = str_replace("int@", "", $row[$j]);
                break;
            case types::bool:
                $text = "bool";
                $row[$j] = str_replace("bool@", "", $row[$j]);
                break;
            case types::string:
                $text = "string";
                $row[$j] = str_replace("string@", "", $row[$j]);
                break;
            case types::nil:
                $text = "nil";
                $row[$j] = str_replace("nil@", "", $row[$j]);
                break;
            case types::label:
                $text = "label";
                break;
            case types::type:
                $text = "type";
                break;
            case types::var:
                $text = "var";
                break;
        }
        $xml->writeAttribute('type', $text);
        $xml->text($row[$j]);
        $xml->endElement();
    }
    $xml->endElement();
}

$xml->endElement();
fwrite(STDOUT, $xml->flush());
exit(0);
?>