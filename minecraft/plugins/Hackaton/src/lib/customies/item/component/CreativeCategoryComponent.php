<?php
declare(strict_types=1);

namespace wapy\libs\customiesdevs\customies\item\component;

use hackaton\lib\customies\item\CreativeInventoryInfo;

final class CreativeCategoryComponent implements ItemComponent {

    /** @var CreativeInventoryInfo  */
	private CreativeInventoryInfo $creativeInfo;

    /**
     * @param CreativeInventoryInfo $creativeInfo
     */
	public function __construct(CreativeInventoryInfo $creativeInfo) {
		$this->creativeInfo = $creativeInfo;
	}

    /**
     * @return string
     */
	public function getName(): string {
		return "creative_category";
	}

    /**
     * @return int
     */
	public function getValue(): int {
		return $this->creativeInfo->getNumericCategory();
	}

    /**
     * @return bool
     */
	public function isProperty(): bool {
		return true;
	}
}