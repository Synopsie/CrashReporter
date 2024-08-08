<?php
declare(strict_types=1);

namespace crash\command;

use crash\Main;
use crash\utils\CrashException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;

class CrashCommand extends Command {

    public function __construct(string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = []) {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->setPermission(Main::getInstance()->getConfig()->getNested('command.permission.name'));
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        throw new CrashException("Volontary crash");
    }
}