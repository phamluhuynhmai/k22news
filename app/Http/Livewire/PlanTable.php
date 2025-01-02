<?php

namespace App\Http\Livewire;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Plan;

class PlanTable extends LivewireTableComponent
{

    protected $listeners = ['refresh' => '$refresh', 'resetPage'];

    public string $tableName = 'plan';
    public string $pageName = 'plan';

    public $orderBy = 'desc';  // default

    protected $queryString = []; //url

    public function query(): Builder
    {
        return Plan::query();
    }

    public function render()
    {
        return view('livewire-tables::'.config('livewire-tables.theme').'.datatable')
            ->with([
                'columns' => $this->columns(),
                'rowView' => $this->rowView(),
                'filtersView' => $this->filtersView(),
                'customFilters' => $this->filters(),
                'rows' => $this->rows,
                'modalsView' => $this->modalsView(),
                'bulkActions' => $this->bulkActions,
                'componentName' => 'plan.add-button',
            ]);
    }

    public function columns(): array
    {
        return [
            Column::make(__('messages.common.name'), "name")
                ->sortable()->searchable(),
            Column::make(__('messages.plans.price'), "price")
                ->sortable()->searchable()->addClass('plan-amount'),
            Column::make(__('messages.plans.frequency'), "frequency")
                ->sortable()->addClass('date-align'),
            Column::make(__('messages.language.is_default'), "is_default")
                ->sortable(),
            Column::make(__('messages.common.action'), "id")->addClass('w-100px justify-content-center d-flex'),
        ];
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.plan_table';
    }
}
