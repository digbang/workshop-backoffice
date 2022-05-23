<?php

namespace App\Http\Backoffice\Handlers\GuestUsers;

use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\GuestUsers\GuestUserEditRequest;
use App\Http\Kernel;
use Illuminate\Routing\Router;
use WorkshopBackoffice\Services\GuestUserService;

class GuestUserEditHandler extends Handler
{
    public function __invoke(GuestUserEditRequest $request, GuestUserService $service): \Illuminate\Http\RedirectResponse
    {
        $request->validate();

        $service->update($request->id(), $request);

        return redirect()->to(url()->to(GuestUserListHandler::route()))->with('success', trans('messages.backoffice.guestUser.edit'));
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->put("$backofficePrefix/guests/{" . GuestUserEditRequest::ROUTE_PARAM_ID . '}', [
                'uses' => self::class,
                'permission' => Permission::GUEST_USER_EDIT,
            ])
            ->name(self::class)
            ->middleware([Kernel::BACKOFFICE_LISTING]);
    }

    public static function route(string $id): string
    {
        return route(static::class, [
            GuestUserEditRequest::ROUTE_PARAM_ID => $id,
        ]);
    }
}
