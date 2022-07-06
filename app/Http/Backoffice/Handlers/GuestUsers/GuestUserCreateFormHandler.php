<?php

namespace App\Http\Backoffice\Handlers\GuestUsers;

use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\GuestUsers\GuestUserCreateRequest;
use App\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use WorkshopBackoffice\Enumerables\Country;

class GuestUserCreateFormHandler extends Handler
{
    public function __invoke()
    {
        $title = 'create user';

        $form = $this->buildForm(
            security()->url()->to(GuestUserCreateHandler::route()),
            $title,
            Request::METHOD_POST,
            security()->url()->to(GuestUserListHandler::route()),
        );

        return view()->make('backoffice::create', [
           'title' => $title,
            'form' => $form,
        ]);
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router->get("$backofficePrefix/guests/users/create", [
            'uses' => self::class,
            'permission' => Permission::GUEST_USER_CREATE,
        ])
            ->name(self::class)
            ->middleware([Kernel::BACKOFFICE_LISTING]);
    }

    public static function route(): string
    {
        return route(self::class);
    }

    public function buildForm(string $target, string $label, string $method, string $cancelAction = '', array $options = [])
    {
        $form = backoffice()->form($target, $label, $method, $cancelAction, $options);

        $form->setSubmitLabel('add');
        $inputs = $form->inputs();

        $profileInputs = $inputs->collection();
        $profileInputs->text(GuestUserCreateRequest::FIRST_NAME, 'First Name')->setRequired();
        $profileInputs->text('lastName', 'Last Name')->setRequired();
        $profileInputs->dropdown('country', 'country', Country::getAllValuesTranslated());
        $profileInputs->textarea('description', 'description');
        $profileInputs->date('birthday', 'birthday');

        $inputs->composite('', $profileInputs)->changeView('backoffice::inputs.labeled-composite');

        return $form;
    }
}
