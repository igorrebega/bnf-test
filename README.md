# Calculator for BNF
Math calculator for expression with BNF:
```
<expr>   ::= <term> | <term> <add> <term>
<term>   ::= <factor> | <factor> <mult> <factor>
<factor> ::= <neg> | - <neg>
<neg>    ::= <number> | ( <expr> )
<add>    ::= + | -
<mult>   ::= * | /
<number> ::= <digit> | <number> <digit>
<digit>  ::= 0 | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9
```