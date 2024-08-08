<?php
declare(strict_types=1);

namespace crash\dump;

use crash\Main;
use neta\class\Embed;
use neta\class\Message;
use neta\Webhook;

class CrashDumpWebhook {

    private string $url;
    private CrashDumpReader $crashDumpReader;

    public function __construct(CrashDumpReader $reader) {
        $this->url = Main::getInstance()->getConfig()->getNested('webhook.url');
        $this->crashDumpReader = $reader;
    }

    public function submit() : void {
        if (!$this->crashDumpReader->hasRead()) {
            return;
        }
        $crashDump = $this->crashDumpReader->getData();

        if($crashDump["uptime"] < 60){
            $uptime = round($crashDump["uptime"], 2)." seconds";
        }elseif($crashDump["uptime"] < 60 ** 2){
            $uptime = round($crashDump["uptime"] / 60)." minutes";
        }elseif($crashDump["uptime"] < 24 * 60 ** 2){
            $uptime = round($crashDump["uptime"] / 3600)." hours";
        }else{
            $uptime = round($crashDump["uptime"] / (24 * 60 ** 2))." days";
        }

        $line = $crashDump['error']['line'];
        $code = $crashDump['code'];
        foreach ($code as $key => $value) {
            $code[$key] = ($key === $line ? ">" : " ")."[" . $key . "] " . $code;
        }
        $codeString = "```php\n";
        $stringEnd = "\n```";
        $codeString .= substr(implode("\n", $code), 0, 1024 - strlen($codeString.$stringEnd)).$stringEnd;

        $data = [
            "Exception Class" => $crashDump["error"]["type"],
            "Erreur" => substr($this->crashDumpReader->getData()["error"]["message"] ?? "Erreur inconnue", 0, 256),
            "Fichier" => "**".$crashDump["error"]["file"]."**",
            "Ligne" => "**".$line."**",
            "Plugin involved" => $crashDump["plugin_involvement"],
            "Plugin" => "**".($crashDump["plugin"] ?? "?")."**",
            "Code" => $codeString,
            "Trace" => "```\n".substr(implode("\n", $crashDump["trace"]), 0, 1024 - strlen("```\n".$stringEnd))."\n```",
            "Date du crash" => $this->crashDumpReader->getCreationTime(),
            "Server Uptime" => $uptime,
            "Server Git Commit" => "__".$crashDump["general"]["git"]."__",
            "PHP Version" => phpversion().((function_exists('opcache_get_status') && ($opcacheStatus = opcache_get_status(false)) !== false && ($opcacheStatus["jit"]["on"] ?? false)) ? " (JIT activé)" : " (JIT désactivé)")
        ];

        $config = Main::getInstance()->getConfig();

        $embed = new Embed();
        $embed->setTitle($config->getNested('webhook.title'))
            ->setColor($config->getNested('webhook.color'))
            ->setFooter("Propulsé par Synopsie", null);
        foreach ($data as $name => $value) {
            $embed->addField($name, $value);
        }
        $embed->setDescription('-# CrashDump - [Synopsie](https://github.com/Synopsie)');
        $message = new Message();
        $message->addEmbed($embed);
        $webhook = new Webhook($this->url, $message);
        $webhook->submit();
    }
}