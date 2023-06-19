<?php
/**
 * UKRGB Map
 *
 * @package        com_ukrgbmap
 *
 * @copyright  (C) 2023 Mark Gawler. <https://github.com/markgawler>
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

class UkrgbmapModelDefault extends JModelItem
{

    /**
     * @var string message
     * @since 1.0
     */
    protected $message;
    //protected string $message;

    /**
     * Get the message
     *
     * @return  string  The message to be displayed to the user
     * @since 1.0
     */
    public function getMessage(): string
    {
        if (!isset($this->message))
        {

            $this->message = "This is the default view, probably no task has been specified.";
        }
        return $this->message;
    }

}
