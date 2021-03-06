<?php

namespace BennoThommo\Imap\Tests;

use BennoThommo\Imap\Server;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Ddeboer\Imap\Exception\AuthenticationFailedException
     */
    public function testFailedAuthenticate()
    {
        $server = new Server('imap.gmail.com');
        $server->authenticate('fake_username', 'fake_password');
    }
}
