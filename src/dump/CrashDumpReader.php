<?php
declare(strict_types=1);

namespace crash\dump;

use crash\utils\Date;
use RuntimeException;

class CrashDumpReader {

    private string $filePath;
    private ?array $data = null;

    public function __construct(string $filePath) {
        $this->filePath = $filePath;
        $this->read();
    }

    private function read() : void {
        $file = fopen($this->filePath, 'r');
        $start = false;
        $end = false;
        $data = '';
        while(($line = fgets($file)) !== false) {
            if(str_contains($line, '===BEGIN CRASH DUMP===')) {
                $start = true;
                continue;
            }
            if($start) {
                if(str_contains($line, '===END CRASH DUMP===')) {
                    $end = true;
                    break;
                }
                $data .= $line;
            }
        }
        fclose($file);
        if ($start && $end && trim($data) !== '') {
            $data = base64_decode($data);
            $data = zlib_decode($data);
            $this->data = json_decode($data, true);
        }
    }

    public function hasRead() : bool {
        return $this->data !== null;
    }

    public function getData() : ?array {
        return $this->data;
    }

    public function getFilePath() : string {
        return $this->filePath;
    }

    public function getCreationTime() : string {
        if (!$this->hasRead()) {
            throw new RuntimeException('Data has not been read');
        }
        return Date::create((int)$this->data['time'])->__toString();
    }

}