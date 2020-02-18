<?php

namespace Katsana\Prefetch\Tests\Stubs;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Katsana\Prefetch\Contracts\LoopUntil;
use Katsana\Prefetch\ExitCommand;
use Katsana\Prefetch\Handler;

class PingWithLoopHandler extends Handler implements LoopUntil
{
    protected $data = ['foo', 'bar'];

    public function collection(Request $request)
    {
        return Collection::make($this->data);
    }

    public function onLoopEnded(): void
    {
        $this->data = [
            'foobar',
            new ExitCommand(),
            'hello',
        ];
    }
}
