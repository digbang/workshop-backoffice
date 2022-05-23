<?php

namespace App\Http\Backoffice\Handlers\GuestUsers;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\GuestUsers\GuestUserCriteriaRequest;
use App\Http\Kernel;
use Digbang\Backoffice\Listings\Listing;
use Digbang\Backoffice\Support\Breadcrumb;
use Digbang\Security\Exceptions\SecurityException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use WorkshopBackoffice\Entities\GuestUser;
use WorkshopBackoffice\Repositories\Criteria\GuestUsers\GuestUserFilter;
use WorkshopBackoffice\Repositories\Criteria\GuestUsers\GuestUserSorting;
use WorkshopBackoffice\Repositories\GuestUserRepository;

class GuestUserListHandler extends Handler
{
    public function __invoke(GuestUserCriteriaRequest $request, GuestUserRepository $repository): View
    {
        $list = $this->listing();

        /** @var GuestUserFilter $filter */
        $filter = $request->getFilter();

        /** @var GuestUserSorting $sorting */
        $sorting = $request->getSorting();

        $data = $repository->filter($filter, $sorting, $request->getPaginationData()->getLimit(), $request->getPaginationData()->getOffset());

        $this->buildFilters($list);
        $this->buildListActions($list, $request);

        $list->fill($data);

        return view()->make('backoffice::index', [
            'title' => trans('labels.backoffice.guestUser.plural'),
            'list' => $list,
            'breadcrumb' => $this->breadcrumb(),
        ]);
    }

    public static function defineRoute(\Illuminate\Routing\Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->get("$backofficePrefix/guests/", [
                'uses' => self::class,
                'permission' => Permission::GUEST_USER_LIST,
            ])
            ->name(self::class)
            ->middleware([Kernel::BACKOFFICE_LISTING]);
    }

    public static function route(): string
    {
        return route(self::class);
    }

    private function buildListActions(Listing $list, GuestUserCriteriaRequest $request): void
    {
        $actions = backoffice()->actions();

        try {
            $actions->link(
                url()->to(GuestUserCreateFormHandler::route()),
                fa('plus') . ' ' . trans('labels.backoffice.guestUser.new.user'),
                [
                    'class' => 'btn btn-primary',
                ]
            );
        } catch (SecurityException $e) { /* Do nothing */
        }

        $list->setActions($actions);

        $rowActions = backoffice()->actions();

        $rowActions->link(function (Collection $row) {
            try {
                if ($row->get('canBeEdited')) {
                    return url()->to(GuestUserEditFormHandler::route($row->get('id')));
                }

                return false;
            } catch (SecurityException $e) {
            }
        }, fa('edit'), [
            'data-toggle' => 'tooltip',
            'data-placement' => 'top',
            'title' => trans('backoffice::default.edit'),
        ]);

        $rowActions->link(function (Collection $row) {
            try {
                return url()->to(GuestUserShowHandler::route($row->get('id')));
            } catch (SecurityException $e) {
            }
        }, fa('eye'), [
            'data-toggle' => 'tooltip',
            'data-placement' => 'top',
            'title' => trans('backoffice::default.show'),
        ]);

        $rowActions->form(
            function (Collection $row) {
                try {
                    return url()->to(GuestUserDeleteHandler::route($row->get('id')));
                } catch (SecurityException $e) {
                    return false;
                }
            },
            fa('times'),
            Request::METHOD_DELETE,
            [
                'class' => 'text-danger',
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'data-confirm' => trans('backoffice::default.delete-confirm'),
                'title' => trans('backoffice::default.delete'),
            ]
        );

        $list->setRowActions($rowActions);
    }

    private function buildFilters(Listing $list): void
    {
        $filters = $list->filters();

        $filters->text(GuestUserFilter::FIRST_NAME, trans('labels.backoffice.guestUser.fields.firstName'), ['class' => 'form-control']);
        $filters->text(GuestUserFilter::LAST_NAME, trans('labels.backoffice.guestUser.fields.lastName'), ['class' => 'form-control']);
        $filters->text(GuestUserFilter::COUNTRY, trans('labels.backoffice.guestUser.fields.country'), ['class' => 'form-control']);
    }

    private function breadcrumb(): Breadcrumb
    {
        return backoffice()->breadcrumb([
            trans('backoffice::default.home') => DashboardHandler::class,
            trans('labels.backoffice.guestUser.plural'),
        ]);
    }

    private function listing(): Listing
    {
        $listing = backoffice()->listing([
            'firstName' => trans('labels.backoffice.guestUser.fields.firstName'),
            'lastName' => trans('labels.backoffice.guestUser.fields.lastName'),
            'country' => trans('labels.backoffice.guestUser.fields.country'),
            'canBeEdited' => trans('labels.backoffice.guestUser.fields.country'),
            'id',
        ]);

        $listing->columns()
            ->hide(['id', 'canBeEdited'])
            ->sortable(['firstName', 'lastName']);

        $listing->addValueExtractor('firstName', fn (GuestUser $guestUser) => $guestUser->getName()->getFirstName());

        $listing->addValueExtractor('lastName', fn (GuestUser $guestUser) => $guestUser->getName()->getLastName());

        $listing->addValueExtractor('country', fn (GuestUser $guestUser) => $guestUser->getCountry()->getValue());

        $listing->addValueExtractor('canBeEdited', fn (GuestUser $guestUser) => (string) $guestUser->canBeEdited());

        $listing->addValueExtractor('id', fn (GuestUser $guestUser) => $guestUser->getId());

        return $listing;
    }
}
