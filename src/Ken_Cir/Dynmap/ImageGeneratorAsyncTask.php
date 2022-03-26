<?php

declare(strict_types=1);

namespace Ken_Cir\Dynmap;

use Ken_Cir\Dynmap\format\ImageTable;
use pocketmine\block\BlockFactory;
use pocketmine\block\VanillaBlocks;
use pocketmine\scheduler\AsyncTask;
use pocketmine\world\format\io\FastChunkSerializer;
use function imagecopy;
use function imagepng;

class ImageGeneratorAsyncTask extends AsyncTask
{
    private string $chunkStr;

    private string $dir;

    public function __construct(Main $plugin)
    {
        $chunks = [];
        foreach ($plugin->getServer()->getWorldManager()->getDefaultWorld()->getLoadedChunks() as $key => $loadedChunk) {
           $chunks[$key] = FastChunkSerializer::serializeTerrain($loadedChunk);
        }

        $this->chunkStr = serialize($chunks);
        $this->dir = $plugin->getDataFolder();
    }

    public function onRun(): void
    {
        $chunks = unserialize($this->chunkStr);
        $imageTable = new ImageTable($this->dir);

        foreach ($chunks as $key => $chunk) {
            $base = imagecreatetruecolor(273, 273);
            $blockStone = [];
            $chunk = FastChunkSerializer::deserializeTerrain($chunk);

            for($x = 0; $x < 16; $x++) {
                for ($z = 0; $z < 16; $z++) {
                    if ($chunk->getHighestBlockAt($x, $z) !== null) {
                        $block = BlockFactory::getInstance()->fromFullBlock($chunk->getFullBlock($x, $chunk->getHighestBlockAt($x, $z), $z));
                    }
                    else {
                        $block = VanillaBlocks::AIR();
                    }

                    $blockStone[] = $imageTable->getImageContentFor($block->getId());
                }
            }

            foreach ($blockStone as $index => $block) {
                if($block === null) continue;
                imagecopy($base, $block,  1 + 17 * ($index & 0x0f), 1 + 17 * ($index >> 4), 0, 0, 16, 16);
            }

            imagepng($base, $this->dir . "$key.png");
        }
    }
}