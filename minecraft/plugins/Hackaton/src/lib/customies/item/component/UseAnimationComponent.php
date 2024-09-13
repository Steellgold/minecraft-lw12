<?php
declare(strict_types=1);

namespace hackaton\lib\customies\item\component;

final class UseAnimationComponent implements ItemComponent {

    public const ANIMATION_EAT = 1;
    public const ANIMATION_DRINK = 2;

    /** @var int */
    private int $animation;

    /**
     * @param int $animation
     */
    public function __construct(int $animation) {
        $this->animation = $animation;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "use_animation";
    }

    /**
     * @return int
     */
    public function getValue(): int {
        return $this->animation;
    }

    /**
     * @return bool
     */
    public function isProperty(): bool {
        return true;
    }
}