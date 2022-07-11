<?php

function debug($array, $level = 1){
    if(!is_array($array)){
        echo gettype($array). '(' . $array . ')';
    } else {
        $prefix = "";
        if($level > 1){
            $prefix = str_repeat("&nbsp;&nbsp;", $level);
        }

        echo "$prefix array(".count($array). ") => { <br>";
        $level++;
        foreach($array as $key => $value){
            if(is_array($value)){
                echo "$prefix [$key] => { <br>";
                debug($value, $level);
                echo "$prefix } <br>";
            } else {
                $type = gettype($value);
                echo "$prefix $prefix [$key] => ". $type. "($value) <br>";
            }
        }
        echo "$prefix } <br>";
    }
}

?>