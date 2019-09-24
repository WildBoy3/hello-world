<?php

namespace codingschule\Lobbysytem;

use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use codingschule\Lobbysystem\Teleport;

class Main extends PluginBase{
    public function onEnable()
    {
        $this->saveResource("Spawn.yml");
    }
    
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        switch(strtolower($command->getName())){
            case "spawn":
                $cfg = new Config($this->getDataFolder() . "Spawn.yml", Config::YAML);
                $cfg1 = $cfg->get("spawn");
                $x = $cfg["x"];
                $y = $cfg["y"];
                $z = $cfg["z"];
                $level = $cfg["welt"];
                $sender->teleport(new Position($x, $y, $z, $level));
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
