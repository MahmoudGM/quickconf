<?php

namespace App\Http\Middleware;

use Closure;
use App\Conference;

class CheckIfAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $acronym = $request->route('acronym');
        $edition = $request->route('edition');
        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);
        
        if ( count($conference) == 0 )
            return redirect()->route('notfound',['404']);

        return $next($request);
    }
}
