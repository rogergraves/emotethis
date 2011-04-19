<?php

class A{
    function __construct() {
	print "Construct 1\n";
    }

    function __construct($survey_code) {
	print "Construct 2\n";
    }
}

$a1 = new A();
$a2 = new A('test');

?>