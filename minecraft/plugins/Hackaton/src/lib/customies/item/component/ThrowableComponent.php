<?php
declare(strict_types=1);

namespace hackaton\lib\customies\item\component;

final class ThrowableComponent implements ItemComponent {

    /** @var bool */
    private bool $doSwingAnimation;

    /**
     * @param bool $doSwingAnimation
     */
    public function __construct(bool $doSwingAnimation) {
        $this->doSwingAnimation = $doSwingAnimation;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "minecraft:throwable";
    }

    /**
     * @return bool[]
     */
    public function getValue(): array {
        return [
            "do_swing_animation" => $this->doSwingAnimation
        ];
    }

    /**
     * @return bool
     */
    public function isProperty(): bool {
        return false;
    }
}