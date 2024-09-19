<?php

namespace hackaton\task\async;

class RequestError {

    /** @var string */
    private readonly string $message;

    public function __construct(string|array $message, private readonly int $code) {
        $this->message = is_array($message) ? $message[0] : $message;
    }

    /**
     * @return string
     */
    public function getMessage(): string {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getCode(): int {
        return $this->code;
    }
}