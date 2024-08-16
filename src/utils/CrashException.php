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
 * @version 1.1.0
 *
 */

declare(strict_types=1);

namespace crash\utils;

use Exception;

class CrashException extends Exception {
}
