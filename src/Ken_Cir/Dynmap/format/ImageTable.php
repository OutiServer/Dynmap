<?php

declare(strict_types=1);

namespace Ken_Cir\Dynmap\format;

use Ken_Cir\Dynmap\Main;
use pocketmine\block\VanillaBlocks;
use pocketmine\errorhandler\ErrorToExceptionHandler;

class ImageTable
{
    /** @var Resource[]  */
    private $imageCache = [];

    public function __construct(string $dir) {
        $unknown = [];
        $zyogai = [
            "button",
            "fence",
            "fence_gate",
            "pressure_plate",
            "sign",
            "slab",
            "???",
            "stair",
            "wood",
            "stairs",
            "azure bluet",
            "banner"
        ];

        foreach (VanillaBlocks::getAll() as $block => $id) {
            $name = str_replace(" ", "_", $id->getName());
            if (!file_exists($dir . "images/" . strtolower($name) . ".png")) {
                $result = array_filter($zyogai, function ($zyogai_name) use ($name) {
                    if (str_ends_with(strtolower($name), $zyogai_name)) return true;
                    return false;
                });
                if (count($result) > 0) continue;
                $unknown[] = $id->getName();
                var_dump(strtolower($name));
                continue;
            }

            var_dump($name);
            $this->imageCache[$id->getId()] = imagecreatefrompng($dir . "images/" . strtolower($name) . ".png");
        }

        var_dump(count($unknown));
    }

    public function getImageContentFor(int $id, int $data = 0) {
        if(!isset($this->imageCache[$id])) {
            return $this->imageCache[1];
        }
        return $this->imageCache[$id];
    }
}