<?php

declare(strict_types=1);

namespace Seeker\pfpm\pathfinding\exception;

use Exception;
use Throwable;

class OutOfRadiusException extends Exception {
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null) {
        $msg = "Starting node was out of radius.";
        $details = $message === "" ? "" : " Details: " . $message;
        $msg .= $details;
        parent::__construct($msg, $code, $previous);
    }
}