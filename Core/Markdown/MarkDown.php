<?php

namespace Markdown;

class MarkDown
{
    private string $raw;

    private string $processed;

    private bool $unTrusted;

    private \Parsedown $library;

    /**
     * @return string
     */
    public function getRaw(): string
    {
        return $this->raw;
    }

    /**
     * @param string $raw
     */
    public function setRaw(string $raw): void
    {
        $this->raw = $raw;
    }

    /**
     * @return string
     */
    public function getProcessed(): string
    {
        return $this->processed;
    }

    /**
     * @param string $processed
     */
    public function setProcessed(string $processed): void
    {
        $this->processed = $processed;
    }

    /**
     * @return bool
     */
    public function isUnTrusted(): bool
    {
        return $this->unTrusted;
    }

    /**
     * @param bool $unTrusted
     */
    public function setUnTrusted(bool $unTrusted): void
    {
        $this->unTrusted = $unTrusted;
    }

    public function __construct()
    {
        $this->raw = "";
        $this->unTrusted = true;
        $this->processed = "";
        $this->library = new \Parsedown();

    }

    public function markDownToHtml(): MarkDown
    {
        $this->library->setSafeMode($this->unTrusted);
        $this->processed = $this->library->text($this->raw);
        return $this;
    }

    public function inLineMarkDownToHtml(): MarkDown
    {
        $this->library->setSafeMode($this->unTrusted);
        $this->processed = $this->library->line($this->raw);
        return $this;
    }


}