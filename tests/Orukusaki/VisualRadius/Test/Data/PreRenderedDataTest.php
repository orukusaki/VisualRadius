<?php
/**
 * Contains the PastedRecordsTest class
 *
 * @package VisualRadius
 * @author  Peter Smith <peter@orukusaki.co.uk>
 * @link    github.com/orukusaki/visualradius
 */
namespace Orukusaki\VisualRadius\Test\Data;

use Orukusaki\VisualRadius\Data\PreRenderedData;
use Orukusaki\VisualRadius\Data\Session;
use Orukusaki\VisualRadius\Data\SessionList;
use Orukusaki\VisualRadius\DataStore\MongoStore;
use DateTime;

/**
 * Tests for the PastedRecords class
 *
 * @package VisualRadius
 * @author  Peter Smith <peter@orukusaki.co.uk>
 * @link    github.com/orukusaki/visualradius
 */
class PreRenderedDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideSessionData
     */
    public function testBuildFromArray($sessionData, $expected)
    {
        $this->markTestIncomplete();
        // $list = new SessionList();
        // foreach ($sessionData as $session) {
        //     $list->add(
        //         new Session($session[0], $session[1], $session[2], $session[3])
        //     );
        // }
        // $options = array(
        //     'condense' => true,
        //     'viewEnd'  => 24 * 60,
        // );

        // $preRenderedData = PreRenderedData::buildFromSessionData($list, $options);


        // $this->assertEquals($expected, $preRenderedData);
    }


    public function provideSessionData()
    {
        return array(
            array(
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
                        null,
                    ),
                ),
                array(),
            )
        );
    }
}
