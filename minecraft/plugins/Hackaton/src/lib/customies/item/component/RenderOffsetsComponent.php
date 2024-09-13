<?php
declare(strict_types=1);

namespace hackaton\lib\customies\item\component;

final class RenderOffsetsComponent implements ItemComponent {

    /** @var int */
    private int $textureWidth;

    /** @var int */
    private int $textureHeight;

    /** @var bool */
    private bool $handEquipped;

    /**
     * @param int $textureWidth
     * @param int $textureHeight
     * @param bool $handEquipped
     */
    public function __construct(int $textureWidth, int $textureHeight, bool $handEquipped = false) {
        $this->textureWidth = $textureWidth;
        $this->textureHeight = $textureHeight;
        $this->handEquipped = $handEquipped;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "minecraft:render_offsets";
    }

    /**
     * @return array[]
     */
    public function getValue(): array {
        $horizontal = ($this->handEquipped ? 0.075 : 0.1) / ($this->textureWidth / 16);
        $vertical = ($this->handEquipped ? 0.125 : 0.1) / ($this->textureHeight / 16);
        $perspectives = [
            "first_person" => [
                "scale" => [$horizontal, $vertical, $horizontal],
            ],
            "third_person" => [
                "scale" => [$horizontal, $vertical, $horizontal]
            ]
        ];
        return [
            "main_hand" => $perspectives,
            "off_hand" => $perspectives
        ];
    }

    /**
     * @return bool
     */
    public function isProperty(): bool {
        return false;
    }
}