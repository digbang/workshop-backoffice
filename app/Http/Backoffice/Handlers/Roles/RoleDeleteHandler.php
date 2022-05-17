<?php

namespace App\Http\Backoffice\Handlers\Roles;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\Roles\RoleRequest;
use App\Http\Kernel;
use Digbang\Backoffice\Exceptions\ValidationException;
use Digbang\Security\Exceptions\SecurityException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Router;

class RoleDeleteHandler extends Handler
{
    public function __invoke(RoleRequest $request): RedirectResponse
    {
        $role = $request->getRole();

        if ($role->getRoleId() === Permission::PROTECTED_ROLE) {
            return redirect()->back()->withDanger(trans('validation.backoffice.roles.protected_deletion'));
        }

        try {
            security()->roles()->delete($role);

            return redirect()
                ->to(security()->url()->to(RoleListHandler::route()))
                ->withSuccess(trans('backoffice::default.delete_msg', [
                    'model' => trans('backoffice::auth.role'),
                    'id' => $role->getName(),
                ]));
        } catch (ValidationException $e) {
            return redirect()->back()->withDanger(implode('<br/>', $e->getErrors()));
        } catch (SecurityException $e) {
            return redirect()->to(url()->to(DashboardHandler::route()))->withDanger(trans('backoffice::auth.permission_error'));
        }
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.roles.url', 'roles');

        $router
            ->delete("$backofficePrefix/$routePrefix/{" . RoleRequest::ROUTE_PARAM_ID . '}', [
                'uses' => self::class,
                'permission' => Permission::ROLE_DELETE,
            ])
            ->where(RoleRequest::ROUTE_PARAM_ID, '[0-9]+')
            ->name(self::class)
            ->middleware([Kernel::BACKOFFICE]);
    }

    public static function route(int $roleId): string
    {
        return route(self::class, [
            RoleRequest::ROUTE_PARAM_ID => $roleId,
        ]);
    }
}
