<?php

namespace App\Http\Backoffice\Handlers\GuestUsers;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\GuestUsers\GuestUserListRequest;
use App\Http\Kernel;
use Digbang\Backoffice\Listings\Listing;
use Digbang\Backoffice\Support\Breadcrumb;
use Digbang\Security\Exceptions\SecurityException;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use WorkshopBackoffice\Entities\GuestUser;
use WorkshopBackoffice\Repositories\Criteria\GuestUsers\GuestUserFilter;
use WorkshopBackoffice\Repositories\Criteria\GuestUsers\GuestUserSorting;
use WorkshopBackoffice\Repositories\GuestUserRepository;

class GuestUserListHandler extends Handler
{
    public function __invoke(GuestUserListRequest $request, GuestUserRepository $repository): View
    {
        $listing = $this->listing();

        /** @var GuestUserFilter $filter */
        $filter = $request->getFilter();

        /** @var GuestUserSorting $sorting */
        $sorting = $request->getSorting();

        $data = $repository->filter($filter, $sorting, $request->getPaginationData()->getLimit(), $request->getPaginationData()->getOffset());

        $listing->fill($data);
        $this->buildListActions($listing, $request);

        return view()->make('backoffice::index', [
            'title' => trans('backoffice::auth.users'),
            'list' => $listing,
            'breadcrumb' => $this->breadcrumb(),
        ]);
    }

    public static function defineRoute(\Illuminate\Routing\Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->get("$backofficePrefix/guests/users", [
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

    public function listing(): Listing
    {
        $listing = backoffice()->listing([
            'firstName' => 'firstName',
            'lastName' => 'lastName',
            'country' => 'country',
            'id',
        ]);

        $listing->columns()
            ->hide(['id'])
            ->sortable(['firstName', 'lastName']);

        $listing->addValueExtractor('firstName', function (GuestUser $user) {
            return $user->getName()->getFirstName();
        });

        $listing->addValueExtractor('lastName', fn (GuestUser $user) => $user->getName()->getLastName());

        $listing->addValueExtractor('country', fn (GuestUser $user) => $user->getCountry()->getValue());

        $listing->addValueExtractor('id', fn (GuestUser $user) => $user->getId());

        return $listing;
    }

    public function buildListActions(Listing $listing, GuestUserListRequest $request)
    {
        $actions = backoffice()->actions();

        try {
            $actions->link(
                url()->to(GuestUserCreateFormHandler::route()),
                fa('plus') . ' add user',
                [
                    'class' => 'btn btn-primary',
                ]
            );
        } catch (SecurityException $exception) {
        }

        $listing->setActions($actions);

        $rowActions = backoffice()->actions();

        $rowActions->link(function (Collection $collection) {
            try {
                return url()->to(GuestUserEditFormHandler::route($collection->get('id')));
            } catch (SecurityException $exception) {
            }
        }, fa('edit'), [
            'data-toggle' => 'tooltip',
            'data-placement' => 'top',
            'title' => 'edit',
        ]);

        $listing->setRowActions($rowActions);
    }

    private function breadcrumb(): Breadcrumb
    {
        return backoffice()->breadcrumb([
            trans('backoffice::default.home') => DashboardHandler::class,
            trans('backoffice::auth.users'),
        ]);
    }
}
