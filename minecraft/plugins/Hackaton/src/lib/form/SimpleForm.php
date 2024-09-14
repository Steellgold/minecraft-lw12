<?php

namespace hackaton\lib\form;

use hackaton\player\GAPlayer;

abstract class SimpleForm extends Form {

    /** @var int */
    const IMAGE_TYPE_PATH = 0;

    /** @var int */
    const IMAGE_TYPE_URL = 1;

    /** @var string */
    private string $content = "";

    /** @var array */
    private array $labelMap = [];

    /**
     * @param GAPlayer $player
     */
    public function __construct(GAPlayer $player) {
        $this->data["type"] = "form";
        $this->data["title"] = "";
        $this->data["content"] = $this->content;
        parent::__construct($player);
    }

    /**
     * @param $data
     * @return void
     */
    public function processData(&$data): void {
        $data = $this->labelMap[$data] ?? null;
    }

    /**
     * @param string $title
     * @param string|null $flag
     * @return void
     */
    public function setTitle(string $title, ?string $flag = null): void {
        $this->data["title"] = ($flag ?? "") . $title;
    }

    /**
     * @return string
     */
    public function getTitle(): string {
        return $this->data["title"];
    }

    /**
     * @return string
     */
    public function getContent(): string {
        return $this->data["content"];
    }

    /**
     * @param string $content
     * @return void
     */
    public function setContent(string $content): void {
        $this->data["content"] = $content;
    }

    /**
     * @param string $text
     * @param int $imageType
     * @param string $imagePath
     * @param $label
     * @return void
     */
    public function addButton(string $text, int $imageType = -1, string $imagePath = "", $label = null): void {
        $content = ["text" => $text];
        if ($imageType !== -1) {
            $content["image"]["type"] = $imageType === self::IMAGE_TYPE_PATH ? "path" : "url";
            $content["image"]["data"] = $imagePath;
        }

        $this->data["buttons"][] = $content;
        $this->labelMap[] = $label ?? count($this->labelMap);
    }
}