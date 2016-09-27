<?php

namespace BennoThommo\Imap\Search\Flag;

use BennoThommo\Imap\Search\AbstractCondition;

/**
 * Represents an SEEN flag condition. Messages must have the \\SEEN flag
 * set in order to match the condition.
 */
class Seen extends AbstractCondition
{
    /**
     * Returns the keyword that the condition represents.
     *
     * @return string
     */
    public function getKeyword()
    {
        return 'SEEN';
    }
}
