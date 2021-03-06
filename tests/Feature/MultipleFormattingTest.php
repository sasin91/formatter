<?php

use Carbon\Carbon;
use CollabCorp\Formatter\Tests\TestCase;
use CollabCorp\Formatter\Formatter;

class MultipleFormattingTest extends TestCase
{
    /**
     * @test
     */
    public function formatterCanConvertMultipleInputUsingExplicitKeyNames()
    {
        $formatters=[

            'name'=>'titleCase',
            'phone'=>'onlyNumbers|phone',
            'ssn'=>'ssn',
            'slug'=>'slug',
            'price'=>'decimals:2|start:$',
            'percent'=>'percentage:2|finish:%'

        ];

        $request=[

            'name'=>'peter parker',
            'phone'=>'sdfdfsdf1234567890',
            'ssn'=>'123456789',
            'slug'=>'about us',
            'price'=>'300',
            'percent'=>'30'
        ];

        $request = Formatter::convert($formatters, $request);

        $this->assertEquals($request['name'], 'Peter Parker');
        $this->assertEquals($request['phone'], '(123)456-7890');
        $this->assertEquals($request['ssn'], '123-45-6789');
        $this->assertEquals($request['slug'], 'about-us');
        $this->assertEquals($request['price'], '$300.00');
        $this->assertEquals($request['percent'], '0.30%');
    }


    /**
     * @test
     */
    public function formatterCanConvertMultipleInputUsingPatternKeyNames()
    {
        $formatters=[

            'name*'=>'titleCase',
            '*phone*'=>'onlyNumbers|phone',
            '*number'=>'add:2|multiply:2',

        ];

        $request=[

            'name'=>'peter parker',
            'somthing_name'=>'peter parker',//this should be the same cause were only formatting things that start with *name
            'phone'=>'sdfdfsdf1234567890',
            'cell_phone'=>'sdfdfsdf1234567890',
            'number_something'=>'2', //this should be the same cause were only formatting things that end with *number
            'something_number'=>'2'
        ];

        $request = Formatter::convert($formatters, $request);

        $this->assertEquals($request['name'], 'Peter Parker');
        $this->assertEquals($request['somthing_name'], 'peter parker');
        $this->assertEquals($request['phone'], '(123)456-7890');
        $this->assertEquals($request['cell_phone'], '(123)456-7890');
        $this->assertEquals($request['number_something'], '2');
        $this->assertEquals($request['something_number'], '8');
    }
}
