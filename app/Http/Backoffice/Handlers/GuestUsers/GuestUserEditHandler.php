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
    public function __invoke(GuestUserEditRequest $request, GuestUserService $service)
    {
        $service->update($request->id(), $request);

        return redirect()->to(url()->to(GuestUserListHandler::route()))->with('success', 'user updated');
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router->put("$backofficePrefix/guests/users/{id}", [
            'uses' => self::class,
            'permission' => Permission::GUEST_USER_EDIT,
        ])
        ->name(self::class)
        ->middleware([Kernel::BACKOFFICE_LISTING]);
    }

    public static function route(\Ramsey\Uuid\UuidInterface $id): string
    {
        return route(static::class, [
            'id' => $id,
        ]);
    }
}
