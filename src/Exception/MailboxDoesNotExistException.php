<?php

namespace BennoThommo\Imap\Exception;

class MailboxDoesNotExistException extends Exception
{
    public function __construct($mailbox)
    {
        parent::__construct('Mailbox ' . $mailbox. ' does not exist');
    }
}
