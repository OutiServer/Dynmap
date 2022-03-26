<?php

declare(strict_types=1);

namespace Ken_Cir\Dynmap\format;

use Ken_Cir\Dynmap\Main;
use pocketmine\block\VanillaBlocks;

class ImageTable
{
    /** @var Resource[]  */
    private $imageCache = [];

    public function __construct(string $dir) {
        foreach (VanillaBlocks::getAll() as $block => $id) {
            if (!file_exists($dir . "images/" . strtolower($id->getName()) . ".png")) {
                continue;
            }

            $this->imageCache[$id->getId()] = imagecreatefrompng($dir . "images/" . strtolower($id->getName()) . ".png");
        }
    }

    public function getImageContentFor(int $id, int $data = 0) {
        if(!isset($this->imageCache[$id])) {
            return $this->imageCache[1];
        }
        return $this->imageCache[$id];
    }
}