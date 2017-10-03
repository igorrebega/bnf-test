<?php

namespace App\Services;

class BnfParser
{
    private $terms = [];
    private $in = '';
    private $results = [];
    private $inPos = 0;
    const BREAK_CHAR = 's';

    /**
     * BnfParser constructor.
     * @param array $terms
     * @param $in
     */
    public function __construct(array $terms, $in)
    {
        $this->terms = $terms;
        $this->in = str_replace(' ', '', $in);
        $this->process('x');
//        $items = $this->parseByName('number');
//        dd($this->results);
    }

    /**
     * @param $termName
     * @return bool
     */
    public function process($termName)
    {
        $pieces = explode('|', $this->terms[$termName]);
        foreach ($pieces as $key => $piece) {
            for ($i = 0; $i < strlen($piece); $i++) {
                echo $piece[$i];
                if ($this->isTerminal($piece[$i])) {
                    if ($this->isMatch($piece[$i])) {
                        $this->inPos++;
                        return true;
                    }
                } elseif ($piece[$i] == self::BREAK_CHAR) {
                    break;
                } else {
                    $this->process($piece[$i]);
                }
                break;
            }
        }
        return false;
    }

    /**
     * @param $char
     * @return bool
     */
    private function isTerminal($char)
    {
        return !isset($this->terms[$char]);
    }

    private function parseByName($name)
    {
        return $this->parseTerm($name, $this->terms['number'], $this->in);
    }

    /**
     * @param $termContent
     * @param $in
     * @return array
     * @internal param $name
     */
    private function parseTerm($termName, $termContent, $in)
    {
        $pieces = explode(' | ', $termContent);

        foreach ($pieces as $key => $piece) {
            $terms = $this->findTermNames($piece);
            foreach ($terms as $term) {
                $changed = $this->parseTerm($term, $this->terms[$term], $in);

                $pieces[$key] = str_replace("<$term>", $changed, $pieces[$key]);
                dd($pieces[$key]);
            }
            echo $pieces[$key];
            if ($this->isMatch($in, $piece)) {
                $this->results[$termName] = $this->isMatch($in, $pieces[$key]);
            } else {
                unset($pieces[$key]);
            }
        }

        return implode(' | ', $pieces);
    }

    /**
     * @param $terminal
     * @return bool|string
     */
    private function isMatch($terminal)
    {
        $pieces = explode(' | ', $terminal);
        foreach ($pieces as $piece) {
            if (substr($this->in, $this->inPos, 1) === $piece) {
                return true;
            }
        }

        return false;
    }


    /**
     * @param $term
     * @param $string
     * @return mixed
     */
    private
    function deleteTermFromString($term, $string)
    {
        return str_replace("<$term>", $string, '');
    }

    /**
     * @param $string
     * @return mixed
     */
    private
    function findTermNames($string)
    {
        preg_match_all('/<([\w]*)>/', $string, $matches, PREG_SET_ORDER);

        $result = [];
        foreach ($matches as $match) {
            $result[] = $match[1];
        }
        return $result;
    }

}