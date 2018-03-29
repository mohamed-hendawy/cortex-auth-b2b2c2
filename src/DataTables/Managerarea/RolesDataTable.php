<?php

declare(strict_types=1);

namespace Cortex\Auth\B2B2C2\DataTables\Managerarea;

use Cortex\Auth\Models\Role;
use Cortex\Foundation\DataTables\AbstractDataTable;

class RolesDataTable extends AbstractDataTable
{
    /**
     * {@inheritdoc}
     */
    protected $model = Role::class;

    /**
     * Get the query object to be processed by dataTables.
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Illuminate\Support\Collection
     */
    public function query()
    {
        $currentUser = $this->request->user($this->request->route('guard'));

        $query = $currentUser->can('superadmin') || config('rinvex.tenants.active')->isOwner($currentUser)
            ? app($this->model)->query() : app($this->model)->query()->whereIn('id', $currentUser->roles->pluck('id')->toArray());

        return $this->applyScopes($query);
    }

    /**
     * Display ajax response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax()
    {
        return datatables($this->query())
            ->orderColumn('title', 'title->"$.'.app()->getLocale().'" $1')
            ->make(true);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns(): array
    {
        $link = config('cortex.foundation.route.locale_prefix')
            ? '"<a href=\""+routes.route(\'managerarea.roles.edit\', {role: hashids.encode(full.id), locale: \''.$this->request->segment(1).'\'})+"\">"+data+"</a>"'
            : '"<a href=\""+routes.route(\'managerarea.roles.edit\', {role: hashids.encode(full.id)})+"\">"+data+"</a>"';

        return [
            'title' => ['title' => trans('cortex/auth::common.title'), 'render' => $link, 'responsivePriority' => 0],
            'name' => ['title' => trans('cortex/auth::common.name')],
            'created_at' => ['title' => trans('cortex/auth::common.created_at'), 'render' => "moment(data).format('MMM Do, YYYY')"],
            'updated_at' => ['title' => trans('cortex/auth::common.updated_at'), 'render' => "moment(data).format('MMM Do, YYYY')"],
        ];
    }
}
