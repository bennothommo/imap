<?php

namespace Ddeboer\Imap\Tests;

class MessageTest extends AbstractTest
{
    /**
     * @var \Ddeboer\Imap\Mailbox
     */
    protected $mailbox;
    public static $Mailbox = null;

    public static function before()
    {
        self::$Mailbox = self::createMailbox('test-message');
    }

    public static function after()
    {
        self::deleteMailbox(self::$Mailbox);
    }

    public function testKeepUnseen()
    {
        $first = $this->createTestMessage(self::$Mailbox, 'Message A');
        $second = $this->createTestMessage(self::$Mailbox, 'Message B');

        $this->assertFalse($first->isSeen());

        $first->getBodyText();
        $this->assertTrue($first->isSeen());

        $this->assertFalse($second->isSeen());

        $second->keepUnseen()->getBodyText();
        $this->assertFalse($second->isSeen());
    }

    public function testEncodingQuotedPrintable()
    {
        $boundary = 'Mailer=123';
        $raw = "Subject: ESPAÑA\n"
            . "Date: =?ISO-8859-2?Q?Fri,_13_Jun_2014_17:18:44_+020?= =?ISO-8859-2?Q?0_(St=F8edn=ED_Evropa_(letn=ED_=E8as))?=\n"
            . "Content-Type: multipart/alternative; boundary=\"$boundary\"\n\n"
            . "--$boundary\n"
            . "Content-Transfer-Encoding: quoted-printable\n"
            . "Content-Type: text/html; charset=\"windows-1252\"\n"
            . "\n"
            . "<html><body>Espa=F1a</body></html>\n\n"
            . "--$boundary--\n\n";

        $message = self::$Mailbox->addMessage($raw, true);

        $this->assertEquals('ESPAÑA', $message->getSubject());
        $this->assertContains("<html><body>España</body></html>", $message->getBodyHtml());
        $this->assertEquals(new \DateTime('2014-06-13 17:18:44+0200'), $message->getDate());
    }

    public function testEmailAddress()
    {
        $message = self::$Mailbox->addMessage($this->getFixture('email_address'), true);

        $from = $message->getFrom();
        $this->assertInstanceOf('\Ddeboer\Imap\Message\EmailAddress', $from);
        $this->assertEquals('no_host', $from->getMailbox());

        $cc = $message->getCc();
        $this->assertCount(2, $cc);
        $this->assertInstanceOf('\Ddeboer\Imap\Message\EmailAddress', $cc[0]);
        $this->assertEquals('This one is right', $cc[0]->getName());
        $this->assertEquals('ding@dong.com', $cc[0]->getAddress());

        $this->assertInstanceOf('\Ddeboer\Imap\Message\EmailAddress', $cc[1]);
        $this->assertEquals('No-address', $cc[1]->getMailbox());
    }

    public function testBase64EncodedEmail()
    {
        $message = self::$Mailbox->addMessage($this->getFixture('selfmanager'), true);
        $html = $message->getBodyHtml();

        $this->assertContains("<html><body>", $html);
    }

    public function testBcc()
    {
        $raw = "Subject: Undisclosed recipients\r\n";
        $message = self::$Mailbox->addMessage($raw, true);

        $this->assertEquals('Undisclosed recipients', $message->getSubject());
        $this->assertCount(0, $message->getTo());
    }

    /**
     * @dataProvider getAttachmentFixture
     */
    public function testGetAttachments()
    {
        $message = self::$Mailbox->addMessage(
            $this->getFixture('attachment_encoded_filename'),
            true
        );

        $this->assertCount(1, $message->getAttachments());
        $attachment = $message->getAttachments()[0];
        $this->assertEquals(
            'Prostřeno_2014_poslední volné termíny.xls',
            $attachment->getFilename()
        );
    }

    public function getAttachmentFixture()
    {
        return [
            [ 'attachment_no_disposition' ],
            [ 'attachment_encoded_filename' ]
        ];
    }
}
