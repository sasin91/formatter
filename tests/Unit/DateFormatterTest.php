<?php

use Carbon\Carbon;
use CollabCorp\Formatter\Formatters\DateFormatter;
use CollabCorp\Formatter\Formatters\StringFormatter;
use CollabCorp\Formatter\Tests\TestCase;

class DateFormatterTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->formatter = new DateFormatter('12/22/2030');
        $this->formatter = $this->formatter->toCarbon();
    }
    /**
     * @test
     */
    public function dateFormatterThrowsExceptionOnUndefinedOrNonWhitelistedMethod()
    {
        $this->expectException(\Exception::class);
        $formatter = (new DateFormatter('12/22/2030'))->iDontExist();
    }

    /**
     * @test
     */
    public function dateFormatterCallsAnotherFormattersMethodUsingMagicMethodCall()
    {
        $this->formatter = new DateFormatter('12/22/2030dsfs');
        //test that this a new type of formatter
        $this->assertInstanceOf(StringFormatter::class, $this->formatter->onlyNumbers());
        //test the method call works,onlyNumbers exists on the StringFormatter class
        $this->assertEquals("12222030", $this->formatter->onlyNumbers()->get());
    }
    /**
     * @test
     */
    public function dateFormatterToCarbonMethodReturnsCarbonInstance()
    {
        $this->assertInstanceOf(Carbon::class, (new DateFormatter('12/22/2030'))->toCarbon()->get());
    }
    /**
     * @test
     */
    public function dateFormatterCanChangeDateFormat()
    {
        $this->formatter = $this->formatter->format('F d, Y');
        $this->assertEquals('December 22, 2030', $this->formatter->get());
    }
    /**
     * @test
     */
    public function dateFormatterCanChangeCarbonTimezone()
    {
        $this->formatter = $this->formatter->setTimeZone('America/Toronto');

        $this->assertEquals('America/Toronto', $this->formatter->get()->tzName);
    }
    /**
     * @test
     */
    public function dateFormatterCanCallCarbonAddMethods()
    {
        $this->assertEquals('2030-12-22 00:00:02', $this->formatter->addSeconds(2)->get()->toDateTimeString());
        $this->assertEquals('2030-12-22 00:02:02', $this->formatter->addMinutes(2)->get()->toDateTimeString());
        $this->assertEquals('2030-12-22 02:02:02', $this->formatter->addHours(2)->get()->toDateTimeString());

        $this->assertEquals('2030-12-24', $this->formatter->addDays(2)->get()->toDateString());
        $this->assertEquals('2031-01-07', $this->formatter->addWeeks(2)->get()->toDateString());
        $this->assertEquals('2031-03-07', $this->formatter->addMonths(2)->get()->toDateString());
        $this->assertEquals('2033-03-07', $this->formatter->addYears(2)->get()->toDateString());
    }

    /**
     * @test
     */
    public function dateFormatterCanCallCarbonSubMethods()
    {
        $this->formatter = (new DateFormatter('2030-12-22 03:40:02'))->toCarbon();

        $this->assertEquals('2030-12-22 03:40:00', $this->formatter->subSeconds(2)->get()->toDateTimeString());
        $this->assertEquals('2030-12-22 03:38:00', $this->formatter->subMinutes(2)->get()->toDateTimeString());
        $this->assertEquals('2030-12-22 01:38:00', $this->formatter->subHours(2)->get()->toDateTimeString());

        $this->assertEquals('2030-12-20', $this->formatter->subDays(2)->get()->toDateString());
        $this->assertEquals('2030-12-06', $this->formatter->subWeeks(2)->get()->toDateString());
        $this->assertEquals('2030-10-06', $this->formatter->subMonths(2)->get()->toDateString());
        $this->assertEquals('2028-10-06', $this->formatter->subYears(2)->get()->toDateString());
    }
}
