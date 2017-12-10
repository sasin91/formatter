<?php

namespace CollabCorp\Formatter\Tests\Feature;

use Carbon\Carbon;
use CollabCorp\Formatter\Formatter;
use CollabCorp\Formatter\Formatters\MathFormatter;
use CollabCorp\Formatter\Formatters\StringFormatter;
use CollabCorp\Formatter\Tests\TestCase;

/**
 * This testcase will serve as a general purpose for tests,
 * that doesn't quite fit into a separate class,
 * or is a general feature of the Formatter object.
 */
class FormatterTest extends TestCase
{
    /** @test */
    public function itGetsTheResultWhenCastToAString()
    {
        $text = Formatter::create("hello world");
        $this->assertEquals("hello world", (string)$text);
        
        $number = Formatter::create(1);
        $this->assertEquals("1", (string)$number);
    }

    /** @test */
    function itIsPossibleToExtractTwoDistinctResultsFromTheSameInstance() 
    {
    	$formatter = Formatter::create("Woohoo 110011!");

    	$string = $formatter->onlyLetters()->suffix(', by John Doe!')->get();
    	$number = $formatter->onlyNumbers();

    	$this->assertEquals("Woohoo, by John Doe!", $string);
    	$this->assertEquals(110011, $number->get());
    } 
}
