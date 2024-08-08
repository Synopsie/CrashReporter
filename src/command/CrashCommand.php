<?php

/*
 *  ____   __   __  _   _    ___    ____    ____    ___   _____
 * / ___|  \ \ / / | \ | |  / _ \  |  _ \  / ___|  |_ _| | ____|
 * \___ \   \ V /  |  \| | | | | | | |_) | \___ \   | |  |  _|
 *  ___) |   | |   | |\  | | |_| | |  __/   ___) |  | |  | |___
 * |____/    |_|   |_| \_|  \___/  |_|     |____/  |___| |_____|
 *
 * Ce plugin permet de signaler quand un crash survient sur votre serveur.
 *
 * @author Synopsie
 * @link https://github.com/Synopsie
 * @version 1.0.0
 *
 */

declare(strict_types=1);

namespace crash\command;

use crash\Main;
use crash\utils\CrashException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;

class CrashCommand extends Command {
	public function __construct(string $name, string|Translatable $description = "", null|string|Translatable $usageMessage = null, array $aliases = []) {
		parent::__construct($name, $description, $usageMessage, $aliases);
		$this->setPermission(Main::getInstance()->getConfig()->getNested('command.permission.name'));
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) {
		throw new CrashException("Volontary crash");
	}
}
