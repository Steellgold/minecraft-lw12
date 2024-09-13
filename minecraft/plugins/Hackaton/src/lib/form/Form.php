<?php

namespace hackaton\lib;

use Closure;
use hackaton\GAPlayer;
use pocketmine\form\Form as IForm;
use pocketmine\player\Player;

abstract class Form implements IForm {

    /** @var array */
    protected array $data = [];

    /** @var Closure|null */
    private ?Closure $callback = null;

    /**
     * @param GAPlayer $player
     */
    public function __construct(GAPlayer $player) {
        $this->send($player);
    }

    /**
     * @param Player $player
     * @param $data
     * @return void
     */
    public function handleResponse(Player $player, $data): void {
        $this->processData($data);
        if (!is_null($this->callback)) {
            ($this->callback)($player, $data);
        }
    }

    /**
     * @param Closure $callback
     */
    public function setCallback(Closure $callback): void {
        $this->callback = $callback;
    }

    /**
     * @param $data
     * @return void
     */
    public function processData(&$data): void {
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array {
        return $this->data;
    }

    /**
     * @param GAPlayer $player
     * @return void
     */
    private function send(GAPlayer $player): void {
        $this->create($this, $player);
        $this->setCallback($this->createHandler($player, $this->data));
        $player->sendForm($this);
    }

    /**
     * @param Form $form
     * @param GAPlayer $player
     * @return void
     */
    abstract protected function create(Form $form, GAPlayer $player): void;

    /**
     * @param GAPlayer $player
     * @param $data
     * @return Closure
     */
    private function createHandler(GAPlayer $player, $data = null): Closure {
        return function ($formPlayer, $formData = null) use ($player, $data): void {
            if ($formPlayer instanceof GAPlayer) {
                $this->handle($formPlayer, $formData);
            }
        };
    }


    /**
     * @param GAPlayer $player
     * @param $data
     * @return void
     */
    abstract protected function handle(GAPlayer $player, $data): void;
}