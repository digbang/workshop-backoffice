<?php

namespace App\Http\Backoffice\Handlers\GuestCategories;

use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\GuestCategories\GuestCategoryBulkDeleteRequest;
use App\Http\Kernel;
use Illuminate\Routing\Router;
use WorkshopBackoffice\Services\GuestCategoryService;

class GuestCategoryBulkDeleteHandler extends Handler
{
    public function __invoke(GuestCategoryBulkDeleteRequest $request, GuestCategoryService $service): \Illuminate\Http\RedirectResponse
    {
        try {
            $service->bulkDelete($request->categoryIds());
        } catch (\Exception $exception) {
            return redirect()
                ->to(url()->to(GuestCategoryListHandler::route()))->with('danger', $exception->getMessage());
        }

        return redirect()
            ->to(url()->to(GuestCategoryListHandler::route()))->with('success', trans('messages.backoffice.guestCategory.bulkDelete'));
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->delete("$backofficePrefix/guests/categories", [
                'uses' => self::class,
                'permission' => Permission::GUEST_CATEGORY_DELETE,
            ])
            ->name(self::class)
            ->middleware([Kernel::BACKOFFICE]);
    }

    public static function route(): string
    {
        return route(self::class);
    }
}
