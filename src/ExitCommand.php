<?php

namespace Katsana\Prefetch;

class ExitCommand
{
    /**
     * Data payload.
     *
     * @var \Katsana\Prefetch\Data|null
     */
    protected $data;

    /**
     * Construct a new ExitCommand.
     *
     * @param \Katsana\Prefetch\Data|null $data
     */
    public function __contruct(?Data $data = null)
    {
        $this->data = $data;
    }

    /**
     * Convert message to SSE payload.
     */
    public function __toString()
    {
        if ($this->data instanceof Data) {
            return (string) $this->data;
        }

        return "\n";
    }
}
