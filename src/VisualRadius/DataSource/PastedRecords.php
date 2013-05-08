<?php
/**
 * Contains the PastedRecords class
 *
 * @package VisualRadius
 * @author  Peter Smith <peter@orukusaki.co.uk>
 * @link    github.com/orukusaki/visualradius
 */
namespace VisualRadius\DataSource;

use VisualRadius\IDataSource;
use VisualRadius\Data\SessionList;
use VisualRadius\Data\Session;
use DateTime;

/**
 * Records which have been pasted in from the Workplace RADIUS reporting page
 *
 * @package VisualRadius
 * @author  Peter Smith <peter@orukusaki.co.uk>
 * @link    github.com/orukusaki/visualradius
 */
class PastedRecords implements IDataSource
{
    const COLUMN_LASTUPDATE = 3;
    const COLUMN_CLI = 7;
    const COLUMN_START = 8;
    const COLUMN_END = 9;

    const DATE_FORMAT = 'H:i d/M/Y';

    /**
     * List of sessions.
     *
     * @var SessionList
     */
    private $_sessions;

    /**
     * Constructor
     *
     * @param string $input The input stright from the HTTP request
     *
     * @return void
     */
    public function __construct($input)
    {
        $lines = explode("\n", $input);
        $this->_sessions = new SessionList();

        foreach ($lines as $line) {

            $items = explode("\t", $line);

            // Skip any blank or incomplete lines
            if (sizeof($items) < 10) {
                continue;
            }

            $sessionLast = self::parseDate($items[self::COLUMN_LASTUPDATE]);

            // Skip any line with an invalid update time (like the title row)
            if (!$sessionLast) {
                continue;
            }

            $sessionStart = self::parseDate($items[self::COLUMN_START]);
            $sessionClose = self::parseDate($items[self::COLUMN_END]);

            $sessionService = (preg_match('/^[0-9]{9,12}/', $items[self::COLUMN_CLI]))
                            ? Session::SERVICE_DIALUP : Session::SERVICE_BROADBAND;

            $this->_sessions->add(
                new Session($sessionService, $sessionLast, $sessionStart, $sessionClose)
            );
        }
    }

    /**
     * Get the collected Sessions
     *
     * @api
     *
     * @return VisualRadius\Data\SessionList
     */
    public function getData()
    {
        return $this->_sessions;
    }

    /**
     * Get a DateTime representation of a date / time string.
     *
     * Returns null if the format isn't what's expected
     *
     * @param string $dateStr Date String
     *
     * @return DateTime|null
     */
    private static function parseDate($dateStr)
    {
        return DateTime::createFromFormat(self::DATE_FORMAT, $dateStr) ?: null;
    }
}
