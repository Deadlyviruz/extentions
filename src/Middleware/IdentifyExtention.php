<?php

namespace Deadlyviruz\Extentions\Middleware;

use Deadlyviruz\Extentions\Extentions;
use Closure;

class IdentifyModule
{
    /**
     * @var Extentions
     */
    protected $extention;

    /**
     * Create a new IdentifyModule instance.
     *
     * @param Extentions $extention
     */
    public function __construct(Extentions $extention)
    {
        $this->extention = $extention;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $slug = null)
    {
        $request->session()->flash('extention', $this->extention->where('slug', $slug));

        return $next($request);
    }
}
