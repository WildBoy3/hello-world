<?php

namespace codingschule\Lobbysytem;

use cs\ui\Test\Test;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

class Teleporter extends PluginBase{

    public function onEnable()
    {
        $this->saveResource("warps.yml");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if (!$sender instanceof Player){
            return true;
        }
        switch ($command->getName()){
            case "teleporter":
                if ($sender instanceof Player) {
                    $this->MainUI($sender);
                }

        }
        return true;
    }
    protected $awarp = [];

    public function MainUI(Player $player){
        $cfg = new Config($this->getDataFolder()."warps.yml");
        $warpcfg = $cfg->get("warps");
        $Mainform = new SimpleForm(function (Player $player, int $data = null){
            if ($data === null){
                return true;
            }
            $warpname = $this->awarp[$data];
            switch ($data){
                case $data:
                    $cfg = new Config($this->getDataFolder()."warps.yml");
                    $warpcfg = $cfg->get("warps");
                    $x = $warpcfg[$warpname]["x"];
                    $y = $warpcfg[$warpname]["y"];
                    $z = $warpcfg[$warpname]["z"];
                    $level = $warpcfg[$warpname]["welt"];
                    $player->teleport(new Position($x, $y, $z, $this->getServer()->getLevelByName($level)));
                    $player->sendMessage($x." ".$y." ".$z."  button: ".$warpname." welt: ".$player->getLevel()->getName());
                    return true;


            }
            $this->awarp = [];
            return true;
        });
        $Mainform->setTitle(TF::GOLD." TELEPORTER ");
        foreach ($warpcfg as $warpname => $cords){
            $Mainform->addButton(TF::AQUA.$warpname);
            $this->awarp[] = $warpname;
        }
        $Mainform->sendToPlayer($player);
        return true;

    }
    public function Item(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();
        if ($item == Item::COMPASS){
            $this->MainUI($player);
        }
    }

}
