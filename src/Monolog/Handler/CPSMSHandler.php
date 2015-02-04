<?php
namespace Monolog\Handler;

use Monolog\Logger;
use Monolog\Handler\MailHandler;
use Monolog\Formatter\LineFormatter;

/**
 * CPSMS (https://www.cpsms.dk) log handler.
 *
 * @author Rasmus Bech <me@rbech.com>
 */
class CPSMSHandler extends MailHandler
{
    protected $username;
    protected $password;
    protected $to;
    protected $from;

    /**
     * @param string       $username CPSMS API username
     * @param string       $password CPSMS API password
     * @param string|array $to       The receiver of the SMS
     * @param string       $from     The sender of the SMS
     * @param integer      $level    The minimum logging level at which this handler will be triggered
     * @param Boolean      $bubble   Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct($username, $password, $to, $from, $level = Logger::CRITICAL, $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->username = $username;
        $this->password = $password;
        $this->to = $to;
        $this->from = $from;
    }

    /**
     * {@inheritdoc}
     */
    protected function send($content, array $records)
    {
        $ch = curl_init();

        $args = array(
            "username" => $this->username,
            "password" => $this->password,
            "recipient" => $this->to,
            "from" => $this->from,
            "message" => $content,
        );

        curl_setopt($ch, CURLOPT_URL, "https://www.cpsms.dk/sms/");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));

        curl_exec($ch);

        curl_close($ch);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultFormatter()
    {
        return new LineFormatter(null, null, false, true);
    }
}
