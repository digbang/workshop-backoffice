<?php

namespace App\Http\Backoffice\Handlers\GuestCategories;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\GuestCategories\GuestCategoryCreateRequest;
use App\Http\Kernel;
use Digbang\Backoffice\Forms\Form;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

class GuestCategoryCreateFormHandler extends Handler
{
    public function __invoke(): View
    {
        $label = trans('labels.backoffice.guestCategory.add');

        $form = $this->buildForm(
            security()->url()->to(GuestCategoryCreateHandler::route()),
            $label,
            Request::METHOD_POST,
            security()->url()->to(GuestCategoryListHandler::route())
        );

        $breadcrumb = backoffice()->breadcrumb([
            trans('backoffice::default.home') => DashboardHandler::class,
            trans('labels.backoffice.guestCategory.plural') => GuestCategoryListHandler::class,
            $label,
        ]);

        return view()->make('backoffice::create', [
            'title' => trans('labels.backoffice.guestCategory.plural'),
            'form' => $form,
            'breadcrumb' => $breadcrumb,
        ]);
    }

    public static function route(): string
    {
        return route(self::class);
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->get("$backofficePrefix/guests/categories/create", [
                'uses' => self::class,
                'permission' => Permission::GUEST_CATEGORY_CREATE,
            ])
            ->name(self::class)
            ->middleware([Kernel::BACKOFFICE]);
    }

    private function buildForm(string $target, string $label, string $method = Request::METHOD_POST, string $cancelAction = '', array $options = []): Form
    {
        $form = backoffice()->form($target, $label, $method, $cancelAction, $options);

        $inputs = $form->inputs();

        $inputs->text(GuestCategoryCreateRequest::NAME, trans('labels.backoffice.guestCategory.fields.name'))->setRequired();

        return $form;
    }
}
