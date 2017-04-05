<?php

namespace Ddeboer\Imap\Tests;

use Ddeboer\Imap\Server;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Ddeboer\Imap\Exception\AuthenticationFailedException
     */
    public function testFailedAuthenticate()
    {
        $server = new Server(getenv(TEST_EMAIL_SERVER));
        $server->authenticate('fake_username', 'fake_password');
    }
}
