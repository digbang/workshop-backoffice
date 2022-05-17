<?php

namespace App\Http\Backoffice\Handlers\Roles;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\Roles\RoleCreateRequest;
use App\Http\Kernel;
use Digbang\Backoffice\Forms\Form;
use Digbang\Backoffice\Support\PermissionParser;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

class RoleCreateFormHandler extends Handler
{
    private PermissionParser $permissionParser;

    public function __construct(PermissionParser $permissionParser)
    {
        $this->permissionParser = $permissionParser;
    }

    public function __invoke(): View
    {
        $label = trans('backoffice::default.new', ['model' => trans('backoffice::auth.role')]);

        $form = $this->buildForm(
            security()->url()->to(RoleCreateHandler::route()),
            $label,
            Request::METHOD_POST,
            security()->url()->to(RoleListHandler::route())
        );

        $breadcrumb = backoffice()->breadcrumb([
            trans('backoffice::default.home') => DashboardHandler::class,
            trans('backoffice::auth.roles') => RoleListHandler::class,
            $label,
        ]);

        return view()->make('backoffice::create', [
            'title' => trans('backoffice::auth.roles'),
            'form' => $form,
            'breadcrumb' => $breadcrumb,
        ]);
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.roles.url', 'roles');

        $router
            ->get("$backofficePrefix/$routePrefix/create", [
                'uses' => self::class,
                'permission' => Permission::ROLE_CREATE,
            ])
            ->name(self::class)
            ->middleware([Kernel::BACKOFFICE]);
    }

    public static function route(): string
    {
        return route(self::class);
    }

    private function buildForm(string $target, string $label, string $method = Request::METHOD_POST, string $cancelAction = '', array $options = []): Form
    {
        $form = backoffice()->form($target, $label, $method, $cancelAction, $options);

        $inputs = $form->inputs();

        $inputs
            ->text(RoleCreateRequest::FIELD_NAME, trans('backoffice::auth.name'))
            ->setRequired();

        $inputs->dropdown(
            RoleCreateRequest::FIELD_PERMISSIONS,
            trans('backoffice::auth.permissions'),
            $this->permissionParser->toDropdownArray(security()->permissions()->all()),
            [
                'multiple' => 'multiple',
                'class' => 'multiselect',
            ]
        );

        return $form;
    }
}
