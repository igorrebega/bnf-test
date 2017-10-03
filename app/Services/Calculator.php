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
    }

    /**
     * Get the result of calculation
     * @return mixed
     */
    public function result()
    {
        $result = $this->evaluate($this->in);
        $this->checkResult($result);
        return $result;
    }

    /**
     * @param $in
     * @return mixed
     */
    private function evaluate($in)
    {
        $archesContent = $this->findArches($in);

        foreach ($archesContent as $archContent) {
            $content = $this->evaluate($archContent);
            $in = str_replace("(" . $archContent . ")", $content, $in);
        }
        $in = $this->evaluateOperation('*', $in);
        $in = $this->evaluateOperation('/', $in);
        $in = $this->evaluateOperation('+', $in);
        $in = $this->evaluateOperation('-', $in);

        while ($this->haveMathOperations($in)) {
            $in = $this->evaluate($in);
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
        $operations = $this->findOperation($content, $symbol);
        foreach ($operations as $operation) {
            $content = str_replace("$operation[0]" . $symbol . "$operation[1]", $this->doOperation($operation[0], $operation[1], $symbol), $content);
        }

        return $content;

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
    private function findArches($string)
    {
        preg_match_all('/\((.+)\)/', $string, $matches, PREG_SET_ORDER);

        $result = [];
        foreach ($matches as $match) {
            $result[] = $match[1];
        }
        return $result;
    }

    /**
     * @param $string
     * @param $symbol
     * @return mixed
     */
    private function findOperation($string, $symbol)
    {
        preg_match_all('/([-]?\d+)\\' . $symbol . '([-]?\d+)/', $string, $matches, PREG_SET_ORDER);

        $result = [];
        foreach ($matches as $match) {
            $result[] = [$match[1], $match[2]];
        }
        return $result;
    }


    /**
     * @param $res
     */
    private function checkResult($res)
    {
        if (!preg_match('/^[-]?\d+$/', $res)) {
            throw new \InvalidArgumentException('wrong input expression');
        }
    }

}