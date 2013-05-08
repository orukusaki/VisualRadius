<?php
/**
 * Contains the PastedRecordsTest class
 *
 * @package VisualRadius
 * @author  Peter Smith <peter@orukusaki.co.uk>
 * @link    github.com/orukusaki/visualradius
 */
namespace VisualRadius\Test\DataSource;

use VisualRadius\DataSource\PastedRecords;
use VisualRadius\Data\Session;
use VisualRadius\Data\SessionList;
use DateTime;

/**
 * Tests for the PastedRecords class
 *
 * @package VisualRadius
 * @author  Peter Smith <peter@orukusaki.co.uk>
 * @link    github.com/orukusaki/visualradius
 */
class PastedRecordsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test importing fomr simple data
     *
     * @param string $input        Input data
     * @param array  $expectedData Data used to create expected session list
     *
     * @dataProvider providePastedDataToParse
     *
     * @covers VisualRadius\DataSource\PastedRecords
     *
     * @return void
     */
    public function testDataImport($input, array $expectedData)
    {
        $pastedRecords = new PastedRecords($input);
        $expected = new SessionList();
        foreach ($expectedData as $expectedSession) {
            $expected->add(
                new Session($expectedSession[0], $expectedSession[1], $expectedSession[2], $expectedSession[3])
            );
        }
        $this->assertEquals($expected, $pastedRecords->getData());

    }

    /**
     * Provider for testDataImport
     *
     * @return array
     */
    public function providePastedDataToParse()
    {
        return array(
            array(
                <<<THEEND
Premium Service Subscriptions
Plusnet Value\tpool\tGeneric Speed 15700 No Time Out


Additional CLIs
None


Session Records
Subscription\tAccess Rack\tLast Event\tEvent Time\tMessage\tIP Assigned\tService\tCalling From\tSession Started\tSession Ended\tSession Duration
Plusnet Value\t195.166.128.97\tactive\t15:44 07/May/2012\tunknown (Interim update)\t87.114.186.27\tNot set\tBBEU05628215\t05:58 03/May/2012\tN/A\t 4 Days, 9:55:10 (on going)
Plusnet Value\t195.166.128.105\tended\t05:57 03/May/2012\tunknown (Termination by server side (NAS) Modem)\t37.152.211.122\tNot set\tBBEU05628215\t11:25 29/Apr/2012\t05:57 03/May/2012\t 3 Days, 18:31:50
THEEND
            ,
                array(
                    array(
                        Session::SERVICE_BROADBAND,
                        new DateTime('15:44 07 May 2012'),
                        new DateTime('05:58 03 May 2012'),
                        null,
                    ),
                    array(
                        Session::SERVICE_BROADBAND,
                        new DateTime('05:57 03 May 2012'),
                        new DateTime('11:25 29 Apr 2012'),
                        new DateTime('05:57 03 May 2012'),
                    ),
                )
            ),
        );
    }
}
