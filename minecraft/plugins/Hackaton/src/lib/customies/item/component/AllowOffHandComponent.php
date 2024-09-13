<?php
declare(strict_types=1);

namespace hackaton\lib\customies\item\component;

final class AllowOffHandComponent implements ItemComponent {

    /** @var bool  */
	private bool $offHand;

    /**
     * @param bool $offHand
     */
	public function __construct(bool $offHand = true) {
		$this->offHand = $offHand;
	}

    /**
     * @return string
     */
	public function getName(): string {
		return "allow_off_hand";
	}

    /**
     * @return bool
     */
	public function getValue(): bool {
		return $this->offHand;
	}

    /**
     * @return bool
     */
	public function isProperty(): bool {
		return true;
	}
}