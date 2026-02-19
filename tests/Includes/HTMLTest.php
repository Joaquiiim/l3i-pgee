<?php

require 'Includes/HTML.php';
class HTMLTest extends PHPUnit\Framework\TestCase {

    public function testOutput()
    {
       // Capture the output
       ob_start();
       echo HTMLElem::input('text','Test','txtTest','txtTest','69');
       $output = ob_get_clean();

       //$this->assertEquals("Hello, Docker!", $output);
    }

}
