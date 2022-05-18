<?php

namespace App\Http\Backoffice\Handlers\GuestCategories;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\GuestCategories\GuestCategoryRequest;
use App\Http\Kernel;
use Digbang\Security\Exceptions\SecurityException;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Router;
use WorkshopBackoffice\Services\GuestCategoryService;

class GuestCategoryShowHandler extends Handler
{
    public function __invoke(GuestCategoryRequest $request, GuestCategoryService $service): View
    {
        $category = $service->find($request->id());

        $breadcrumb = backoffice()->breadcrumb([
            trans('backoffice::default.home') => DashboardHandler::class,
            trans('labels.backoffice.guestCategory.list') => GuestCategoryListHandler::class,
            $category->getName(),
        ]);

        $data = [
            trans('labels.backoffice.guestCategory.fields.name') => $category->getName(),
            trans('labels.backoffice.guestCategory.fields.createdAt') => $category->getCreatedAt()->toAtomString(),
            trans('labels.backoffice.guestCategory.fields.updatedAt') => $category->getUpdatedAt()->toAtomString(),
        ];

        $actions = backoffice()->actions();

        try {
            $actions->link(
                security()->url()->to(GuestCategoryEditFormHandler::route($category->getId())),
                fa('edit') . ' ' . trans('backoffice::default.edit'),
                ['class' => 'btn btn-success']
            );
        } catch (SecurityException $e) {
        }

        try {
            $actions->link(
                security()->url()->to(GuestCategoryListHandler::route()),
                trans('backoffice::default.back'),
                ['class' => 'btn btn-default']
            );
        } catch (SecurityException $e) {
        }

        $topActions = backoffice()->actions();

        try {
            $topActions->link(
                security()->url()->to(GuestCategoryListHandler::route()),
                fa('arrow-left') . ' ' . trans('backoffice::default.back')
            );
        } catch (SecurityException $e) {
        }

        return view()->make('backoffice::show', [
            'title' => trans('labels.backoffice.guestCategory.title'),
            'breadcrumb' => $breadcrumb,
            'label' => $category->getName(),
            'data' => $data,
            'actions' => $actions,
            'topActions' => $topActions,
        ]);
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->get("$backofficePrefix/guests/categories/{" . GuestCategoryRequest::ROUTE_PARAM_ID . '}/show', [
                'uses' => self::class,
                'permission' => Permission::GUEST_CATEGORY_SHOW,
            ])
            ->name(self::class)
            ->middleware([Kernel::BACKOFFICE]);
    }

    public static function route(string $guestCategoryId): string
    {
        return route(static::class, [
            GuestCategoryRequest::ROUTE_PARAM_ID => $guestCategoryId,
        ]);
    }
}
