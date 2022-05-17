<?php

namespace App\Http\Backoffice\Handlers\Users;

use App\Http\Backoffice\Handlers\Auth\AuthActivateHandler;
use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Handlers\SendsEmails;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\Users\UserRequest;
use App\Http\Kernel;
use Digbang\Backoffice\Exceptions\ValidationException;
use Digbang\Security\Exceptions\SecurityException;
use Digbang\Security\Users\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Router;

class UserResendActivationHandler extends Handler
{
    use SendsEmails;

    public function __invoke(UserRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->findUser();

        try {
            $activation = security()->activations()->create($user);

            $this->sendActivation(
                $user,
                security()->url()->to(AuthActivateHandler::route($user->getUserId(), $activation->getCode()))
            );

            return redirect()->back()->withSuccess(trans('backoffice::auth.activation.email-sent', ['email' => $user->getEmail()]));
        } catch (ValidationException $e) {
            return redirect()->back()->withDanger(implode('<br/>', $e->getErrors()));
        } catch (SecurityException $e) {
            return redirect()->to(url()->to(DashboardHandler::route()))->withDanger(trans('backoffice::auth.permission_error'));
        }
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.users.url', 'operators');

        $router
            ->post("$backofficePrefix/$routePrefix/{" . UserRequest::ROUTE_PARAM_ID . '}/resend-activation', [
                'uses' => self::class,
                'permission' => Permission::OPERATOR_RESEND_ACTIVATION,
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
