<?php
declare(strict_types=1);

namespace hackaton\lib\customies\item\component;

final class ArmorComponent implements ItemComponent {

    public const TEXTURE_TYPE_CHAIN = "chain";
    public const TEXTURE_TYPE_DIAMOND = "diamond";
    public const TEXTURE_TYPE_ELYTRA = "elytra";
    public const TEXTURE_TYPE_GOLD = "gold";
    public const TEXTURE_TYPE_IRON = "iron";
    public const TEXTURE_TYPE_LEATHER = "leather";
    public const TEXTURE_TYPE_NETHERITE = "netherite";
    public const TEXTURE_TYPE_NONE = "none";
    public const TEXTURE_TYPE_TURTLE = "turtle";

    /** @var int */
    private int $protection;

    /** @var string */
    private string $textureType;

    /**
     * @param int $protection
     * @param string $textureType
     */
    public function __construct(int $protection, string $textureType = self::TEXTURE_TYPE_NONE) {
        $this->protection = $protection;
        $this->textureType = $textureType;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "minecraft:armor";
    }

    /**
     * @return array
     */
    public function getValue(): array {
        return [
            "protection" => $this->protection,
            "texture_type" => $this->textureType
        ];
    }

    /**
     * @return bool
     */
    public function isProperty(): bool {
        return false;
    }
}