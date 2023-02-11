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
print_r($list);

if(strcmp($list[0][0], ".IPPcode23")){
    fwrite(STDERR, "Chybejici hlavicka souboru!\n");
    exit(21);
}
?> 