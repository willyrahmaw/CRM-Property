<?php

namespace App\Http\Middleware;

use App\Enums\IndonesianTimezone;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetUserTimezone
{
    /**
     * Handle an incoming request and apply active Indonesian timezone.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $timezone = null;

        // When PT (Company) timezone is configured, all users in the PT follow it
        if ($request->user() && $request->user()->company && $request->user()->company->timezone) {
            $timezone = $request->user()->company->timezone;
        } elseif ($request->user() && $request->user()->timezone) {
            $timezone = $request->user()->timezone;
        } elseif ($request->hasSession() && $request->session()->has('timezone')) {
            $timezone = $request->session()->get('timezone');
        }

        $validTimezones = IndonesianTimezone::values();

        if (! $timezone || ! in_array($timezone, $validTimezones, true)) {
            $timezone = config('app.timezone', 'Asia/Jakarta');
            if (! in_array($timezone, $validTimezones, true)) {
                $timezone = IndonesianTimezone::WIB->value;
            }
        }

        date_default_timezone_set($timezone);
        config(['app.timezone' => $timezone]);

        return $next($request);
    }
}
