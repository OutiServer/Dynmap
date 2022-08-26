<?php

declare(strict_types=1);

namespace Ken_Cir\Dynmap;

use pocketmine\block\BlockFactory;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\LegacyBlockIdToStringIdMap;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\ItemFactory;
use pocketmine\plugin\PluginBase;
use pocketmine\world\World;

class Main extends PluginBase implements Listener
{
    protected function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    protected function onDisable(): void
    {
    }

    public function onJoin(PlayerJoinEvent $event)
    {
        $this->getServer()->getAsyncPool()->submitTask(new ImageGeneratorAsyncTask($this));
    }
}