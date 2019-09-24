<?php

namespace codingschule\Lobbysystem;

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

class Admin extends PluginBase implements Listener
{

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

    }

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        switch (strtolower($command->getName())){
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
        }

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

}
