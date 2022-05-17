<?php

namespace App\Http\Backoffice\Handlers\Users;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\Users\UserCriteriaRequest;
use App\Http\Kernel;
use Digbang\Backoffice\Listings\Listing;
use Digbang\Backoffice\Repositories\DoctrineUserRepository;
use Digbang\Security\Exceptions\SecurityException;
use Digbang\Security\Users\User;
use Digbang\Utils\CriteriaRequest;
use Digbang\Utils\Sorting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use ProjectName\Repositories\Criteria\Users\UserFilter;
use ProjectName\Repositories\Criteria\Users\UserSorting;

class UserListHandler extends Handler
{
    public function __invoke(UserCriteriaRequest $request): View
    {
        $list = $this->getListing();

        $this->buildFilters($list);
        $this->buildListActions($list, $request);

        $list->fill($this->getData($request));

        $breadcrumb = backoffice()->breadcrumb([
            trans('backoffice::default.home') => DashboardHandler::class,
            trans('backoffice::auth.users'),
        ]);

        return view()->make('backoffice::index', [
            'title' => trans('backoffice::auth.users'),
            'list' => $list,
            'breadcrumb' => $breadcrumb,
        ]);
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.users.url', 'operators');

        $router
            ->get("$backofficePrefix/$routePrefix/", [
                'uses' => self::class,
                'permission' => Permission::OPERATOR_LIST,
            ])
            ->name(self::class)
            ->middleware([Kernel::BACKOFFICE_LISTING]);
    }

    public static function route(): string
    {
        return route(self::class);
    }

    private function getListing(): Listing
    {
        $listing = backoffice()->listing([
            'firstName' => trans('backoffice::auth.first_name'),
            'lastName' => trans('backoffice::auth.last_name'),
            'email' => trans('backoffice::auth.email'),
            'username' => trans('backoffice::auth.username'),
            'activated' => trans('backoffice::auth.activated'),
            'lastLogin' => trans('backoffice::auth.last_login'),
            'user_id', 'name', 'id',
        ]);

        $listing->columns()
            ->hide(['id', 'user_id', 'name'])
            ->sortable(['firstName', 'lastName', 'lastLogin', 'email', 'username']);

        $listing->addValueExtractor('firstName', function (User $user): string {
            return $user->getName()->getFirstName();
        });

        $listing->addValueExtractor('lastName', function (User $user): string {
            return $user->getName()->getLastName();
        });

        $listing->addValueExtractor('lastLogin', function (User $user): string {
            return $user->getLastLogin() ? $user->getLastLogin()->format(trans('backoffice::default.datetime_format')) : '';
        });

        $listing->addValueExtractor('id', function (User $user): int {
            return $user->getUserId();
        });

        return $listing;
    }

    private function buildFilters(Listing $list): void
    {
        $filters = $list->filters();

        $filters->text(UserFilter::EMAIL, trans('backoffice::auth.email'), ['class' => 'form-control']);
        $filters->text(UserFilter::USERNAME, trans('backoffice::auth.username'), ['class' => 'form-control']);
        $filters->text(UserFilter::FIRST_NAME, trans('backoffice::auth.first_name'), ['class' => 'form-control']);
        $filters->text(UserFilter::LAST_NAME, trans('backoffice::auth.last_name'), ['class' => 'form-control']);
        $filters->boolean(UserFilter::ACTIVATED, trans('backoffice::auth.activated'), ['class' => 'form-control']);
    }

    private function buildListActions(Listing $list, UserCriteriaRequest $request): void
    {
        $actions = backoffice()->actions();

        $actions->link(function () {
            try {
                return security()->url()->to(UserCreateFormHandler::route());
            } catch (SecurityException $e) {
                return false;
            }
        }, fa('plus') . ' ' . trans('backoffice::default.new', ['model' => trans('backoffice::auth.user')]),
        [
            'class' => 'btn btn-primary',
        ]);

        $actions->link(function () use ($request) {
            try {
                return security()->url()->to(UserExportHandler::route($request->all()));
            } catch (SecurityException $e) {
                return false;
            }
        }, fa('file-excel-o') . ' ' . trans('backoffice::default.export'),
        [
            'class' => 'btn btn-success',
        ]);

        $list->setActions($actions);

        $rowActions = backoffice()->actions();

        $rowActions->link(
            function (Collection $row) {
                try {
                    return url()->to(UserShowHandler::route($row->get('id')));
                } catch (SecurityException $e) {
                    return false;
                }
            },
            fa('eye'),
            [
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => trans('backoffice::default.show'),
            ]
        );

        $rowActions->link(
            function (Collection $row) {
                try {
                    return url()->to(UserEditFormHandler::route($row->get('id')));
                } catch (SecurityException $e) {
                    return false;
                }
            },
            fa('edit'),
            [
                'class' => 'text-success',
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => trans('backoffice::default.edit'),
            ]
        );
        $rowActions->form(
            function (Collection $row) {
                if ($row->get('id') == Permission::PROTECTED_USER) {
                    return false;
                }

                try {
                    return url()->to(UserDeleteHandler::route($row->get('id')));
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

        $rowActions->form(
            function (Collection $row) {
                try {
                    return url()->to(UserResetPasswordHandler::route($row->get('id')));
                } catch (SecurityException $e) {
                    return false;
                }
            },
            fa('unlock-alt'),
            Request::METHOD_POST,
            [
                'class' => 'text-warning',
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'data-confirm' => trans('backoffice::auth.reset-password.confirm'),
                'title' => trans('backoffice::auth.reset-password.title'),
            ]
        );

        $rowActions->form(
            function (Collection $row) {
                if ($row['activated']) {
                    return false;
                }

                try {
                    return url()->to(UserResendActivationHandler::route($row->get('id')));
                } catch (SecurityException $e) {
                    return false;
                }
            },
            fa('reply-all'),
            Request::METHOD_POST,
            [
                'class' => 'text-primary',
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'data-confirm' => trans('backoffice::auth.activation.confirm'),
                'title' => trans('backoffice::auth.activation.title'),
            ]
        );

        $list->setRowActions($rowActions);
    }

    /**
     * @return \Illuminate\Pagination\LengthAwarePaginator|int|mixed|string
     */
    private function getData(CriteriaRequest $request)
    {
        /** @var DoctrineUserRepository $users */
        $users = security()->users();

        $filters = $request->getFilter();

        $availableFilters = array_filter($filters->values(), function ($field): bool {
            return $field !== null && $field !== '';
        });

        if ($filters->has('activated')) {
            $availableFilters['activated'] = $filters->getBoolean('activated');
        }

        $sorting = $this->convertSorting($request->getSorting());
        $limit = $request->getPaginationData()->getLimit();
        $offset = $request->getPaginationData()->getOffset();

        return $users->search($availableFilters, $sorting, $limit, $offset);
    }

    /*
     * This is only needed when using any of the digbang/backoffice package repositories
     */
    private function convertSorting(Sorting $userSorting): array
    {
        $sortings = [
            UserSorting::FIRST_NAME => 'u.name.firstName',
            UserSorting::LAST_NAME => 'u.name.lastName',
            UserSorting::EMAIL => 'u.email.address',
            UserSorting::USERNAME => 'u.username',
            UserSorting::LAST_LOGIN => 'u.lastLogin',
        ];

        $converted = [];
        foreach ($userSorting->getRaw() as $field => $sense) {
            if (isset($sortings[$field])) {
                $converted[$sortings[$field]] = $sense;
            }
        }

        return $converted;
    }
}
