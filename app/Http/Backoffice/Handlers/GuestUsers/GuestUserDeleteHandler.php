<?php

namespace App\Http\Backoffice\Handlers\GuestUsers;

use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\GuestUsers\GuestUserRequest;
use App\Http\Kernel;
use Illuminate\Routing\Router;
use WorkshopBackoffice\Services\GuestUserService;

class GuestUserDeleteHandler extends Handler
{
    public function __invoke(GuestUserRequest $request, GuestUserService $service): \Illuminate\Http\RedirectResponse
    {
        $service->delete($request->id());

        return redirect()->to(url()->to(GuestUserListHandler::route()))->with('success', trans('messages.backoffice.guestUser.delete'));
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->delete("$backofficePrefix/guests/{" . GuestUserRequest::ROUTE_PARAM_ID . '}', [
                'uses' => self::class,
                'permission' => Permission::GUEST_USER_DELETE,
            ])
            ->name(self::class)
            ->middleware([Kernel::BACKOFFICE_LISTING]);
    }

    public static function route(string $userId): string
    {
        return route(static::class, [
            GuestUserRequest::ROUTE_PARAM_ID => $userId,
        ]);
    }
}
