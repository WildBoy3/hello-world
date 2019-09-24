<?php

namespace codingschule\Lobbysytem;

use pocketmine\event\Listener;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Main extends PluginBase{
    public function onEnable()
    {
        $this->saveResource("spawn.yml");
    }
    
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        switch(strtolower($command->getName())){
            case "spawn":
                $cfg = new Config($this->getDataFolder() . "Spawn.yml", Config::YAML);
                $cfg1 = $cfg->get("spawn");
                $x = $cfg1["x"];
                $y = $cfg1["y"];
                $z = $cfg1["z"];
                $level = $cfg1["welt"];
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
