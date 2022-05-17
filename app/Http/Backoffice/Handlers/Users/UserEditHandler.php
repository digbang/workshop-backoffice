<?php

namespace App\Http\Backoffice\Handlers\Users;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\Users\UserEditRequest;
use App\Http\Backoffice\Requests\Users\UserRequest;
use App\Http\Kernel;
use Digbang\Backoffice\Exceptions\ValidationException;
use Digbang\Security\Exceptions\SecurityException;
use Digbang\Security\Users\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Router;

class UserEditHandler extends Handler
{
    public function __invoke(UserEditRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->findUser();

        $request->validate();

        try {
            security()->users()->update($user, $request->credentials());

            return redirect()->to(url()->to(UserListHandler::route()));
        } catch (ValidationException $e) {
            return redirect()->back()->withInput()->withErrors($e->getErrors());
        } catch (SecurityException $e) {
            return redirect()->to(url()->to(DashboardHandler::route()))->withDanger(trans('backoffice::auth.permission_error'));
        }
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.users.url', 'operators');

        $router
            ->put("$backofficePrefix/$routePrefix/{" . UserRequest::ROUTE_PARAM_ID . '}', [
                'uses' => self::class,
                'permission' => Permission::OPERATOR_UPDATE,
            ])
            ->where(UserRequest::ROUTE_PARAM_ID, '[0-9]+')
            ->name(self::class)
            ->middleware([Kernel::BACKOFFICE]);
    }

    public static function route(int $userId): string
    {
        return route(self::class, [
            UserRequest::ROUTE_PARAM_ID => $userId,
        ]);
    }
}
