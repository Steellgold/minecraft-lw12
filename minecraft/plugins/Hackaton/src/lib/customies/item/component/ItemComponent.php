<?php
declare(strict_types=1);

namespace hackaton\lib\customies\item\component;

interface ItemComponent {

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return mixed
     */
    public function getValue(): mixed;

    /**
     * @return bool
     */
    public function isProperty(): bool;
}