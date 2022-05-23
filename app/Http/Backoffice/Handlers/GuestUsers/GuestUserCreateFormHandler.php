<?php

namespace App\Http\Backoffice\Handlers\GuestUsers;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\GuestUsers\GuestUserCreateRequest;
use App\Http\Kernel;
use Digbang\Backoffice\Forms\Form;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use WorkshopBackoffice\Entities\GuestCategory;
use WorkshopBackoffice\Enumerables\Country;
use WorkshopBackoffice\Repositories\GuestCategoryRepository;

class GuestUserCreateFormHandler extends Handler
{
    public function __invoke(GuestCategoryRepository $guestCategoryRepository): View
    {
        $label = trans('labels.backoffice.guestUser.add');

        $categories = $guestCategoryRepository->all();

        $form = $this->buildForm(
            security()->url()->to(GuestUserCreateHandler::route()),
            $label,
            Request::METHOD_POST,
            security()->url()->to(GuestUserListHandler::route()),
            [],
            $categories
        );

        $breadcrumb = backoffice()->breadcrumb([
            trans('backoffice::default.home') => DashboardHandler::class,
            trans('labels.backoffice.guestUser.plural') => GuestUserListHandler::class,
            $label,
        ]);

        return view()->make('backoffice::create', [
            'title' => trans('labels.backoffice.guestUser.new.user'),
            'form' => $form,
            'breadcrumb' => $breadcrumb,
        ]);
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->get("$backofficePrefix/guests/create", [
                'uses' => self::class,
                'permission' => Permission::GUEST_USER_CREATE,
            ])
            ->name(self::class)
            ->middleware([Kernel::BACKOFFICE_LISTING]);
    }

    public static function route(): string
    {
        return route(static::class);
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

        $inputs->text(GuestUserCreateRequest::FIST_NAME, trans('labels.backoffice.guestUser.fields.firstName'))->setRequired();
        $inputs->text(GuestUserCreateRequest::LAST_NAME, trans('labels.backoffice.guestUser.fields.lastName'))->setRequired();
        $inputs->dropdown(GuestUserCreateRequest::COUNTRY, trans('labels.backoffice.guestUser.fields.country'), Country::getAllValuesTranslated())->setRequired();
        $inputs->dropdown(GuestUserCreateRequest::CATEGORIES . '[]', trans('labels.backoffice.guestUser.fields.categories'), $categories, ['multiple' => 'multiple'])->setRequired();
        $otherInputs->textarea(GuestUserCreateRequest::DESCRIPTION, trans('labels.backoffice.guestUser.fields.description'))->setRequired();
        $otherInputs->date(GuestUserCreateRequest::BIRTHDAY, trans('labels.backoffice.guestUser.fields.birthday'))->setRequired();
        $otherInputs->string(GuestUserCreateRequest::ADDRESS, trans('labels.backoffice.guestUser.fields.address'))->setRequired();
        $otherInputs->literal('literal', trans('labels.backoffice.guestUser.fields.active'))->setRequired();
        $otherInputs->boolean(GuestUserCreateRequest::ACTIVE, trans('labels.backoffice.guestUser.fields.active'))->setRequired();
        $otherInputs->literal('literal', trans('labels.backoffice.guestUser.fields.admissionDate'))->setRequired();
        $otherInputs->datetime(GuestUserCreateRequest::ADMISSION_DATE, trans('labels.backoffice.guestUser.fields.admissionDate'))->setRequired();
        $inputs->checkbox(GuestUserCreateRequest::WISH_TO_BE_CONTACTED, trans('labels.backoffice.guestUser.fields.wishToBeContacted'))->setRequired();
        $inputs->checkbox(GuestUserCreateRequest::CAN_BE_EDITED, trans('labels.backoffice.guestUser.fields.canBeEdited'))->setRequired();
        $contactInfoInputs->integer(GuestUserCreateRequest::PHONE_NUMBER, trans('labels.backoffice.guestUser.fields.phoneNumber'))->setRequired();
        $recordInput->wysiwyg(GuestUserCreateRequest::RECORD, trans('labels.backoffice.guestUser.fields.record'))->setRequired();
        $photoInput->file(GuestUserCreateRequest::PHOTO, trans('labels.backoffice.guestUser.fields.photo'))->setRequired();

        $inputs->composite('others', $otherInputs, 'Others')->changeView('backoffice::inputs.labeled-composite');
        $inputs->composite('', $photoInput, 'Photo');
        $inputs->composite('', $recordInput, 'Record');
        $inputs->composite('', $contactInfoInputs, 'Contact information');

        return $form;
    }
}
