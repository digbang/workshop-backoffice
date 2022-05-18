<?php

namespace App\Http\Backoffice\Handlers\GuestUsers;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Kernel;
use Digbang\Backoffice\Support\Breadcrumb;
use Illuminate\Contracts\View\View;

class GuestUserListHandler extends Handler
{
    public function __invoke(): View
    {
        return view()->make('backoffice::index', [
            'title' => trans('backoffice::auth.users'),
            'list' => [],
            'breadcrumb' => $this->breadcrumb(),
        ]);
    }

    public static function defineRoute(\Illuminate\Routing\Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->get("$backofficePrefix/guests/", [
                'uses' => self::class,
                'permission' => Permission::GUEST_USER_LIST,
            ])
            ->name(self::class)
            ->middleware([Kernel::BACKOFFICE_LISTING]);
    }

    public static function route(): string
    {
        return route(self::class);
    }

    private function breadcrumb(): Breadcrumb
    {
        return backoffice()->breadcrumb([
            trans('backoffice::default.home') => DashboardHandler::class,
            trans('backoffice::auth.users'),
        ]);
    }
}
