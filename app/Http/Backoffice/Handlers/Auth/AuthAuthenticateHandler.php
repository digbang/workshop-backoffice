<?php

namespace App\Http\Backoffice\Handlers\Auth;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Requests\Auth\LoginRequest;
use App\Http\Kernel;
use Cake\Chronos\Chronos;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Digbang\Security\Contracts\SecurityApi;
use Illuminate\Routing\Router;
use Illuminate\Support\MessageBag;

class AuthAuthenticateHandler extends Handler
{
    /** @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse */
    public function __invoke(LoginRequest $request, SecurityApi $securityApi)
    {
        $request->validate();

        try {
            $credentials = $request->request()->all(['email', 'username', 'login', 'password']);

            $authenticated = $securityApi->authenticate(
                $credentials,
                $request->request()->input('remember') ?? false
            );

            if ($authenticated) {
                return redirect()->intended(
                    $securityApi->url()->to(DashboardHandler::route())
                );
            }

            $errors = new MessageBag();
            $errors->add('password', trans('backoffice::auth.validation.password.wrong'));

            return redirect()->to(AuthLoginHandler::route())->withInput()->withErrors($errors);
        } catch (ThrottlingException $e) {
            return view()->make('backoffice::auth.throttling', [
                'message' => trans('backoffice::auth.throttling.' . $e->getType(), ['remaining' => (new Chronos())->diffInSeconds(Chronos::createFromTimestamp($e->getFree()->timestamp))]),
            ]);
        } catch (NotActivatedException $e) {
            return view()->make('backoffice::auth.not-activated');
        }
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->post("$backofficePrefix/auth/login", self::class)
            ->name(self::class)
            ->middleware([Kernel::BACKOFFICE_PUBLIC]);
    }

    public static function route(): string
    {
        return route(self::class);
    }
}
