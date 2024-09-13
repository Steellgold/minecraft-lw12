<?php
declare(strict_types=1);

namespace hackaton\lib\customies\item\component;

final class CanDestroyInCreativeComponent implements ItemComponent {

    /** @var bool  */
    private bool $canDestroyInCreative;

    /**
     * @param bool $canDestroyInCreative
     */
    public function __construct(bool $canDestroyInCreative = true) {
        $this->canDestroyInCreative = $canDestroyInCreative;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "can_destroy_in_creative";
    }

    /**
     * @return bool
     */
    public function getValue(): bool {
        return $this->canDestroyInCreative;
    }

    /**
     * @return bool
     */
    public function isProperty(): bool {
        return true;
    }
}
