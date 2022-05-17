<?php

namespace App\Http\Backoffice\Handlers\Dashboard;

use App\Http\Backoffice\Handlers\Handler;
use App\Http\Kernel;
use Digbang\Security\Contracts\SecurityApi;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Router;

class DashboardHandler extends Handler
{
    public function __invoke(SecurityApi $securityApi): View
    {
        return view('backoffice::empty', [
            'user' => $securityApi->getUser(),
        ]);
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->get("$backofficePrefix/", self::class)
            ->name(self::class)
            ->middleware([Kernel::BACKOFFICE]);
    }

    public static function route(): string
    {
        return route(self::class);
    }
}
