<?php


$vars = array(
            "MOVE",
            "CREATEFRAME",
            "PUSHFRAME",
            "POPFRAME",
            "DEFVAR",
            "CALL",
            "RETURN",
            "PUSHS",
            "POPS",
            "ADD",
            "SUB",
            "MUL",
            "IDIV",
            "LT",
            "GT",
            "EQ",
            "AND",
            "OR",
            "NOT",
            "INT2CHAR",
            "STRI2INT",
            "READ",
            "WRITE",
            "CONCAT",
            "STRLEN",
            "GETCHAR",
            "SETCHAR",
            "TYPE",
            "LABEL",
            "JUMP",
            "JUMPIFEQ",
            "JUMPIFNEQ",
            "EXIT",
            "DPRINT",
            "BREAK"
        );

enum types{
    case int;
    case bool;
    case string;
    case nil;
    case label;
    case type;
    case var;
    case ippcode;
    case opcode;
    case nomatch;
}


