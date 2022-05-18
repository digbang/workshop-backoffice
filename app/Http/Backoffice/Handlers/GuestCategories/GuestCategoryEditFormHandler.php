<?php

namespace App\Http\Backoffice\Handlers\GuestCategories;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\GuestCategories\GuestCategoryEditRequest;
use App\Http\Kernel;
use Digbang\Backoffice\Forms\Form;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use WorkshopBackoffice\Services\GuestCategoryService;

class GuestCategoryEditFormHandler extends Handler
{
    public function __invoke(GuestCategoryEditRequest $request, GuestCategoryService $service): \Illuminate\Contracts\View\View
    {
        $guestCategory = $service->find($request->id());

        $form = $this->buildForm(
            url()->to(GuestCategoryEditHandler::route($guestCategory->getId())),
            trans('backoffice::default.edit') . ' ' . $guestCategory->getName(),
            Request::METHOD_PUT,
            url()->to(GuestCategoryListHandler::route())
        );

        $form->fill([
            GuestCategoryEditRequest::NAME => $guestCategory->getName(),
        ]);

        $breadcrumb = backoffice()->breadcrumb([
            trans('backoffice::default.home') => DashboardHandler::class,
            trans('labels.backoffice.guestCategory.plural') => GuestCategoryListHandler::class,
            trans('labels.backoffice.guestCategory.edit'),
        ]);

        return view()->make('backoffice::edit', [
            'title' => trans('labels.backoffice.guestCategory.edit'),
            'form' => $form,
            'breadcrumb' => $breadcrumb,
        ]);
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->get("$backofficePrefix/guests/categories/{" . GuestCategoryEditRequest::ROUTE_PARAM_ID . '}/edit', [
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

    private function buildForm(string $target, string $label, string $method = Request::METHOD_POST, string $cancelAction = '', array $options = []): Form
    {
        $form = backoffice()->form($target, $label, $method, $cancelAction, $options);

        $inputs = $form->inputs();
        $inputs->text(GuestCategoryEditRequest::NAME, trans('labels.backoffice.guestCategory.fields.name'))->setRequired();

        return $form;
    }
}
