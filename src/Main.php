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

namespace crash;

use crash\command\CrashCommand;
use crash\dump\CrashDumpReader;
use crash\dump\CrashDumpWebhook;
use InvalidArgumentException;
use olymp\PermissionManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use sofia\Updater;
use Throwable;
use function count;
use function explode;
use function glob;
use function round;
use function trim;
use function unlink;

class Main extends PluginBase {
	use SingletonTrait;

	protected function onLoad() : void {
		self::setInstance($this);
		$this->saveResource('config.yml');
	}

	protected function onEnable() : void {
		require $this->getFile() . 'vendor/autoload.php';

		$config = $this->getConfig();

        Updater::checkUpdate('CrashReporter', $this->getDescription()->getVersion(), 'Synopsie', 'CrashReporter');

		if($config->get('enable.command.crash')) {
			$permissionManager = new PermissionManager();
			$permissionManager->registerPermission(
				$config->getNested('command.permission.name'),
				'Crash',
				$permissionManager->getType($config->getNested('command.permission.default')),
			);
			$this->getServer()->getCommandMap()->register(
				'Synopsie',
				new CrashCommand(
					$config->getNested('command.name'),
					$config->getNested('command.description'),
					$config->getNested('command.usage'),
					[]
				)
			);
		}
		$files   = $this->getCrashdumpFiles();
		$removed = 0;
		foreach($files as $filePath) {
			try {
				$crashDumpReader = new CrashDumpReader($filePath);

				if(!$crashDumpReader->hasRead()) {
					continue;
				}
				unlink($filePath);
				++$removed;
			} catch(Throwable $e) {
				foreach(explode("\n", $e->getTraceAsString()) as $traceString) {
					$this->getLogger()->debug("[ERROR] " . $traceString);
				}
			}
		}

		$fileAmount = count($files);
		$percentage = $fileAmount > 0 ? round($removed * 100 / $fileAmount, 2) : "NAN";

		$message = "Checks finished, Deleted crash dump files: " . $removed . " (" . $percentage . "%)";
		if($removed > 0) {
			$this->getLogger()->notice($message);
		} else {
			$this->getLogger()->info($message);
		}

	}

	protected function onDisable() : void {

		if(trim($this->getConfig()->getNested("webhook.url")) === null) {
			throw new InvalidArgumentException("Webhook url is invalid");
		}

		$files     = $this->getCrashdumpFiles();
		$startTime = (int) $this->getServer()->getStartTime();
		foreach($files as $filePath) {
			try {
				$crashDumpReader = new CrashDumpReader($filePath);

				if(!$crashDumpReader->hasRead() || $crashDumpReader->getCreationTime() < $startTime) {
					continue;
				}
				if($crashDumpReader->hasRead()) {
					$handler = new CrashDumpWebhook($crashDumpReader);
					$handler->submit();
				}
			} catch(Throwable $e) {
				foreach(explode("\n", $e->getTraceAsString()) as $traceString) {
					$this->getLogger()->debug("[ERROR] " . $traceString);
				}
			}
		}
	}

	public function getCrashdumpFiles() : array {
		return glob($this->getServer()->getDataPath() . "crashdumps/*.log");
	}

}
