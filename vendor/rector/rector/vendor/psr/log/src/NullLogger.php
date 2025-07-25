<?php

namespace RectorPrefix202507\Psr\Log;

/**
 * This Logger can be used to avoid conditional log calls.
 *
 * Logging should always be optional, and if no logger is provided to your
 * library creating a NullLogger instance to have something to throw logs at
 * is a good way to avoid littering your code with `if ($this->logger) { }`
 * blocks.
 */
class NullLogger extends AbstractLogger
{
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed[] $context
     *
     * @throws \Psr\Log\InvalidArgumentException
     * @param string|\Stringable $message
     */
    public function log($level, $message, array $context = []) : void
    {
        // noop
    }
}
