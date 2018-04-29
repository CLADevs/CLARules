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
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Main extends PluginBase{

    const VERSION = "v1.0.0";

    public function onEnable() : void{
        $this->getLogger()->info("CLARules " . self::VERSION . " by CLADevs is enabled");
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if($command->getName() === "rules"){
            if(!$sender instanceof Player){
                $sender->sendMessage(TextFormat::RED . "Use this command in-game");
                return false;
            }
            if(!$sender->hasPermission("rules.command")){
                $sender->sendMessage(TextFormat::RED . "You do not have permission to use this command");
                return false;
            }
            if($this->getConfig()->get("rules-type") === "book"){
                $this->giveBookRules($sender);
            }elseif($this->getConfig()->get("rules-type") === "message"){
                $this->messageRules($sender);
                return false;
            }
        }
        return true;
    }

    private function giveBookRules(Player $player) : void{
        $book = Item::get(Item::WRITTEN_BOOK, 0, 1);
        $book->setTitle(str_replace(["&", "{line}"], ["§", "\n"], $this->getConfig()->get("rules-title")));
        $book->setPageText(0, str_replace(["&", "{line}"], ["§", "\n"], $this->getConfig()->get("rules-page1")));
        $book->setPageText(1, str_replace(["&", "{line}"], ["§", "\n"], $this->getConfig()->get("rules-page2")));
        $book->setPageText(2, str_replace(["&", "{line}"], ["§", "\n"], $this->getConfig()->get("rules-page3")));
        $book->setPageText(3, str_replace(["&", "{line}"], ["§", "\n"], $this->getConfig()->get("rules-page4")));
        $book->setPageText(4, str_replace(["&", "{line}"], ["§", "\n"], $this->getConfig()->get("rules-page5")));
        $book->setAuthor(str_replace(["&", "{line}"], ["§", "\n"], $this->getConfig()->get("rules-author")));
        $player->getInventory()->addItem($book);
        $player->sendMessage(str_replace(["&", "{line}"], ["§", "\n"], $this->getConfig()->get("book-give-success-message")));
    }

    private function messageRules(Player $player) : void{
        $messages = [
            str_replace(["&", "{line}"], ["§", "\n"], $this->getConfig()->get("message-1")),
            str_replace(["&", "{line}"], ["§", "\n"], $this->getConfig()->get("message-2")),
            str_replace(["&", "{line}"], ["§", "\n"], $this->getConfig()->get("message-3")),
            str_replace(["&", "{line}"], ["§", "\n"], $this->getConfig()->get("message-4")),
            str_replace(["&", "{line}"], ["§", "\n"], $this->getConfig()->get("message-5"))
        ];
        foreach($messages as $message) $player->sendMessage($message);
    }
}