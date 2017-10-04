<?php

namespace App\Services;

class Calculator
{
    /**
     * @var string
     */
    private $in = '';

    /**
     * BnfParser constructor.
     * @param $in
     */
    public function __construct($in)
    {
        $this->in = str_replace(' ', '', $in);
        $this->checkInput();
    }

    /**
     * Get the result of calculation
     * @return mixed
     */
    public function result()
    {
        $result = $this->evaluate($this->in);
        if (!$result) {
            return false;
        }
        return $result * 1;
    }

    /**
     * @param $in
     * @return mixed
     */
    private function evaluate($in)
    {
        $result = $this->evaluateParenthesis($in);
        $result = $this->evaluateMath($result);
        return $this->evaluateDoubleMinus($result);
    }

    /**
     * @param $in
     * @return mixed
     */
    public function evaluateMath($in)
    {
        $in = $this->evaluateOperation('/', $in);
        $in = $this->evaluateOperation('*', $in);
        $in = $this->evaluateOperation('+', $in);
        $in = $this->evaluateOperation('-', $in);

        while ($this->haveMathOperations($in)) {
            $in = $this->evaluateMath($in);
        }
        return $in;
    }

    /**
     * @param $in
     * @return mixed
     */
    public function evaluateParenthesis($in)
    {
        while ($content = $this->findParenthesis($in)) {
            $result = $this->evaluateMath($content);
            $in = str_replace("(" . $content . ")", $result, $in);
        }
        return $in;
    }

    /**
     * @param $in
     * @return bool
     */
    private function haveMathOperations($in)
    {
        return (bool)preg_match('/\(?\d\)?[\+,\*,\/,-]-?\(?\d\)?/', $in);
    }

    /**
     * @param $symbol
     * @param $content
     * @return mixed
     */
    private function evaluateOperation($symbol, $content)
    {
        while ($operation = $this->findOperation($content, $symbol)) {
            $content = str_replace("$operation[0]" . $symbol . "$operation[1]", $this->doOperation($operation[0], $operation[1], $symbol), $content);
        }

        return $content;
    }

    /**
     * @param $in
     * @return mixed
     */
    private function evaluateDoubleMinus($in)
    {
        $result = str_replace('--', '', $in);
        if (strpos($in, '--') !== false) {
            $result = $this->evaluateDoubleMinus($result);
        }
        return $result;
    }

    /**
     * @param $one
     * @param $two
     * @param $symbol
     * @return float|int
     */
    private function doOperation($one, $two, $symbol)
    {
        switch ($symbol) {
            case '*':
                return $one * $two;
                break;
            case '/':
                if ($two == 0) throw  new \InvalidArgumentException('Division by zero');
                return $one / $two;
                break;
            case '+':
                return $one + $two;
                break;
            case '-':
                return $one - $two;
                break;
        }
        throw new \InvalidArgumentException('Wrong symbol: ' . $symbol);
    }

    /**
     * @param $string
     * @return mixed
     */
    private function findParenthesis($string)
    {
        preg_match('/\(([^\(]+?)\)/', $string, $matches);
        if (isset($matches[1])) {
            return $matches[1];
        }
        return false;
    }

    /**
     * @param $string
     * @param $symbol
     * @return mixed
     */
    private function findOperation($string, $symbol)
    {
        $exp = '/([-]?[0-9]+(?:\.[0-9]*)?)\\' . $symbol . '([-]?[0-9]+(?:\.[0-9]*)?)/';
        preg_match($exp, $string, $matches);

        if (isset($matches[1]) && isset($matches[2])) {
            return [$matches[1], $matches[2]];
        }
        return false;
    }

    /**
     * Rules that must be false if expression is valid
     *
     * @var array
     */
    private $checkRulesThatMustFalse = [
        '\+{2}'     => 'Cannot add',
        '\*{2}'     => 'Cannot multiply',
        '\-{3}'     => 'Cannot add',
        '\-\*'      => 'Cannot add',
        '\*\+'      => 'Cannot multiply',
        '\-\+'      => 'Cannot add',
        '-\(--'     => 'Cannot calculate',
        '^[\*\?\+]' => 'Cannot parse',
        '^\-{2}'    => 'Cannot parse',
        '\([\+\/]'  => 'Cannot calculate',
        '\)\('      => 'Invalid expression'
    ];

    /**
     * Rules that must be true if expression is valid
     *
     * @var array
     */
    private $checkRulesThatMustTrue = [
        '^[0-9\+\-\*\/\)\(]*$' => 'Bad character',
    ];

    /**
     * Check if count of parenthesis is right
     */
    private function checkParenthesis()
    {
        $open = 0;
        $close = 0;
        for ($i = 0; $i < strlen($this->in); $i++) {
            if ($this->in[$i] == '(') $open++;
            if ($this->in[$i] == ')') $close++;
        }

        switch ($close <=> $open) {
            case 1:
                throw new \InvalidArgumentException('Unexpected parentheses');
                break;
            case -1:
                throw new \InvalidArgumentException('Missing parentheses');
        }
    }

    /**
     * Check if expression is valid
     */
    private function checkInput()
    {
        foreach ($this->checkRulesThatMustFalse as $rule => $message) {
            if (preg_match('/' . $rule . '/', $this->in)) {
                throw new \InvalidArgumentException($message);
            }
        }
        foreach ($this->checkRulesThatMustTrue as $rule => $message) {
            if (!preg_match('/' . $rule . '/', $this->in)) {
                throw new \InvalidArgumentException($message);
            }
        }

        $this->checkParenthesis();
    }

}