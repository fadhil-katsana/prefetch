<?php

namespace Katsana\Prefetch;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class Controller extends \Illuminate\Routing\Controller
{
    use Concerns\DisableBuffering;

    /**
     * Should exit request.
     *
     * @var bool
     */
    protected $shouldExit = false;

    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        \ini_set('max_execution_time', 0);
        $this->disableOutputBuffering();

        $handler = \app()->make($request->route()->defaults['handler']);

        return Response::stream($this->streamResolver($handler, $request), 200, [
            'Content-Type' => 'text/event-stream',
            'X-Accel-Buffering' => 'no',
            'Cache-Control' => 'no-cache',
        ]);
    }

    /**
     * Stream resolver.
     */
    protected function streamResolver(Handler $handler, Request $request): Closure
    {
        return function () use ($handler, $request) {
            do {
                $handler->collection($request)
                    ->map(static function ($data) use ($handler) {
                        if ($data instanceof ExitCommand) {
                            return $data;
                        }

                        return $handler->transform($data);
                    })->each(function ($data) {
                        if ($data instanceof ExitCommand) {
                            $this->shouldExit = true;

                            return false;
                        }

                        echo (string) $data;
                    });

                $handler->onLoopEnded();
            } while ($handler instanceof Contracts\LoopUntil && $this->shouldExit === false);

            $handler->onStreamEnded();
        };
    }
}
