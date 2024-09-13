<?php
declare(strict_types=1);

namespace hackaton\lib\customies\item\component;

final class BlockPlacerComponent implements ItemComponent {

    /** @var string */
    private string $blockIdentifier;

    /** @var bool */
    private bool $useBlockDescription;

    /**
     * @param string $blockIdentifier
     * @param bool $useBlockDescription
     */
    public function __construct(string $blockIdentifier, bool $useBlockDescription = false) {
        $this->blockIdentifier = $blockIdentifier;
        $this->useBlockDescription = $useBlockDescription;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "minecraft:block_placer";
    }

    /**
     * @return array
     */
    public function getValue(): array {
        return [
            "block" => $this->blockIdentifier,
            "use_block_description" => $this->useBlockDescription
        ];
    }

    /**
     * @return bool
     */
    public function isProperty(): bool {
        return false;
    }
}
