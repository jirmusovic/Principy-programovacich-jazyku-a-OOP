instructions.php
<?php

$stderr = fopen('php://stderr', 'w');
$stdin = fopen('php://stdin', "r") or die("Unable to open file!");
$list = array();

while(!feof($stdin)){
    $one_by_one = fgets($stdin);
    $trimmed = str_replace("#", " #", $one_by_one);
    $trimmed = trim($trimmed);

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

if(strcmp($list[0][0], ".IPPcode23")){
    fwrite(STDERR, "Chybejici nebo chybna hlavicka souboru!\n");
    exit(21);
}

\array_splice($list, 0, 1);

print_r($list);
$list_cnt = count($list);
print("list_cnt = $list_cnt\n");
for($i = 0; $i < $list_cnt; $i++){
    $line_cnt = count($list[$i]);
    print("line_cnt $i = $line_cnt\n");
    for($j = 0; $j < $line_cnt ; $j++){
        if(stripos($list[$i][$j], "createframe") !== false || stripos($list[$i][$j], "pushframe") !== false || stripos($list[$i][$j], "popframe") !== false || stripos($list[$i][$j], "return") !== false || stripos($list[$i][$j], "break")){
            print("jsem tu v poli [$i][$j]\n");
            if(!(!strcasecmp($list[$i][$j], "createframe") || !strcasecmp($list[$i][$j], "pushframe") || !strcasecmp($list[$i][$j], "popframe") || !strcasecmp($list[$i][$j], "return") || !strcasecmp($list[$i][$j], "break"))){
                fwrite(STDERR, "Jina lexikalni nebo syntakticka chyba!\n");
                exit(23);
            }
        }
        if(stripos($list[$i][$j], "defvar") !== false){
            print("jsem tu v poli [$i][$j]\n");
            $j++;
            if(strpos($list[$i][$j], "GF@") !== 0 && strpos($list[$i][$j], "LF@") !== 0 && strpos($list[$i][$j], "TF@") !== 0){
                print("jsem tu v poli [$i][$j]\n");
                fwrite(STDERR, "Jina lexikalni nebo syntakticka chyba!\n");
                exit(23);
            }
        }
        
        
    }
}



?> 