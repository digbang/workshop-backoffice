<?php

namespace App\Http\Backoffice\Handlers\GuestUsers;

use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\GuestUsers\GuestUserCreateRequest;
use App\Http\Kernel;
use Illuminate\Routing\Router;
use WorkshopBackoffice\Services\GuestUserService;

class GuestUserCreateHandler extends Handler
{
    public function __invoke(GuestUserCreateRequest $request, GuestUserService $service): \Illuminate\Http\RedirectResponse
    {
        $request->validate();

        $service->create($request);

        return redirect()->to(url()->to(GuestUserListHandler::route()))->with('success', trans('messages.backoffice.guestUser.create'));
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->post("$backofficePrefix/guests", [
                'uses' => self::class,
                'permission' => Permission::GUEST_USER_CREATE,
            ])
            ->name(self::class)
            ->middleware([Kernel::BACKOFFICE_LISTING]);
    }

    public static function route(): string
    {
        return route(static::class);
    }
}
