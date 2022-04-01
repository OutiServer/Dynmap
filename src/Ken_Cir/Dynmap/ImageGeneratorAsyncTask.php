<?php

declare(strict_types=1);

namespace Ken_Cir\Dynmap;

use Ken_Cir\Dynmap\format\ImageTable;
use pocketmine\block\BlockFactory;
use pocketmine\block\VanillaBlocks;
use pocketmine\scheduler\AsyncTask;
use pocketmine\world\format\io\FastChunkSerializer;
use pocketmine\world\World;
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
        $chunkImages = [];
        $minWidth = 0;
        $maxWidth = 0;
        $minHeight = 0;
        $maxHeight = 0;
        $processedChunks = 0;

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

            World::getXZ($key, $x, $z);
            $chunkImages[$key] = $base;
            if ($x > $maxWidth) {
                $maxWidth = $x;
            }
            elseif ($x < $minWidth) {
                $minWidth = $x;
            }

            if ($z > $maxHeight) {
                $maxHeight = $z;
            }
            elseif ($z < $minHeight) {
                $minHeight= $z;
            }

            #World::getXZ($key, $x, $z);
            #imagepng($base, $this->dir . "h$key-x$x-z$z.png");
        }

        $totalChunks = count($chunkImages);
        $totalWidth = ($maxWidth - $minWidth) * 273;
        $totalHeight = ($maxHeight - $minHeight) * 273;
        $newImage = imagecreatetruecolor($totalWidth, $totalHeight);
        foreach ($chunkImages as $hash => $chunkImage) {
            World::getXZ($hash, $x, $z);
            #imagecopy($newImage, $chunk->getImage(), $chunk::getWidth() * $chunk->getX(), $chunk::getWidth() * $chunk->getZ(), 0, 0, $chunk::getWidth(), $chunk::getWidth());
            imagecopy($newImage, $chunkImage, 273 * $x, 273 * $z, 0, 0, 273, 273);
            var_dump($processedChunks++ . "/" . $totalChunks);
        }

        imagepng($newImage, $this->dir . "dynmap.png");
    }
}