<?php

namespace App\Http\Backoffice\Handlers\Users;

use App\Http\Backoffice\Handlers\Auth\AuthActivateHandler;
use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Handlers\SendsEmails;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\Users\UserCreateRequest;
use App\Http\Kernel;
use Digbang\Backoffice\Exceptions\ValidationException;
use Digbang\Security\Activations\Activation;
use Digbang\Security\Exceptions\SecurityException;
use Digbang\Security\Roles\Role;
use Digbang\Security\Roles\Roleable;
use Digbang\Security\Users\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Router;

class UserCreateHandler extends Handler
{
    use SendsEmails;

    public function __invoke(UserCreateRequest $request): RedirectResponse
    {
        $request->validate();

        try {
            /** @var User $user */
            $user = security()->users()->create($request->credentials(), function (User $user) use ($request): void {
                $this->addRoles($user, $request->roles());
            });

            if ($request->activated()) {
                security()->activate($user);
            } else {
                /** @var Activation $activation */
                $activation = security()->activations()->create($user);

                $this->sendActivation(
                    $user,
                    AuthActivateHandler::route($user->getUserId(), $activation->getCode())
                );
            }

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
            ->post("$backofficePrefix/$routePrefix/", [
                'uses' => self::class,
                'permission' => Permission::OPERATOR_CREATE,
            ])
            ->name(self::class)
            ->middleware([Kernel::BACKOFFICE]);
    }

    public static function route(): string
    {
        return route(self::class);
    }

    private function addRoles(User $user, array $roles): void
    {
        if ($user instanceof Roleable && count($roles) > 0) {
            /** @var string $slug */
            foreach ($roles as $slug) {
                /** @var Role|null $role */
                $role = security()->roles()->findBySlug($slug);

                if ($role) {
                    $user->addRole($role);
                }
            }
        }
    }
}
