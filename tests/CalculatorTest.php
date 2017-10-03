<?php

class ExampleTest extends TestCase
{
    public function testNullInMustReturnNull()
    {
        $this->expectRes('0', 0);
    }

    public function testWrongExpressionMustThrowError()
    {
        $this->expectException(InvalidArgumentException::class);

        $calculator = new \App\Services\Calculator("((1)");
        $calculator->result();
    }

    public function testWrongExpressionWith3Minus()
    {
        $this->expectException(InvalidArgumentException::class);

        $calculator = new \App\Services\Calculator("3---3");
        $calculator->result();
    }

    public function testArchesMustRunFirst()
    {
        $this->expectRes('(2+2)*2', 8);
    }

    public function testSubArchesMustRunFirst()
    {
        $this->expectRes('(2+2*(2))*2', 12);
    }

    public function testBigExpression()
    {
        $this->expectRes('200/-8+2*(3+5)-19', -28);
    }

    public function testAnotherBigExpr()
    {
        $this->expectRes('200/-8+2*(((3+5)-19))', -47);
    }

    public function testMinusBeforeArches()
    {
        $this->expectRes('-(4+3+3)', -10);
    }

    /**
     * @param $in
     * @param $out
     */
    private function expectRes($in, $out)
    {
        $calculator = new \App\Services\Calculator($in);

        $this->assertEquals($out, $calculator->result());
    }
}
