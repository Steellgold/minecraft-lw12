<?php
declare(strict_types=1);

namespace wapy\libs\customiesdevs\customies\item\component;

final class FoilComponent implements ItemComponent {

    /** @var bool  */
	private bool $foil;

    /**
     * @param bool $foil
     */
	public function __construct(bool $foil = true) {
		$this->foil = $foil;
	}

    /**
     * @return string
     */
	public function getName(): string {
		return "foil";
	}

    /**
     * @return bool
     */
	public function getValue(): bool {
		return $this->foil;
	}

    /**
     * @return bool
     */
	public function isProperty(): bool {
		return true;
	}
}