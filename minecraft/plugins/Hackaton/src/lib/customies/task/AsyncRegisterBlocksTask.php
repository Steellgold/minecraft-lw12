<?php
declare(strict_types=1);

namespace hackaton\lib\customies\task;

use hackaton\lib\customies\block\CustomiesBlockFactory;
use pmmp\thread\ThreadSafeArray;
use pocketmine\block\Block;
use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;
use pocketmine\scheduler\AsyncTask;

final class AsyncRegisterBlocksTask extends AsyncTask {

    /** @var ThreadSafeArray */
    private ThreadSafeArray $blockFuncs;

    /** @var ThreadSafeArray */
    private ThreadSafeArray $serializer;

    /** @var ThreadSafeArray */
    private ThreadSafeArray $deserializer;

    /**
     * @param Closure[] $blockFuncs
     * @phpstan-param array<string, array{(Closure(int): Block), (Closure(BlockStateWriter): Block), (Closure(Block): BlockStateReader)}> $blockFuncs
     */
    public function __construct(private string $cachePath, array $blockFuncs) {
        $this->blockFuncs = new ThreadSafeArray();
        $this->serializer = new ThreadSafeArray();
        $this->deserializer = new ThreadSafeArray();

        foreach ($blockFuncs as $identifier => [$blockFunc, $serializer, $deserializer]) {
            $this->blockFuncs[$identifier] = $blockFunc;
            $this->serializer[$identifier] = $serializer;
            $this->deserializer[$identifier] = $deserializer;
        }
    }

    /**
     * @return void
     */
    public function onRun(): void {
        foreach ($this->blockFuncs as $identifier => $blockFunc) {
            CustomiesBlockFactory::getInstance()->registerBlock($blockFunc, $identifier, serializer: $this->serializer[$identifier], deserializer: $this->deserializer[$identifier]);
        }
    }
}
