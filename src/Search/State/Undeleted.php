<?php

namespace BennoThommo\Imap\Search\State;

use BennoThommo\Imap\Search\AbstractCondition;

/**
 * Represents a UNDELETED condition. Messages must not have been marked for
 * deletion in order to match the condition.
 */
class Undeleted extends AbstractCondition
{
    /**
     * Returns the keyword that the condition represents.
     *
     * @return string
     */
    public function getKeyword()
    {
        return 'UNDELETED';
    }
}
