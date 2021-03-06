<?php

namespace BennoThommo\Imap\Search\State;

use BennoThommo\Imap\Search\AbstractCondition;

/**
 * Represents an OLD condition. Only old messages will match this condition.
 */
class Old extends AbstractCondition
{
    /**
     * Returns the keyword that the condition represents.
     *
     * @return string
     */
    public function getKeyword()
    {
        return 'OLD';
    }
}
