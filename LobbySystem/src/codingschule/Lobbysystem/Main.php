<?php

namespace test;

use pocketmine\block\Block;
use pocketmine\block\Dirt;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener{
    public $data;
    public function onEnable()
    {
        $this->getLogger()->info("teeest");
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
    }
    public function onBreak(BlockBreakEvent $breakEvent){
        $block = $breakEvent->getBlock();
        $player = $breakEvent->getPlayer();
        $yourblock = $breakEvent->getBlock()->getName();
        if ($block->getId() === Block::DIRT){
            $blockname = $block->getName();

            $player->sendMessage("Du hast gerade ein block abgebaut: $blockname");
        }
        else{
            $player->sendMessage(TextFormat::RED."Falscher block. gesucht: dirt. Dein block:".$yourblock);
            return true;
        }
    return false;
    }
}