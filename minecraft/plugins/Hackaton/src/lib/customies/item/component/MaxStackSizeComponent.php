<?php
declare(strict_types=1);

namespace hackaton\lib\customies\item\component;

final class MaxStackSizeComponent implements ItemComponent {

    /** @var int  */
	private int $maxStackSize;

    /**
     * @param int $maxStackSize
     */
	public function __construct(int $maxStackSize) {
		$this->maxStackSize = $maxStackSize;
	}

    /**
     * @return string
     */
	public function getName(): string {
		return "max_stack_size";
	}

    /**
     * @return int
     */
	public function getValue(): int {
		return $this->maxStackSize;
	}

    /**
     * @return bool
     */
	public function isProperty(): bool {
		return true;
	}
}