<?php

namespace test;

use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use codingschule\Lobbysystem\Teleport;

class Main extends PluginBase{
    public function onEnable()
    {
    }
    public function Drop(PlayerDropItemEvent $event){
        $player = $event->getPlayer();
        if ($player->getGamemode() == 0 or $player->getGamemode() == 2){
            $event->setCancelled(true);
        }

    }
}
