<?php
declare(strict_types=1);

namespace hackaton\lib\customies\item\item\component;

final class UseDurationComponent implements ItemComponent {

    /** @var int  */
	private int $duration;

    /**
     * @param int $duration
     */
	public function __construct(int $duration) {
		$this->duration = $duration;
	}

    /**
     * @return string
     */
	public function getName(): string {
		return "use_duration";
	}

    /**
     * @return int
     */
	public function getValue(): int {
		return $this->duration;
	}

    /**
     * @return bool
     */
	public function isProperty(): bool {
		return true;
	}
}