<?php

namespace Spatie\BpostAddressWebservice\Issues;

use JsonSerializable;

abstract class Issue implements JsonSerializable
{
    /** @var string */
    protected $message;

    /** @var string */
    protected $attribute;

    public function __construct(string $message, string $attribute)
    {
        $this->message = $message;
        $this->attribute = $attribute;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function attribute(): string
    {
        return $this->attribute;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'attribute' => $this->attribute,
            'message' => $this->message,
        ];
    }
}
