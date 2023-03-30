<?php

namespace Seeker\pfpm\pathfinding\exception;

use Exception;
use Throwable;

class PathNotFoundException extends Exception {
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null) {
        $msg = "PFPath not found.";
        $details = $message === "" ? "" : " Details: " . $message;
        $msg .= $details;
        parent::__construct($msg, $code, $previous);
    }
}