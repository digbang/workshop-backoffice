<?php

namespace App\Http\Backoffice\Handlers\Users;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Kernel;
use Digbang\Backoffice\Forms\Form;
use Digbang\Backoffice\Support\PermissionParser;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

class UserCreateFormHandler extends Handler
{
    private PermissionParser $permissionParser;

    public function __construct(PermissionParser $permissionParser)
    {
        $this->permissionParser = $permissionParser;
    }

    public function __invoke(): View
    {
        $label = trans('backoffice::default.new', ['model' => trans('backoffice::auth.user')]);

        $form = $this->buildForm(
            security()->url()->to(UserCreateHandler::route()),
            $label,
            Request::METHOD_POST,
            security()->url()->to(UserListHandler::route())
        );

        $breadcrumb = backoffice()->breadcrumb([
            trans('backoffice::default.home') => DashboardHandler::class,
            trans('backoffice::auth.users') => UserListHandler::class,
            $label,
        ]);

        return view()->make('backoffice::create', [
            'title' => trans('backoffice::auth.users'),
            'form' => $form,
            'breadcrumb' => $breadcrumb,
        ]);
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.users.url', 'operators');

        $router
            ->get("$backofficePrefix/$routePrefix/create", [
                'uses' => self::class,
                'permission' => Permission::OPERATOR_CREATE,
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

        $inputs->text('firstName', trans('backoffice::auth.first_name'));
        $inputs->text('lastName', trans('backoffice::auth.last_name'));

        $inputs
            ->text('email', trans('backoffice::auth.email'))
            ->setRequired();

        $inputs
            ->text('username', trans('backoffice::auth.username'))
            ->setRequired();

        $inputs
            ->password('password', trans('backoffice::auth.password'))
            ->setRequired();

        $inputs
            ->password('password_confirmation', trans('backoffice::auth.confirm_password'))
            ->setRequired();

        $inputs->checkbox('activated', trans('backoffice::auth.activated'));

        $roles = security()->roles()->findAll();

        $options = [];
        $rolePermissions = [];

        /** @var \Digbang\Security\Roles\Role $role */
        foreach ($roles as $role) {
            $options[$role->getRoleSlug()] = $role->getName();

            $rolePermissions[$role->getRoleSlug()] = $role->getPermissions()->map(function (\Digbang\Security\Permissions\Permission $permission): string {
                return $permission->getName();
            })->toArray();
        }

        $inputs->dropdown(
            'roles',
            trans('backoffice::auth.roles'),
            $options,
            [
                'multiple' => 'multiple',
                'class' => 'user-groups form-control',
                'data-permissions' => json_encode($rolePermissions),
            ]
        );

        $permissions = security()->permissions()->all();

        $inputs->dropdown(
            'permissions',
            trans('backoffice::auth.permissions'),
            $this->permissionParser->toDropdownArray($permissions),
            [
                'multiple' => 'multiple',
                'class' => 'multiselect',
            ]
        );

        return $form;
    }
}
