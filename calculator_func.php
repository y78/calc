<?php

function calc($exp)
{
    $tokens = preg_split('/\s*(\d+|.+?)\s*/', $exp . '$', -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    $weight = array_flip(['*', '/', '-', '+', ')', '(', '$']);
    $values = $operations = [];

    foreach ($tokens as $token) {
        if (is_numeric($token)) {
            $values[] = $token;
            continue;
        }

        if ($token == '(') {
            $operations[] = $token;
            continue;
        }

        while ($operations && ($token == ')' || $weight[$token] > $weight[end($operations)])) {
            if ('(' == $operation = array_pop($operations)) {
                continue 2;
            }

            $right = array_pop($values);
            $left = array_pop($values);
            switch ($operation) {
                case '+':
                    $values[] = $left + $right;
                    break;
                case '-':
                    $values[] = $left - $right;
                    break;
                case '*':
                    $values[] = $left * $right;
                    break;
                case '/':
                    $values[] = $left / $right;
                    break;
            }
        }

        $operations[] = $token;
    }

    return reset($values);
}

var_dump(calc('9*(9-1)+2*(6-((8-3)*4)*2)/4+1'));