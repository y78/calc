<?php

class Calc
{
    protected $operators;
    protected $values;

    public function __construct()
    {
        $this->operators = new \SplStack();
        $this->values = new \SplStack();
    }

    public function getResult($exp)
    {
        $tokens = preg_split('/\s*(\d+|.+?)\s*/', $exp . '$', -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        foreach ($tokens as $token) {
            if (is_numeric($token)) {
                $this->values->push($token);
                continue;
            }

            $this->handle($token);
        }

        return $this->values->pop();
    }

    protected function handle($token)
    {
        while (!$this->operators->isEmpty() && $this->compare($this->operators->top(), $token)) {
            $value = $this->run($this->operators->pop());
            $this->values->push($value);
        }

        $this->operators->push($token);
    }

    protected function run($token)
    {
        list ($b, $a) = [$this->values->pop(), $this->values->pop()];

        switch ($token) {
            case '+':
                return $a + $b;
            case '-':
                return $a - $b;
            case '*':
                return $a * $b;
            case '/':
                return $a / $b;
        }

        return null; // parent::run($token)
    }

    protected function compare($left, $right)
    {
        return $this->getWeight($left) < $this->getWeight($right);
    }

    protected function getWeight($token)
    {
        if ($token == '+' || $token == '-') {
            return 5;
        }

        if ($token == '$') {
            return 10;
        }

        return 1;
    }
}

class SkobkaCalc extends Calc
{
    protected function handle($token)
    {
        if ($token == '(') {
            $this->operators->push($token);
            return;
        }
        
        if ($token == ')') {
            while (!$this->operators->isEmpty() && $this->operators->top() != '(') {
                $value = $this->run($this->operators->pop());
                $this->values->push($value);
            }

            $this->operators->pop();
            return;
        }
        
        parent::handle($token);
    }

    protected function getWeight($token)
    {
        if ($token == '(') {
            return 100;
        }

        return parent::getWeight($token);
    }
}

$calc = new SkobkaCalc();
$result = $calc->getResult('(2 + 2) * (1 + 2)');
var_dump($result);
