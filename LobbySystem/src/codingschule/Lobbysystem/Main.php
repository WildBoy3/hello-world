<?php

namespace test;

use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use codingschule\Lobbysystem\Teleport;

class Main extends PluginBase{
    public function onEnable()
    {
        $cfg = new Config($this->getDataFolder() . "Spawn.yml", Config::YAML);
    }
    
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        switch($command->getName()){
            case "spawn":
                $cfg = new Config($this->getDataFolder() . "Spawn.yml", Config::YAML);
                $cfg
        }
        return true;
    }
    public function Drop(PlayerDropItemEvent $event){
        $player = $event->getPlayer();
        if ($player->getGamemode() == 0 or $player->getGamemode() == 2){
            $event->setCancelled(true);
        }

    }
}
