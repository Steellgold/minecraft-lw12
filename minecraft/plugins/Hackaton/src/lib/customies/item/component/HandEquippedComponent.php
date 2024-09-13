<?php
declare(strict_types=1);

namespace hackaton\lib\customies\item\component;

final class HandEquippedComponent implements ItemComponent {

    /** @var bool */
    private bool $handEquipped;

    /**
     * @param bool $handEquipped
     */
    public function __construct(bool $handEquipped = true) {
        $this->handEquipped = $handEquipped;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "hand_equipped";
    }

    /**
     * @return bool
     */
    public function getValue(): bool {
        return $this->handEquipped;
    }

    /**
     * @return bool
     */
    public function isProperty(): bool {
        return true;
    }
}