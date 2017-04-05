<?php
namespace Ddeboer\Imap\Tests;

use Ddeboer\Imap\Exception\MailboxDoesNotExistException;
use Ddeboer\Imap\Mailbox;
use Ddeboer\Imap\Server;
use Ddeboer\Imap\Connection;

abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    protected static $connection;
    protected static $executing = false;

    public function setUp()
    {
        // Trigger the before() callback - this is a replacement for setUpBeforeClass that should respect test filters
        if (!self::$executing) {
            static::$executing = true;
            static::before();
        }

        parent::setUp();
    }

    public static function tearDownAfterClass()
    {
        // Trigger the after() callback - this is a replacement for tearDownAfterClass that should respect test filters
        if (static::$executing) {
            static::$executing = false;
            static::after();
        }

        parent::tearDownAfterClass();
    }

    public static function before() {}
    public static function after() {}

    public static function setUpBeforeClass()
    {
        if (getenv('TEST_EMAIL_SERVER') === false) {
            throw new \RuntimeException(
                'Please set environment variable TEST_EMAIL_SERVER before running functional tests'
            );
        }

        if (getenv('TEST_EMAIL_USERNAME') === false) {
            throw new \RuntimeException(
                'Please set environment variable TEST_EMAIL_USERNAME before running functional tests'
            );
        }

        if (getenv('TEST_EMAIL_PASSWORD') === false) {
            throw new \RuntimeException(
                'Please set environment variable TEST_EMAIL_PASSWORD before running functional tests'
            );
        }

        $server = new Server(getenv('TEST_EMAIL_SERVER'));

        static::$connection = $server->authenticate(getenv('TEST_EMAIL_USERNAME'), getenv('TEST_EMAIL_PASSWORD'));
    }

    /**
     * @return Connection
     */
    protected static function getConnection()
    {
        return static::$connection;
    }

    /**
     * Create a mailbox
     *
     * If the mailbox already exists, it will be deleted first
     *
     * @param string $name Mailbox name
     *
     * @return Mailbox
     */
    protected static function createMailbox($name)
    {
        $uniqueName = 'INBOX.' . $name . uniqid();

        try {
            $mailbox = static::getConnection()->getMailbox($uniqueName);
            $this->deleteMailbox($mailbox);
        } catch (MailboxDoesNotExistException $e) {
            // Ignore mailbox not found
        }

        return static::getConnection()->createMailbox($uniqueName);
    }

    /**
     * Delete a mailbox and all its messages
     *
     * @param Mailbox $mailbox
     */
    protected static function deleteMailbox(Mailbox $mailbox)
    {
        // Delete all messages
        foreach ($mailbox->getMessages() as $message) {
            $message->delete();
        }
        $mailbox->delete();
    }

    protected function createTestMessage(
        Mailbox $mailbox,
        $subject = 'Don\'t panic!',
        $contents = 'Don\'t forget your towel',
        $from = 'someone@there.com',
        $to = 'me@here.com'
    ) {
        $message = "From: $from\r\n"
            . "To: $to\r\n"
            . "Subject: $subject\r\n"
            . "\r\n"
            . "$contents";

        return $mailbox->addMessage($message, true);
    }

    protected function getFixture($fixture)
    {
        return file_get_contents(__DIR__ . '/fixtures/' . $fixture);
    }
}
