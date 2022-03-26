<?php

declare(strict_types=1);

namespace Ken_Cir\Dynmap\format;

class Generator2DChunk
{
    /** @var int */
    private int $chunkX = 0;
    /** @var int */
    private int $chunkZ = 0;
    /** @var resource */
    private $base = null;
    /** @var array */
    private array $blockData = [];

    public function __construct(int $x, int $z, array $blockStone, ImageTable $imageTable) {
        $this->chunkX = $x;
        $this->chunkZ = $z;

        $this->base = imagecreatetruecolor(self::getWidth(), self::getWidth());

        $this->addData($blockStone);
    }

    /**
     * @return int
     */
    public function getX(): int {
        return $this->chunkX;
    }

    /**
     * @return int
     */
    public function getZ(): int {
        return $this->chunkZ;
    }

    /**
     * @param array $data
     */
    public function addData(array $data): void {
        $this->blockData = $data;
    }

    /**
     * @return int
     */
    public static function getWidth(): int {
        return 273;
    }

    /**
     * @return resource
     */
    public function getImage() {
        foreach($this->blockData as $index => $image) {
            if($image === null) continue;
            imagecopy($this->base, $image, 1 + 17 * ($index & 0x0f), 1 + 17 * ($index >> 4), 0, 0, 16, 16);
        }
        return $this->base;
    }
}