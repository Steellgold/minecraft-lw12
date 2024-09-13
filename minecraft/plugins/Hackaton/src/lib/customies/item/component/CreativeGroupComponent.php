<?php
declare(strict_types=1);

namespace hackaton\lib\customies\item\component;

use hackaton\lib\customies\item\CreativeInventoryInfo;

final class CreativeGroupComponent implements ItemComponent {

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
		return "creative_group";
	}

    /**
     * @return string
     */
	public function getValue(): string {
		return $this->creativeInfo->getGroup();
	}

    /**
     * @return bool
     */
	public function isProperty(): bool {
		return true;
	}
}