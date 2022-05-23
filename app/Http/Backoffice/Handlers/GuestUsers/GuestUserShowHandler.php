<?php

namespace App\Http\Backoffice\Handlers\GuestUsers;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\GuestUsers\GuestUserRequest;
use App\Http\Kernel;
use Digbang\Security\Exceptions\SecurityException;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Router;
use WorkshopBackoffice\Services\GuestUserService;

class GuestUserShowHandler extends Handler
{
    public function __invoke(GuestUserRequest $request, GuestUserService $service): View
    {
        $user = $service->find($request->id());

        $breadcrumb = backoffice()->breadcrumb([
            trans('backoffice::default.home') => DashboardHandler::class,
            trans('labels.backoffice.guestUser.list') => GuestUserListHandler::class,
            $user->getName(),
        ]);

        $data = [
            trans('labels.backoffice.guestUser.fields.firstName') => $user->getName()->getFirstName(),
            trans('labels.backoffice.guestUser.fields.lastName') => $user->getName()->getLastName(),
            trans('labels.backoffice.guestUser.fields.country') => $user->getCountry()->getValue(),
        ];

        $actions = backoffice()->actions();

        try {
            $actions->link(
                security()->url()->to(GuestUserEditFormHandler::route($user->getId())),
                fa('edit') . ' ' . trans('backoffice::default.edit'),
                ['class' => 'btn btn-success']
            );
        } catch (SecurityException $e) {
        }

        try {
            $actions->link(
                security()->url()->to(GuestUserListHandler::route()),
                trans('backoffice::default.back'),
                ['class' => 'btn btn-default']
            );
        } catch (SecurityException $e) {
        }

        $topActions = backoffice()->actions();

        try {
            $topActions->link(
                security()->url()->to(GuestUserListHandler::route()),
                fa('arrow-left') . ' ' . trans('backoffice::default.back')
            );
        } catch (SecurityException $e) {
        }

        return view()->make('backoffice::show', [
            'title' => trans('labels.backoffice.tenant.tenant'),
            'breadcrumb' => $breadcrumb,
            'label' => $user->getName(),
            'data' => $data,
            'actions' => $actions,
            'topActions' => $topActions,
        ]);
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->get("$backofficePrefix/guests/{" . GuestUserRequest::ROUTE_PARAM_ID . '}/show', [
                'uses' => self::class,
                'permission' => Permission::GUEST_USER_SHOW,
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
