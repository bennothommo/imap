<?php

namespace BennoThommo\Imap\Search\State;

use BennoThommo\Imap\Search\AbstractCondition;

/**
 * Represents a DELETED condition. Messages must have been marked for deletion
 * but not yet expunged in order to match the condition.
 */
class Deleted extends AbstractCondition
{
    /**
     * Returns the keyword that the condition represents.
     *
     * @return string
     */
    public function getKeyword()
    {
        return 'DELETED';
    }
}
