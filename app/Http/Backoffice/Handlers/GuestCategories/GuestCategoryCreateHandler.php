<?php

namespace App\Http\Backoffice\Handlers\GuestCategories;

use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\GuestCategories\GuestCategoryCreateRequest;
use App\Http\Kernel;
use Illuminate\Routing\Router;
use WorkshopBackoffice\Services\GuestCategoryService;

class GuestCategoryCreateHandler extends Handler
{
    public function __invoke(GuestCategoryCreateRequest $request, GuestCategoryService $service): \Illuminate\Http\RedirectResponse
    {
        $request->validate();

        $service->create($request);

        return redirect()->to(url()->to(GuestCategoryListHandler::route()))
            ->with('success', trans('messages.backoffice.guestCategory.create'));
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->post("$backofficePrefix/guests/categories/create", [
                'uses' => self::class,
                'permission' => Permission::GUEST_CATEGORY_CREATE,
            ])
            ->name(self::class)
            ->middleware([Kernel::BACKOFFICE]);
    }

    public static function route(): string
    {
        return route(self::class);
    }
}
