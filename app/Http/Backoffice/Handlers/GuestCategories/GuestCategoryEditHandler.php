<?php

namespace App\Http\Backoffice\Handlers\GuestCategories;

use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\GuestCategories\GuestCategoryEditRequest;
use App\Http\Kernel;
use Illuminate\Routing\Router;
use WorkshopBackoffice\Services\GuestCategoryService;

class GuestCategoryEditHandler extends Handler
{
    public function __invoke(GuestCategoryEditRequest $request, GuestCategoryService $service): \Illuminate\Http\RedirectResponse
    {
        $request->validate();

        $service->update($request->id(), $request);

        return redirect()->to(url()->to(GuestCategoryListHandler::route()))->with('success', trans('messages.backoffice.guestCategory.edit'));
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->put("$backofficePrefix/guests/categories/{" . GuestCategoryEditRequest::ROUTE_PARAM_ID . '}/edit', [
                'uses' => self::class,
                'permission' => Permission::GUEST_CATEGORY_EDIT,
            ])
            ->name(self::class)
            ->middleware([Kernel::BACKOFFICE]);
    }

    public static function route(string $id): string
    {
        return route(static::class, [
            GuestCategoryEditRequest::ROUTE_PARAM_ID => $id,
        ]);
    }
}
