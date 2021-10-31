<?php


namespace App\data;


use DateTime;

class VersionInfo
{
    private string $version;
    private DateTime $date;
    private array $changes;

    public function __construct(string $version, DateTime $date, array $changes)
    {
        $this->version = $version;
        $this->date    = $date;
        $this->changes = $changes;
    }

    function getVersion(): string   { return $this->version; }
    function getDate   (): DateTime { return $this->date   ; }
    function getChanges(): array    { return $this->changes; }
}
