<?php

namespace App\Http\Backoffice\Handlers\Roles;

use App\Exports\RoleExport;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\Roles\RoleCriteriaRequest;
use App\Http\Kernel;
use Digbang\Security\Roles\Role;
use Digbang\Security\Users\User;
use Digbang\Utils\Sorting;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use WorkshopBackoffice\Repositories\Criteria\Roles\RoleSorting;

class RoleExportHandler extends Handler
{
    public function __invoke(RoleCriteriaRequest $request, Excel $exporter): BinaryFileResponse
    {
        $items = new Collection($this->getData($request));
        $items = $items->map(function (Role $role): array {
            $users = [];

            /** @var User $user */
            foreach ($role->getUsers() as $user) {
                $users[] = $user->getName();
            }

            return [
                $role->getRoleId(),
                $role->getName(),
                implode(', ', $users),
            ];
        });

        $filename = (new \DateTime())->format('Ymd') . ' - ' . trans('backoffice::auth.roles') . '.xls';

        return $exporter->download(new RoleExport($items), $filename);
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.roles.url', 'roles');

        $router
            ->get("$backofficePrefix/$routePrefix/export", [
                'uses' => self::class,
                'permission' => Permission::ROLE_EXPORT,
            ])
            ->name(self::class)
            ->middleware([Kernel::BACKOFFICE]);
    }

    public static function route(array $filter): string
    {
        return route(self::class, $filter);
    }

    /**
     * @return array|\Illuminate\Pagination\LengthAwarePaginator
     */
    private function getData(RoleCriteriaRequest $request)
    {
        /** @var \Digbang\Backoffice\Repositories\DoctrineRoleRepository $roles */
        $roles = security()->roles();

        $filter = $request->getFilter()->values();
        $sorting = $this->convertSorting($request->getSorting());

        return $roles->search($filter, $sorting, null, null);
    }

    /*
     * This is only needed when using any of the digbang/backoffice package repositories
     */
    private function convertSorting(Sorting $roleSorting): array
    {
        $sortings = [
            RoleSorting::NAME => 'r.name',
        ];

        $converted = [];
        foreach ($roleSorting->getRaw() as $field => $sense) {
            if (isset($sortings[$field])) {
                $converted[$sortings[$field]] = $sense;
            }
        }

        return $converted;
    }
}
