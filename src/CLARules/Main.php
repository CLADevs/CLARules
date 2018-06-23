<?php

/*
 * CLARules, a public rules plugin for PocketMine-MP
 * Copyright (C) 2017-2018 CLADevs
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY;  without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

declare(strict_types=1);

namespace CLARules;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\item\WrittenBook;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Main extends PluginBase{

    private const VERSION = "v1.0.2";
    private const PREFIX = TextFormat::GREEN . "CLARules" . TextFormat::GOLD . " > ";

    public function onEnable() : void{
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->getLogger()->info("CLARules " . self::VERSION . " by CLADevs is enabled");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if($command->getName() === "rules"){
            if(!$sender instanceof Player){
                $sender->sendMessage(self::PREFIX . TextFormat::RED . "Use this command in-game");
                return false;
            }
            if(!$sender->hasPermission("rules.command")){
                $sender->sendMessage(self::PREFIX . TextFormat::RED . "You do not have permission to use this command");
                return false;
            }
            if($this->getConfig()->get("rules-type") === "book"){
                if($sender->getInventory()->canAddItem(Item::get(Item::WRITTEN_BOOK, 0, 1))){
                    count($this->getConfig()->get("rules-pages")) > 0 ? $this->bookRules($sender) : $sender->sendMessage(TextFormat::RED . str_replace(["&", "{line}"], ["§", "\n"], $this->getConfig()->get("rules-not-found")));
                }else{
                    $sender->sendMessage(TextFormat::RED . str_replace(["&", "{line}"], ["§", "\n"], $this->getConfig()->get("your-inventory-full")));
                }
            }elseif($this->getConfig()->get("rules-type") === "message"){
                count($this->getConfig()->get("messages")) > 0 ? $this->messageRules($sender) : $sender->sendMessage(TextFormat::RED . str_replace(["&", "{line}"], ["§", "\n"], $this->getConfig()->get("rules-not-found")));
            }
        }
        return true;
    }

    private function bookRules(Player $player) : void{
        /** @var WrittenBook $book */
        $book = Item::get(Item::WRITTEN_BOOK, 0, 1);
        $book->setTitle(str_replace(["&", "{line}"], ["§", "\n"], $this->getConfig()->get("rules-title")));
        $book->setAuthor(str_replace(["&", "{line}"], ["§", "\n"], $this->getConfig()->get("rules-author")));

        $rules = $this->getConfig()->get("rules-pages");
        for($i = 0; $i < count($rules); $i++){
            $book->setPageText($i, str_replace(["&", "{line}"], ["§", "\n"], $rules[$i]));
        }
        
        $player->getInventory()->addItem($book);
        $player->sendMessage(TextFormat::GREEN . str_replace(["&", "{line}"], ["§", "\n"], $this->getConfig()->get("book-give-success-message")));
    }

    private function messageRules(Player $player) : void{
        foreach($this->getConfig()->get("messages") as $message) $player->sendMessage(str_replace(["&", "{line}"], ["§", "\n"], $message));
    }
}