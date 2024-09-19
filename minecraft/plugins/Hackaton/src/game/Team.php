<?php

namespace hackaton\game;

use hackaton\player\GAPlayer;
use hackaton\player\PlayerSession;
use pocketmine\color\Color;

class Team {

    /** @var int */
    public const TYPE_SOLO = 1;

    /** @var int */
    public const TYPE_DUO = 2;

    /** @var int */
    public const TYPE_TRIO = 3;

    /** @var int */
    public const TYPE_SQUAD = 4;

    /** @var PlayerSession[] */
    private array $sessions = [];

    /**
     * @param int $type
     * @param string $name
     * @param string $color
     * @param string $laserColor
     * @param string $icon
     */
    public function __construct(
        private readonly int    $type,
        private readonly string $name,
        private readonly string $color,
        private readonly string $laserColor,
        private readonly string $icon
    ) {
    }

    /**
     * @return int
     */
    public function getType(): int {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getColor(): string {
        return $this->color;
    }

    /**
     * @return Color
     */
    public function getLaserColor(): Color {
        $rgb = explode(":", $this->laserColor);
        return new Color((int)$rgb[0], (int)$rgb[1], (int)$rgb[2]);
    }

    /**
     * @return string
     */
    public function getIcon(): string {
        return $this->icon;
    }

    /**
     * @return array
     */
    public function getSessions(): array {
        return $this->sessions;
    }

    /**
     * @param PlayerSession $session
     * @return void
     */
    public function addSession(PlayerSession $session): void {
        $this->sessions[$session->getPlayer()->getUniqueId()->toString()] = $session;
    }

    /**
     * @param PlayerSession|null $session
     * @return bool
     */
    public function removeSession(?PlayerSession $session): bool {
        if (is_null($session)) return false;
        $uuid = $session->getPlayer()->getUniqueId()->toString();
        $session = $this->sessions[$uuid] ?? null;

        if (is_null($session)) return false;

        unset($this->sessions[$uuid]);
        return true;
    }

    /**
     * @return int
     */
    public function getMaximumPlayers(): int {
        return match ($this->type) {
            self::TYPE_SOLO => 1,
            self::TYPE_DUO => 2,
            self::TYPE_TRIO => 3,
            self::TYPE_SQUAD => 4,
            default => 0
        };
    }
}