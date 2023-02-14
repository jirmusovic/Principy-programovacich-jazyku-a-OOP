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

print_r($list);

//regex
$var = "/ ^((GF)|(LF)|(TF))@([a-z]|[A-Z]|([_$&%*!?])|(-))([_$&%*!?]|[0-9]|[a-z]|[A-Z]|(-))*$/";
$label = "/^(\S^#)*/";
//$symbol = "/(((GF)|(LF)|(TF))@(([a-z])|[A-Z]|([_$&%*!?])|(-))(\S)*)*";  //dodelat bo fakt uz nevim
$int = "/^(int@[+-]?[1-9]\d*|0)$/";
$bool = "/^bool@(true|false)$/";
$string = "/^string@(([^#\\\\])|([\\\\[0-9]{3}]))*$/";
$nil = "/^nil@nil$/";
$patterns = array($int, $bool, $var, $string, $nil);

function is_symbol($arg){
    global $patterns;
    foreach($patterns as $p){
        if(preg_match($p, $arg)){
            return true;
        }
    }
    return false;
}


$list_cnt = count($list);
for($i = 0; $i < $list_cnt; $i++){
    if(in_array(strtoupper($list[$i][0]), $vars, true)){
        $instr = $list[$i][0];
        if(!strcasecmp($instr, "createframe") || !strcasecmp($instr, "pushframe") || !strcasecmp($instr, "popframe") || !strcasecmp($instr, "return") || !strcasecmp($instr, "break")){
            if(count($list[$i]) != 1){
                err();
            }
        }
        elseif(!strcasecmp($instr, "defvar") || !strcasecmp($instr, "pops")){
            if(count($list[$i]) != 2 || !preg_match($var, $list[$i][1])){
                err();
            }                                                       //haha
        }
        elseif(!strcasecmp($instr, "call") || !strcasecmp($instr, "label") || !strcasecmp($instr, "jump")){
            if(count($list[$i]) != 2 || !preg_match($label, $list[$i][1])){
                err();
            }    
        }
        elseif(!strcasecmp($instr, "write") || !strcasecmp($instr, "exit") || !strcasecmp($instr, "pushs") || !strcasecmp($instr, "dprint")){
            if(count($list[$i]) != 2 || !is_symbol($list[$i][1])){
                err();
            }    
        }
    }
    else{
        err(22, "Neznamy nebo chybny operacni kod!");
    }
}

/*$list_cnt = count($list);
print("list_cnt = $list_cnt\n");
for($i = 0; $i < $list_cnt; $i++){
    $line_cnt = count($list[$i]);
    print("line_cnt $i = $line_cnt\n");
    for($j = 0; $j < $line_cnt ; $j++){
        if(stripos($list[$i][$j], "createframe") !== false || stripos($list[$i][$j], "pushframe") !== false || stripos($list[$i][$j], "popframe") !== false || stripos($list[$i][$j], "return") !== false || stripos($list[$i][$j], "break")){
            #print("jsem tu v poli [$i][$j]\n");
            if(!(!strcasecmp($list[$i][$j], "createframe") || !strcasecmp($list[$i][$j], "pushframe") || !strcasecmp($list[$i][$j], "popframe") || !strcasecmp($list[$i][$j], "return") || !strcasecmp($list[$i][$j], "break"))){
                fwrite(STDERR, "Jina lexikalni nebo syntakticka chyba!\n");
                exit(23);
            }
        }
        if(!strcasecmp($list[$i][$j], "defvar") || !strcasecmp($list[$i][$j], "pops")){
            print("1. jsem tu v poli [$i][$j]\n");
            $arr_cnt = count($list[$i]);
            $j++;
            if($arr_cnt === $j){
                #print("4. jsem tu v poli [$i][$j]\n");
                fwrite(STDERR, "Jina lexikalni nebo syntakticka chyba!\n");
                exit(23);
            }
            else if(strpos($list[$i][$j], "GF@") !== 0 && strpos($list[$i][$j], "LF@") !== 0 && strpos($list[$i][$j], "TF@") !== 0){
                #print("2. jsem tu v poli [$i][$j]\n");
                fwrite(STDERR, "Jina lexikalni nebo syntakticka chyba!\n");
                exit(23);
            }
        }
        if(!strcasecmp($list[$i][$j], "call") || !strcasecmp($list[$i][$j], "label") || !strcasecmp($list[$i][$j], "jump")){
            $arr_cnt = count($list[$i]);
            $j++;
            #print("3. jsem tu v poli [$i][$j]\n");
            if($arr_cnt === $j){
                #print("4. jsem tu v poli [$i][$j]\n");
                fwrite(STDERR, "Jina lexikalni nebo syntakticka chyba!\n");
                exit(23);
            }
        }
        if(!strcasecmp($list[$i][$j], "pushs") || !strcasecmp($list[$i][$j], "write") || !strcasecmp($list[$i][$j], "dprint")){
            $j++;
            $arr_cnt = count($list[$i]);
            if($arr_cnt === $j){
                #print("4. jsem tu v poli [$i][$j]\n");
                fwrite(STDERR, "Jina lexikalni nebo syntakticka chyba!\n");
                exit(23);
            }
        }
    }
}*/



?> 