<?php

namespace App\Http\Backoffice\Handlers\Roles;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\Roles\RoleEditRequest;
use App\Http\Backoffice\Requests\Roles\RoleRequest;
use App\Http\Kernel;
use Digbang\Backoffice\Exceptions\ValidationException;
use Digbang\Security\Exceptions\SecurityException;
use Digbang\Security\Permissions\Permissible;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Router;

class RoleEditHandler extends Handler
{
    public function __invoke(RoleEditRequest $request): RedirectResponse
    {
        $role = $request->getRole();

        $request->validate();

        try {
            $role->setName($request->name());

            if ($role instanceof Permissible) {
                $role->syncPermissions($request->permissions());
            }

            security()->roles()->save($role);

            return redirect()->to(
                security()->url()->to(RoleListHandler::route())
            );
        } catch (ValidationException $e) {
            return redirect()->back()->withInput()->withErrors($e->getErrors());
        } catch (SecurityException $e) {
            return redirect()->to(url()->to(DashboardHandler::route()))->withDanger(trans('backoffice::auth.permission_error'));
        }
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.roles.url', 'roles');

        $router
            ->put("$backofficePrefix/$routePrefix/{" . RoleRequest::ROUTE_PARAM_ID . '}', [
                'uses' => self::class,
                'permission' => Permission::ROLE_UPDATE,
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
