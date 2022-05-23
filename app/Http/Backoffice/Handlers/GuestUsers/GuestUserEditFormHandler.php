<?php

namespace App\Http\Backoffice\Handlers\GuestUsers;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\GuestUsers\GuestUserCreateRequest;
use App\Http\Backoffice\Requests\GuestUsers\GuestUserEditRequest;
use App\Http\Kernel;
use Digbang\Backoffice\Forms\Form;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use WorkshopBackoffice\Entities\GuestCategory;
use WorkshopBackoffice\Enumerables\Country;
use WorkshopBackoffice\Repositories\GuestCategoryRepository;
use WorkshopBackoffice\Services\GuestUserService;

class GuestUserEditFormHandler extends Handler
{
    public function __invoke(GuestUserEditRequest $request, GuestUserService $service, GuestCategoryRepository $guestCategoryRepository): View
    {
        $user = $service->find($request->id());

        $form = $this->buildForm(
            url()->to(GuestUserEditHandler::route($user->getId())),
            trans('labels.backoffice.guestUser.edit') . ' ' . $user->getName(),
            Request::METHOD_PUT,
            url()->to(GuestUserListHandler::route()),
            [],
            $guestCategoryRepository->all()
        );

        $form->fill([
            GuestUserEditRequest::FIST_NAME => $user->getName()->getFirstName(),
            GuestUserEditRequest::LAST_NAME => $user->getName()->getLastName(),
            GuestUserEditRequest::COUNTRY => $user->getCountry()->getValue(),
            GuestUserEditRequest::DESCRIPTION => $user->getDescription(),
            GuestUserEditRequest::BIRTHDAY => $user->getBirthdate(),
            GuestUserEditRequest::ADDRESS => $user->getAddress(),
            GuestUserEditRequest::ACTIVE => $user->isActivated(),
            GuestUserEditRequest::ADMISSION_DATE => $user->getAdmissionDate(),
            GuestUserEditRequest::WISH_TO_BE_CONTACTED => $user->isWishToBeContacted(),
            GuestUserEditRequest::CAN_BE_EDITED => $user->canBeEdited(),
            GuestUserEditRequest::PHONE_NUMBER => $user->getPhoneNumber(),
            GuestUserEditRequest::RECORD => $user->getRecord(),
            GuestUserCreateRequest::CATEGORIES . '[]' => array_map(fn (GuestCategory $category) => $category->getId()->toString(), $user->getCategories()),
        ]);

        $breadcrumb = backoffice()->breadcrumb([
            trans('backoffice::default.home') => DashboardHandler::class,
            trans('labels.backoffice.guestUser.plural') => GuestUserListHandler::class,
            trans('labels.backoffice.guestUser.edit'),
        ]);

        return view()->make('backoffice::edit', [
            'title' => trans('labels.backoffice.guestUser.edit'),
            'form' => $form,
            'breadcrumb' => $breadcrumb,
        ]);
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->get("$backofficePrefix/guests/{" . GuestUserEditRequest::ROUTE_PARAM_ID . '}/edit', [
                'uses' => self::class,
                'permission' => Permission::GUEST_USER_EDIT,
            ])
            ->name(self::class)
            ->middleware([Kernel::BACKOFFICE_LISTING]);
    }

    public static function route(string $id): string
    {
        return route(static::class, [
            GuestUserEditRequest::ROUTE_PARAM_ID => $id,
        ]);
    }

    private function buildForm(string $target, string $label, string $method = Request::METHOD_POST, string $cancelAction = '', array $options = [], $guestCategories = []): Form
    {
        $form = backoffice()->form($target, $label, $method, $cancelAction, $options);

        $inputs = $form->inputs();
        $otherInputs = $form->inputs()->collection();
        $photoInput = $form->inputs()->collection();
        $recordInput = $form->inputs()->collection();
        $contactInfoInputs = $form->inputs()->collection();

        $categories = [];
        /** @var GuestCategory $category */
        foreach ($guestCategories as $category) {
            $categories[$category->getId()->toString()] = $category->getName();
        }

        $inputs->text(GuestUserEditRequest::FIST_NAME, trans('labels.backoffice.guestUser.fields.firstName'))->setRequired();
        $inputs->text(GuestUserEditRequest::LAST_NAME, trans('labels.backoffice.guestUser.fields.lastName'))->setRequired();
        $inputs->dropdown(GuestUserCreateRequest::CATEGORIES . '[]', trans('labels.backoffice.guestUser.fields.categories'), $categories, ['multiple'])->setRequired();
        $inputs->dropdown(GuestUserEditRequest::COUNTRY, trans('labels.backoffice.guestUser.fields.country'), Country::getAllValuesTranslated())->setRequired();
        $otherInputs->textarea(GuestUserEditRequest::DESCRIPTION, trans('labels.backoffice.guestUser.fields.description'))->setRequired();
        $otherInputs->date(GuestUserEditRequest::BIRTHDAY, trans('labels.backoffice.guestUser.fields.birthday'))->setRequired();
        $otherInputs->string(GuestUserEditRequest::ADDRESS, trans('labels.backoffice.guestUser.fields.address'))->setRequired();
        $otherInputs->literal('literal', trans('labels.backoffice.guestUser.fields.active'))->setRequired();
        $otherInputs->boolean(GuestUserEditRequest::ACTIVE, trans('labels.backoffice.guestUser.fields.active'))->setRequired();
        $otherInputs->literal('literal', trans('labels.backoffice.guestUser.fields.admissionDate'))->setRequired();
        $otherInputs->datetime(GuestUserEditRequest::ADMISSION_DATE, trans('labels.backoffice.guestUser.fields.admissionDate'))->setRequired();
        $inputs->checkbox(GuestUserEditRequest::WISH_TO_BE_CONTACTED, trans('labels.backoffice.guestUser.fields.wishToBeContacted'))->setRequired();
        $inputs->checkbox(GuestUserEditRequest::CAN_BE_EDITED, trans('labels.backoffice.guestUser.fields.canBeEdited'))->setRequired();
        $contactInfoInputs->integer(GuestUserEditRequest::PHONE_NUMBER, trans('labels.backoffice.guestUser.fields.phoneNumber'))->setRequired();
        $recordInput->wysiwyg(GuestUserEditRequest::RECORD, trans('labels.backoffice.guestUser.fields.record'))->setRequired();
        $photoInput->file(GuestUserEditRequest::PHOTO, trans('labels.backoffice.guestUser.fields.photo'))->setRequired();

        $inputs->composite('others', $otherInputs, 'Others');
        $inputs->composite('', $photoInput, 'Photo');
        $inputs->composite('', $recordInput, 'Record');
        $inputs->composite('', $contactInfoInputs, 'Contact information');

        return $form;
    }
}
