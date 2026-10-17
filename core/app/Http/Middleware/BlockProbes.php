<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BlockProbes
{
    public function handle(Request $request, Closure $next)
    {
        $path = strtolower(str_replace("\0", '', urldecode($request->path())));

        if (str_contains($path, '..') || preg_match('#(^|/)(\.env|\.git|composer\.(json|lock)|vendor|storage|phpunit|wp-admin|wp-login|xmlrpc|phpinfo|cgi-bin|_ignition|telescope|horizon|debugbar)(/|$)#', $path)) {
            abort(404);
        }

        $agent = strtolower((string) $request->userAgent());
        if ($agent !== '' && preg_match('/sqlmap|nikto|nmap|masscan|zgrab|nuclei|acunetix|nessus|dirbuster|gobuster|hydra|wpscan|havij|libwww-perl/', $agent)) {
            abort(403);
        }

        if ($request->isMethod('post') && $agent === '' && ($request->is('user/login') || $request->is('user/register') || $request->is('admin') || $request->is('admin/*'))) {
            abort(403);
        }

        return $next($request);
    }
}