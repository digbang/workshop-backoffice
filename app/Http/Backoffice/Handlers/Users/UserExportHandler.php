<?php

namespace App\Http\Backoffice\Handlers\Users;

use App\Exports\UserExport;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\Users\UserCriteriaRequest;
use App\Http\Kernel;
use Digbang\Backoffice\Repositories\DoctrineUserRepository;
use Digbang\Security\Users\User;
use Digbang\Utils\Sorting;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Excel;
use WorkshopBackoffice\Repositories\Criteria\Users\UserSorting;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserExportHandler extends Handler
{
    public function __invoke(UserCriteriaRequest $request, Excel $exporter): BinaryFileResponse
    {
        $items = new Collection($this->getData($request));
        $items = $items->map(function (User $user): array {
            return [
                $user->getName()->getFirstName(),
                $user->getName()->getLastName(),
                $user->getEmail(),
                $user->getUsername(),
                $user->isActivated() ? $user->getActivatedAt()->format(trans('backoffice::default.datetime_format')) : '',
                $user->getLastLogin() ? $user->getLastLogin()->format(trans('backoffice::default.datetime_format')) : '',
            ];
        });

        $filename = (new \DateTime())->format('Ymd') . ' - ' . trans('backoffice::auth.users') . '.xls';

        return $exporter->download(new UserExport($items), $filename);
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.users.url', 'operators');

        $router
            ->get("$backofficePrefix/$routePrefix/export", [
                'uses' => self::class,
                'permission' => Permission::OPERATOR_EXPORT,
            ])
            ->name(self::class)
            ->middleware([Kernel::BACKOFFICE]);
    }

    public static function route(array $filter): string
    {
        return route(self::class, $filter);
    }

    /**
     * @return \Illuminate\Pagination\LengthAwarePaginator|int|mixed|string
     */
    private function getData(UserCriteriaRequest $request)
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

        return $users->search($availableFilters, $sorting, null, null);
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
