<?php
namespace BennoThommo\Imap\Tests;

use BennoThommo\Imap\Exception\MailboxDoesNotExistException;
use BennoThommo\Imap\Mailbox;
use BennoThommo\Imap\Server;
use BennoThommo\Imap\Connection;

abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    protected static $connection;

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
    protected function createMailbox($name)
    {
        $uniqueName = $name . uniqid();

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
    protected function deleteMailbox(Mailbox $mailbox)
    {
        // Move all messages in the mailbox to Gmail trash
        $trash = self::getConnection()->getMailbox('[Gmail]/Bin');

        foreach ($mailbox->getMessages() as $message) {
            $message->move($trash);
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

        $mailbox->addMessage($message);
    }

    protected function getFixture($fixture)
    {
        return file_get_contents(__DIR__ . '/fixtures/' . $fixture);
    }
}
