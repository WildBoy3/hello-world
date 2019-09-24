<?php

namespace codingschule\Lobbysystem;

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;
use codingschule\Lobbysystem\Teleport;

class Main extends PluginBase implements Listener{

    public function onEnable()
    {
        $this->saveResource("warps.yml");
        $this->saveResource("Spawn.yml");
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
    }

    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $cfg = new Config($this->getDataFolder()."Admin.yml",Config::YAML);
        $cfg1 = $cfg->get("chat");
        $cfg1[$player->getName()] = false;
        $cfg->set("chat",$cfg1);

        $cfg1 = $cfg->get("fly");
        $cfg1[$player->getName()] = false;
        $cfg->set("fly",$cfg1);
        $player->setAllowFlight(false);

        $cfg1 = $cfg->get("vanish");
        $cfg1[$player->getName()] = false;
        $cfg->set("vanish",$cfg1);
        foreach ($this->getServer()->getOnlinePlayers() as $onlinePlayer) {
            $onlinePlayer->showPlayer($player);
            $player->setNameTagVisible(false);
            $player->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INVISIBLE, false);
        }

        $player = $event->getPlayer();
        $cfg = new Config($this->getDataFolder() . "Spawn.yml", Config::YAML);
        $cfg1 = $cfg->get("spawn");
        $x = $cfg1["x"];
        $y = $cfg1["y"];
        $z = $cfg1["z"];
        $level = $cfg1["welt"];
        $player->teleport(new Position($x, $y, $z, $this->getServer()->getLevelByName($level)));
        $event->setJoinMessage(TF::GOLD . $player->getName() . " hat das Spiel betreten!");
        $this->getItems($player);

        $cfg = new Config($this->getDataFolder()."item.yml");
        $cfg1 = $cfg->get("carrot");
        $cfg1[$player->getName()] = false;
        $cfg->set("carrot", $cfg1);
        $cfg->save();

    }
    public function onQuit(PlayerQuitEvent $playerQuitEvent){
        $player = $playerQuitEvent->getPlayer();
        $playerQuitEvent->setQuitMessage(TF::GOLD. $player->getName() . " hat das Spiel verlassen!");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if (!$sender instanceof Player) {
            return true;
        }
        switch(strtolower($command->getName())){
            case "spawn":
                if ($sender instanceof Player) {
                    $cfg = new Config($this->getDataFolder() . "Spawn.yml", Config::YAML);
                    $cfg1 = $cfg->get("spawn");
                    $x = $cfg1["x"];
                    $y = $cfg1["y"];
                    $z = $cfg1["z"];
                    $level = $cfg1["welt"];
                    $sender->teleport(new Position($x, $y, $z, $this->getServer()->getLevelByName($level)));
                }
                return true;
            case "admin":
                if ($sender instanceof Player) {
                    if (empty($args[0])) {
                        if ($sender->hasPermission("admin.command")) {
                            $this->AdminUI($sender);
                        } else {
                            $sender->sendMessage(TF::RED . "Du hast die Berechtigung dafür nicht");
                            return true;
                        }
                    } else {
                        if (strtolower($args[0]) == "chat" or strtolower($args[0]) == "tc" or strtolower($args[0]) == "teamchat" or strtolower($args[0]) == "c") {
                            $cfg = new Config($this->getDataFolder() . "Admin.yml", Config::YAML);
                            if ($cfg->get("chat")[$sender->getName()] == true) {
                                $cfg1 = $cfg->get("chat");
                                $cfg1[$sender->getName()] = false;
                                $cfg->set("chat", $cfg1);
                                $cfg->save();
                                $sender->sendMessage(TF::DARK_AQUA . "Teamchat" . TF::DARK_RED . " wurde Deaktiviert");
                                return true;
                            }
                            if ($cfg->get("chat")[$sender->getName()] == false) {
                                $cfg1 = $cfg->get("chat");
                                $cfg1[$sender->getName()] = true;
                                $cfg->set("chat", $cfg1);
                                $cfg->save();
                                $sender->sendMessage(TF::DARK_AQUA . "Teamchat" . TF::DARK_GREEN . " wurde Aktiviert");
                                return true;
                            }
                        }
                        if (strtolower($args[0]) == "v" or strtolower($args[0]) == "vanish") {
                            $cfg = new Config($this->getDataFolder()."Admin.yml",Config::YAML);
                            $cfg1 = $cfg->get("vanish");
                            foreach ($this->getServer()->getOnlinePlayers() as $onlinePlayer) {
                                if ($cfg1[$sender->getName()] == false) {
                                    $onlinePlayer->hidePlayer($sender);
                                    $sender->setNameTagVisible(true);
                                    $sender->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INVISIBLE, true);
                                    $cfg1[$sender->getName()] = true;
                                    $cfg->set("vanish", $cfg1);
                                    $cfg->save();
                                    $sender->sendMessage(TF::DARK_GREEN."Unsichtbarkeit aktiviert");
                                    return true;
                                }
                                if ($cfg1[$sender->getName()] == true) {
                                    $onlinePlayer->showPlayer($sender);
                                    $sender->setNameTagVisible(false);
                                    $sender->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INVISIBLE, false);
                                    $cfg1[$sender->getName()] = false;
                                    $cfg->set("vanish", $cfg1);
                                    $cfg->save();
                                    $sender->sendMessage(TF::DARK_RED."Unsichtbarkeit deaktviert");
                                    return true;
                                }
                                return true;
                            }
                        }
                        if (strtolower($args[0]) == "fly" or strtolower($args[0] == "f")) {
                            $cfg = new Config($this->getDataFolder()."Admin.yml",Config::YAML);
                            $cfg1 = $cfg->get("fly");
                            if ($cfg1[$sender->getName()] == true) {
                                $sender->setAllowFlight(false);
                                $cfg1[$sender->getName()] = false;
                                $cfg->set("fly", $cfg1);
                                $cfg->save();
                                $sender->sendMessage(TF::DARK_RED."Fliegen deaktiviert");
                                return true;
                            }
                            if ($cfg1[$sender->getName()] == false) {
                                $sender->setAllowFlight(true);
                                $cfg1[$sender->getName()] = true;
                                $cfg->set("fly", $cfg1);
                                $cfg->save();
                                $sender->sendMessage(TF::DARK_GREEN."Fliegen aktiviert");
                                return true;
                            }
                        }
                        if (strtolower($args[0]) == "help"){
                            if (!empty($args[1])) {
                                if (strtolower($args[1]) == "teamchat") {
                                    $sender->sendMessage(" ");
                                    $sender->sendMessage(TF::YELLOW . "Mögliche Teamchat commands:");
                                    $sender->sendMessage(TF::WHITE . " /admin tc");
                                    $sender->sendMessage(TF::WHITE . " /admin c");
                                    $sender->sendMessage(TF::WHITE . " /admin chat");
                                    $sender->sendMessage("  ");
                                    $sender->sendMessage(TF::GOLD . "Alles auch möglich mit der Admin UI, mit /admin");
                                    $sender->sendMessage("  ");
                                    return true;
                                }
                                if (strtolower($args[1]) == "vanish") {
                                    $sender->sendMessage(" ");
                                    $sender->sendMessage(TF::YELLOW . "Mögliche Vanish commands:");
                                    $sender->sendMessage(TF::WHITE . " /admin v");
                                    $sender->sendMessage(TF::WHITE . " /admin vanish");
                                    $sender->sendMessage("  ");
                                    $sender->sendMessage(TF::GOLD . "Alles auch möglich mit der Admin UI, mit /admin");
                                    $sender->sendMessage("  ");
                                    return true;
                                }
                                if (strtolower($args[1]) == "fly") {
                                    $sender->sendMessage(" ");
                                    $sender->sendMessage(TF::YELLOW . "Mögliche Fly commands:");
                                    $sender->sendMessage(TF::WHITE . " /admin fly");
                                    $sender->sendMessage(TF::WHITE . " /admin f");
                                    $sender->sendMessage("  ");
                                    $sender->sendMessage(TF::GOLD . "Alles auch möglich mit der Admin UI, mit /admin");
                                    $sender->sendMessage("  ");
                                    return true;
                                }
                            }
                            $sender->sendMessage("  ");
                            $sender->sendMessage(TF::YELLOW."Help | Genauere helps mit:");
                            $sender->sendMessage(TF::WHITE." /admin help teamchat");
                            $sender->sendMessage(TF::WHITE." /admin help vanish");
                            $sender->sendMessage(TF::WHITE." /admin help fly");
                            $sender->sendMessage("   ");
                            $sender->sendMessage(TF::YELLOW."Commands: ");
                            $sender->sendMessage(TF::WHITE." /admin teamchat");
                            $sender->sendMessage(TF::WHITE." /admin vanish");
                            $sender->sendMessage(TF::WHITE." /admin fly");
                            $sender->sendMessage(TF::WHITE." /admin help");
                            $sender->sendMessage(" ");
                            $sender->sendMessage(TF::GOLD."Alles auch möglich mit der Admin UI, mit /admin");
                            $sender->sendMessage("  ");
                            return true;

                        }
                    }
                }
                return true;
            case "teleporter":
                if ($sender instanceof Player) {
                    $this->MainUI($sender);
                }
        }
        return true;
    }

    public $awarp = [];

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

    public $onlinep = [];

    public function AdminUI(Player $player){
        $cfg = new Config($this->getDataFolder()."Admin.yml",Config::YAML);
        $cfg1 = $cfg->get("chat");
        if (empty($cfg1[$player->getName()])) {
            $cfg1[$player->getName()] = false;
            $cfg->set("chat",$cfg1);
            $cfg->save();
        }
        $cfg1 = $cfg->get("fly");
        if (empty($cfg1[$player->getName()])) {
            $cfg1[$player->getName()] = false;
            $cfg->set("fly",$cfg1);
            $cfg->save();
        }
        $cfg1 = $cfg->get("vanish");
        if (empty($cfg1[$player->getName()])) {
            $cfg1[$player->getName()] = false;
            $cfg->set("vanish",$cfg1);
            $cfg->save();
        }
        $sform = new SimpleForm(function (Player $player, int $data = null) {

            if ($data === null){
                return true;
            }

            switch ($data) {
                case 0:
                    $form = new SimpleForm(function (Player $player, int $data = null){
                        if ($data === null){
                            return true;
                        }
                        $onlinep = $this->onlinep[$data];
                        switch ($data){
                            case $data:
                                if (!$onlinep instanceof Player){
                                    $player->sendMessage(TF::RED."Dieser Spieler ist kein Spieler oder nicht online!");
                                    return true;
                                }
                                if ($onlinep instanceof Player){
                                    if ($onlinep->getName() == $player->getName()){
                                        $player->sendMessage(TF::RED."Du kannst dich nicht zu dir selbst teleportieren");
                                        return true;
                                    }
                                    $player->teleport(new Position($onlinep->getX(), $onlinep->getY(), $onlinep->getZ(), $onlinep->getLevel()));
                                    $player->sendMessage(TF::YELLOW."Du hast dich zu ".TF::AQUA.$onlinep->getName().TF::YELLOW. " teleportiert" );
                                    return true;
                                }
                        }

                        return true;

                    });
                    $form->setTitle(TF::AQUA . "AdminUI");
                    $form->setContent(TF::GRAY."Wähle ein Spieler zudem du dich teleportieren möchtest");
                    foreach ($this->getServer()->getOnlinePlayers() as $onlinePlayer){
                        $form->addButton($onlinePlayer->getName());
                        $this->onlinep[] = $onlinePlayer->getPlayer();
                    }
                    $form->sendToPlayer($player);
                    return true;
                case 1:
                    $form = new CustomForm(function (Player $player, array $data = null) {
                        $cfg = new Config($this->getDataFolder()."Admin.yml",Config::YAML);
                        if ($data === null) {
                            return true;
                        }
                        $player->setAllowFlight($data[0]);
                        $cfg1 = $cfg->get("fly");
                        $cfg1[$player->getName()] = $data[0];
                        $cfg->set("fly", $cfg1);
                        $player->setNameTagVisible($data[1]);
                        $cfg1 = $cfg->get("vanish");
                        $cfg1[$player->getName()] = $data[1];
                        $cfg->set("vanish", $cfg1);
                        $player->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INVISIBLE, $data[1]);
                        foreach ($this->getServer()->getOnlinePlayers() as $onlinePlayer) {
                            if ($data[1] === true){
                                $onlinePlayer->hidePlayer($player);
                            }
                            if ($data[1] === false){
                                $onlinePlayer->showPlayer($player);
                            }
                        }
                        $cfg1 = $cfg->get("chat");
                        $cfg1[$player->getName()] = $data[2];
                        $cfg->set("chat", $cfg1);
                        $cfg->save();
                        $player->sendMessage(TF::GREEN."Admin Eintellungen wurden geändert");
                        return true;
                    });
                    $cfg = new Config($this->getDataFolder()."Admin.yml",Config::YAML);
                    $form->setTitle(TF::AQUA . "AdminUI");
                    $cfg1 = $cfg->get("fly");
                    $cfg1 = $cfg1[$player->getName()];
                    $form->addToggle(TF::RED . "Fly",$cfg1);
                    $cfg1 = $cfg->get("vanish");
                    $cfg1 = $cfg1[$player->getName()];
                    $form->addToggle("Vanish",$cfg1);
                    $cfg1 = $cfg->get("chat");
                    $cfg1 = $cfg1[$player->getName()];
                    $form->addToggle("TeamChat",$cfg1);
                    $form->sendToPlayer($player);
            }
            return true;
        });
        $sform->setTitle(TF::AQUA . "AdminUI");
        $sform->setContent(TF::GRAY."Deine Admin Hilfe. Wähle aus");
        $sform->addButton(TF::YELLOW."Teleportieren");
        $sform->addButton(TF::YELLOW."Einstellungen anpassen");
        $sform->sendToPlayer($player);
    }

    public function Teamchat(PlayerChatEvent $event){
        $player = $event->getPlayer();
        $cfg = new Config($this->getDataFolder()."Admin.yml",Config::YAML);
        $cfg1 = $cfg->get("chat");
        if ($cfg1[$player->getName()] == true){
            $emfpang = $event->getRecipients();
            foreach ($emfpang as $p){
                if ($p->hasPermission("admin.command")) {
                    $p->sendMessage(TF::GRAY . "[" . TF::DARK_AQUA . "Teamchat" . TF::GRAY . "] ".TF::AQUA.$player->getName().TF::GRAY." > " . TF::WHITE . $event->getMessage());
                    $event->setCancelled(true);
                }
            }

        }

    }

    public function Drop(PlayerDropItemEvent $event){
        $player = $event->getPlayer();
        if ($player->getGamemode() == 0 or $player->getGamemode() == 2){
            $event->setCancelled(true);
        }

    }

    public function Item(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $cfg = new Config($this->getDataFolder()."item.yml");
        $cfg1 = $cfg->get("carrot");

        $item = $player->getInventory()->getItemInHand();
        if ($item->getId() == 399){
            $this->AdminUI($player);
            return true;
        }
        if ($item->getId() == 345){
            $this->MainUI($player);
            return true;
        }
        if ($item->getId() == 396){
            foreach ($this->getServer()->getOnlinePlayers() as $onlinePlayer){
                if ($cfg1[$player->getName()] == false){
                    $player->hidePlayer($onlinePlayer);
                    $cfg1[$player->getName()] = true;
                    $onlinePlayer->setNameTagVisible(true);
                    $cfg->set("carrot", $cfg1);
                    $player->sendMessage(TF::GOLD."Alle andere Spieler sind für dich nun Unsichtbar");
                    $cfg->save();
                    return true;
                }
                if ($cfg1[$player->getName()] == true){
                    $player->showPlayer($onlinePlayer);
                    $onlinePlayer->setNameTagVisible(false);
                    $cfg1[$player->getName()] = false;
                    $cfg->set("carrot", $cfg1);
                    $player->sendMessage(TF::GOLD."Alle andere Spieler sind für dich nun Sichtbar");
                    $cfg->save();
                    return true;
                }
            }
        }
        return true;
    }

    public function getItems(Player $player){

        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $compass = Item::get(Item::COMPASS);
        $compass->setCustomName("§8Teleporter");
        $hider = Item::get(Item::GOLDEN_CARROT);
        $hider->setCustomName("§8Player_Hider");
        $adminshop = Item::get(Item::NETHER_STAR);
        $adminshop->setCustomName("§7Admin UI");

        $player->getInventory()->setItem(4, $compass);
        $player->getInventory()->setItem(1, $hider);
        $player->getInventory()->setItem(8, $adminshop);
    }
}
