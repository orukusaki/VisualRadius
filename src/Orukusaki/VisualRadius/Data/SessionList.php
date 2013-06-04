<?php
/**
 * Contains the SessionList class
 *
 * @package    VisualRadius
 * @subpackage Data
 * @author     Peter Smith <peter@orukusaki.co.uk>
 * @link       Link
 */
namespace Orukusaki\VisualRadius\Data;

/**
 * A container for many Session objects
 *
 * @package    VisualRadius
 * @subpackage Data
 * @author     Peter Smith <peter@orukusaki.co.uk>
 * @link       Link
 */
class SessionList implements \Iterator
{
    /**
     * Session List
     *
     * @var array
     */
    private $sessions = array();

    /**
     * Index
     *
     * @var int
     */
    private $index = 0;

    /**
     * Add A session to the list
     *
     * @param Session $session Session
     *
     * @return SessionList
     */
    public function add(Session $session)
    {
        $this->sessions[] = $session;
        return $this;
    }

    /**
     * Reset the index
     *
     * @return void
     */
    public function rewind()
    {
        $this->index = 0;
    }

    /**
     * Return the entry at the current position
     *
     * @return Session
     */
    public function current()
    {
        return $this->sessions[$this->index];
    }

    /**
     * Get the current key
     *
     * @return int
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * Increment the index
     *
     * @return void
     */
    public function next()
    {
        $this->index++;
    }

    /**
     * Is the current index valid?
     *
     * @return bool
     */
    public function valid()
    {
        return isset($this->sessions[$this->index]);
    }
}
