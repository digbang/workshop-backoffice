<?php

namespace App\Http\Backoffice\Handlers\GuestCategories;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\GuestCategories\GuestCategoryCriteriaRequest;
use App\Http\Kernel;
use Digbang\Backoffice\Listings\Listing;
use Digbang\Security\Exceptions\SecurityException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use WorkshopBackoffice\Entities\GuestCategory;
use WorkshopBackoffice\Repositories\Criteria\GuestCategories\GuestCategoryFilter;
use WorkshopBackoffice\Repositories\Criteria\GuestCategories\GuestCategorySorting;
use WorkshopBackoffice\Repositories\GuestCategoryRepository;

class GuestCategoryListHandler extends Handler
{
    public function __invoke(GuestCategoryCriteriaRequest $request, GuestCategoryRepository $repository): \Illuminate\Contracts\View\View
    {
        $list = $this->getListing();

        /** @var GuestCategoryFilter $filter */
        $filter = $request->getFilter();

        /** @var GuestCategorySorting $sorting */
        $sorting = $request->getSorting();

        $data = $repository->filter($filter, $sorting, $request->getPaginationData()->getLimit(), $request->getPaginationData()->getOffset());

        $this->buildFilters($list);
        $this->buildListActions($list, $request);

        $list->fill($data);

        $breadcrumb = backoffice()->breadcrumb([
            trans('backoffice::default.home') => DashboardHandler::class,
            trans('labels.backoffice.guestCategory.plural'),
        ]);

        return view()->make('backoffice::index', [
            'title' => trans('labels.backoffice.guestCategory.plural'),
            'list' => $list,
            'breadcrumb' => $breadcrumb,
        ]);
    }

    public static function defineRoute(\Illuminate\Routing\Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->get("$backofficePrefix/guests/categories", [
                'uses' => self::class,
                'permission' => Permission::GUEST_CATEGORY_LIST,
            ])
            ->name(self::class)
            ->middleware([Kernel::BACKOFFICE_LISTING]);
    }

    public static function route(): string
    {
        return route(static::class);
    }

    private function getListing(): Listing
    {
        $listing = backoffice()->listing([
            'name' => trans('labels.backoffice.guestCategory.fields.name'),
            'createdAt' => trans('labels.backoffice.guestCategory.fields.createdAt'),
            'updatedAt' => trans('labels.backoffice.guestCategory.fields.updatedAt'),
            'id',
        ]);

        $listing->columns()
            ->hide(['id'])
            ->sortable(['name', 'createdAt', 'updatedAt']);

        $listing->addValueExtractor('name', fn (GuestCategory $guestCategory) => $guestCategory->getName());

        $listing->addValueExtractor('createdAt', fn (GuestCategory $guestCategory) => $guestCategory->getCreatedAt()->toAtomString());

        $listing->addValueExtractor('updatedAt', fn (GuestCategory $guestCategory) => $guestCategory->getUpdatedAt()->toAtomString());

        $listing->addValueExtractor('id', fn (GuestCategory $guestCategory) => $guestCategory->getId());

        return $listing;
    }

    private function buildFilters(Listing $list): void
    {
        $filters = $list->filters();

        $filters->text(GuestCategoryFilter::NAME, trans('labels.backoffice.guestCategory.fields.name'), ['class' => 'form-control']);
    }

    private function buildListActions(Listing $list, GuestCategoryCriteriaRequest $request): void
    {
        $actions = backoffice()->actions();
        $bulkActions = backoffice()->actions();

        try {
            $actions->link(
                url()->to(GuestCategoryCreateFormHandler::route()),
                fa('plus') . ' ' . trans('labels.backoffice.guestCategory.new.category'),
                [
                    'class' => 'btn btn-primary',
                ]
            );
        } catch (SecurityException $e) { /* Do nothing */
        }

        $list->setBulkActions($bulkActions, collect()->toArray());
        $list->setActions($actions);

        $rowActions = backoffice()->actions();

        $rowActions->link(function (Collection $row) {
            try {
                return url()->to(GuestCategoryEditFormHandler::route($row->get('id')));
            } catch (SecurityException $e) {
            }
        }, fa('edit'), [
            'data-toggle' => 'tooltip',
            'data-placement' => 'top',
            'title' => trans('backoffice::default.edit'),
        ]);

        $rowActions->link(function (Collection $row) {
            try {
                return url()->to(GuestCategoryShowHandler::route($row->get('id')));
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
                    return url()->to(GuestCategoryDeleteHandler::route($row->get('id')));
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

        $actions = $bulkActions->dropdown(fa('gear') . ' Opciones', ['class' => 'btn btn-primary']);

        try {
            $actions->form(
                url()->to(GuestCategoryBulkDeleteHandler::route()),
                fa('fa-medkit') . ' Eliminar todos',
                'DELETE',
                ['class' => 'btn-link']
            );
        } catch (SecurityException $ex) { /* Nothing to do */
        }

        $list->setRowActions($rowActions);
    }
}
