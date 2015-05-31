<?php
/**
 * Message module.
 *
 * @copyright 2008-2015 Vanilla Forums, Inc
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU GPL v2
 * @package Dashboard
 * @since 2.0
 */

/**
 * Handle display of a message.
 */
class MessageModule extends Gdn_Module {

    /** @var string */
    protected $_Message;

    /**
     *
     *
     * @param string $Sender
     * @param bool $Message
     */
    public function __construct($Sender = '', $Message = FALSE) {
        parent::__construct($Sender);

        $this->_ApplicationFolder = 'dashboard';
        $this->_Message = $Message;
    }

    /**
     *
     *
     * @return mixed|string
     */
    public function AssetTarget() {
        return $this->_Message == FALSE ? 'Content' : GetValue('AssetTarget', $this->_Message);
    }

}
