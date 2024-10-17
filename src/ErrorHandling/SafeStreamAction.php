<?php

declare(strict_types=1);

namespace Phpro\ResourceStream\ErrorHandling;

use Phpro\ResourceStream\Exception\StreamActionFailureException;

final class SafeStreamAction
{
    /**
     * @template R
     *
     * @param (\Closure(): (R|false)) $closure
     *
     * @throws StreamActionFailureException
     *
     * @return R
     */
    public static function run(\Closure $closure, string $message): mixed
    {
        $previous_level = error_reporting(0);

        try {
            $result = $closure();
        } finally {
            error_reporting($previous_level);
        }

        if (false === $result) {
            $lastError = error_get_last();
            $details = $lastError['message'] ?? null;

            throw StreamActionFailureException::unableToRunAction($message, $details);
        }

        return $result;
    }
}
