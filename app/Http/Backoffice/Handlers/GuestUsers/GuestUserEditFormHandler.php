<?php

namespace App\Http\Backoffice\Handlers\GuestUsers;

use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Ramsey\Uuid\Uuid;
use WorkshopBackoffice\Enumerables\Country;
use WorkshopBackoffice\Services\GuestUserService;

class GuestUserEditFormHandler extends Handler
{
    public function __invoke(string $id, GuestUserService $service)
    {
        $user = $service->find(Uuid::fromString($id));

        $form = $this->buildForm(
            url()->to(GuestUserEditHandler::route($user->getId())),
            fa('plus') . ' edit user',
            Request::METHOD_PUT,
            url()->to(GuestUserListHandler::route()),
            [
            ]
        );

        $form->fill([
            'firstName' => $user->getName()->getFirstName(),
            'lastName' => $user->getName()->getLastName(),
            'country' => $user->getCountry()->getValue(),
            'description' => $user->getDescription(),
            'birthday' => $user->getBirthdate(),
        ]);

        return view()->make('backoffice::edit', [
            'title' => 'edit user',
            'form' => $form,
        ]);
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router->get("$backofficePrefix/guests/users/{id}", [
            'uses' => self::class,
            'permission' => Permission::GUEST_USER_EDIT,
        ])
        ->name(self::class)
        ->middleware([\App\Http\Kernel::BACKOFFICE_LISTING]);
    }

    public static function route(string $id)
    {
        return route(static::class, [
           'id' => $id,
        ]);
    }

    public function buildForm(string $target, string $label, string $method = 'POST', string $cancelAction = '', array $options = [])
    {
        $form = backoffice()->form($target, $label, $method, $cancelAction, $options);

        $form->setSubmitLabel('edit');
        $inputs = $form->inputs();

        $profileInputs = $inputs->collection();
        $profileInputs->text('firstName', 'First Name')->setRequired();
        $profileInputs->text('lastName', 'Last Name')->setRequired();
        $profileInputs->dropdown('country', 'country', Country::getAllValuesTranslated());
        $profileInputs->textarea('description', 'description');
        $profileInputs->date('birthday', 'birthday');

        $inputs->composite('', $profileInputs)->changeView('backoffice::inputs.labeled-composite');

        return $form;
    }
}
