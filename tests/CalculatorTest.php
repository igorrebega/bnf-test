<?php

use App\Services\Calculator;

class ExampleTest extends TestCase
{
    /**
     * @dataProvider validProvider
     */
    public function testValid($value, $expected)
    {
        $this->assertSame($expected, $value);
    }
//

    /**
     * @dataProvider invalidProvider
     */
    public function testInvalid($value, $msg)
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageRegExp('/^' . $msg . '/');

        (new Calculator($value))->result();
    }

    public function testNone()
    {
        $this->assertFalse((new Calculator(' '))->result());
    }

    public function invalidProvider()
    {
        return [
            ['200++10', 'Cannot add'],
            ['456**23', 'Cannot multiply'],
            ['876---987', 'Cannot add'],
            ['876-*987', 'Cannot add'],
            ['876*+987', 'Cannot multiply'],
            ['2.5+4', 'Bad character'],
            ['4-+4', 'Cannot add'],
            ['(3)(5)', 'Invalid expression'],
            ['4-(--4)', 'Cannot calculate'],
            ['4-(4+(-5/2)', 'Missing parentheses'],
            ['(4-(4)+(-5)/2', 'Missing parentheses'],
            ['*567+98899', 'Cannot parse'],
            ['24/(-567+(+98899))', 'Cannot calculate'],
            ['-(567+98899))', 'Unexpected parentheses'],
            ['ssadsd', 'Bad character'],
            ['87-5/(25/5+8-26/2)', 'Division by zero'],
            ['78+(09-007))', 'Unexpected parentheses'],
            ['78+9*7))', 'Unexpected parentheses']
        ];
    }

    public function validProvider()
    {
        $data = [];

        $values = [
            '345',
            '((2566))',
            '-789787',
            '-(-(-2566))',
            '200+12*((1/-8)+1)-19',
            '2*(-1)',
            '4-(-4)',
            '4--4',
            '200+12*((1/8)+1)-19+2-4*5+-10+(81/9-4)+2*11',
            '200+12*((1/8)+1)-19',
            '4-(4+(-5+2))',
            '(4-(4))+(-5)/2',
            '(4-(4)+(-5)/2)',
            '-(567+98899)',
            '3*(4/2*2*3)/3*9*(4*(5*20)/10)',
            '3*(4/2*2*3)/3*9*(4*(5*20)/10)+(4-(4)+(-5)+2)+(4-(4+(-5+2)))+98-87',
            '3*(4/2*(2*3)/(3*9*(4)))*(5*20)/10+(4-(4)+((-5)+2))+(4)-(4+(-5+2)+(98-87))'
        ];

        foreach ($values as $value) {
            $calc = new Calculator($value);

            if (strpos($value, '--') !== false) {
                $value = preg_replace("#--#", '+', $value);
            }

            eval("\$ret = $value;");

            $data[] = [$calc->result(), $ret];
        }

        return $data;
    }
}
