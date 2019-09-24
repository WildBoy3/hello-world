<?php

namespace join;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;

use pocketmine\plugin\PluginBase;

use pocketmine\utils\Textformat as Color;

class more extends PluginBase
{
    public $prefix = "§f[§1join§f]";

    public function onEnable(){
    }

    public function onDisable(){
    }

    public function onJoin(PlayerJoinEvent $playerJoinEvent){
        $player = $playerJoinEvent->getPlayer();
        $playerJoinEvent->setJoinMessage($this->prefix . $player->getName() . " hat das Spiel betreten!");
        $this->getItems($player);
    }

    public function onQuit(PlayerQuitEvent $playerQuitEvent){
        $player = $playerQuitEvent->getPlayer();
        $playerQuitEvent->setQuitMessage($this->prefix . $player->getName() . " hat das Spiel verlassen!");
    }

    public function getItems(Player $player){

        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $compass = Item::get(Item::COMPASS);
        $compass->setCustomName("§8Teleporter");
        $hider = Item::get(Item::GOLDEN_CARROT);
        $hider->setCustomName("§fPlayer_Hider");
        $partyui = Item::get(Item::STOCK);
        $partyui->setCustomName("§2Party_UI");
        $profilui = Item::get(Item::STEVE_KOPF);
        $adminshop = Item::get(Item::NETHER_STAR);
        $adminshop->setCustomName("§7Admin UI");

        $player->getInventory()->setItem(4, $compass);
        $player->getInventory()->setItem(1, $hider);
        $player->getInventory()->setItem(2, $partyui);
        $player->getInventory()->setItem(0, $profilui);
        $player->getInventory()->setItem(8, $adminshop);
    }
    
}
