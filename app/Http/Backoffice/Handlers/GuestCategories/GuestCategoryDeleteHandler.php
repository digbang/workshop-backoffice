<?php

namespace App\Http\Backoffice\Handlers\GuestCategories;

use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\GuestCategories\GuestCategoryRequest;
use App\Http\Kernel;
use Illuminate\Routing\Router;
use WorkshopBackoffice\Services\GuestCategoryService;

class GuestCategoryDeleteHandler extends Handler
{
    public function __invoke(GuestCategoryRequest $request, GuestCategoryService $service): \Illuminate\Http\RedirectResponse
    {
        try {
            $service->delete($request->id());
        } catch (\Exception $exception) {
            return redirect()
                ->to(url()->to(GuestCategoryListHandler::route()))->with('danger', $exception->getMessage());
        }

        return redirect()
            ->to(url()->to(GuestCategoryListHandler::route()))->with('success', trans('messages.backoffice.guestCategory.delete'));
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->delete("$backofficePrefix/guests/categories/{" . GuestCategoryRequest::ROUTE_PARAM_ID . '}', [
                'uses' => self::class,
                'permission' => Permission::GUEST_CATEGORY_DELETE,
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
