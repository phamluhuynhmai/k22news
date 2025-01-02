<?php

namespace App\Http\Livewire;

use App\Exports\ContactExport;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ContactTable extends LivewireTableComponent
{
    public string $tableName = 'Contact';
    public string $pageName = 'Contact';

    /**
     * @var \null[][]
     */
    protected $queryString = []; //url

    protected $listeners = ['refresh' => '$refresh', 'resetPage'];

    public function columns(): array
    {
        return [

            Column::make(__('messages.common.name'), 'name')
                ->sortable()->searchable(),
            Column::make(__('messages.emails.email'), 'email')
                ->sortable()->searchable(),
            Column::make(__('messages.phone'), 'phone')
                ->sortable(),
            Column::make(__('messages.common.action'), 'id')
                ->addClass('text-center'),
        ];
    }

    public function query(): Builder
    {
        return Contact::query();
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.contact_table';
    }

    public function bulkActions(): array
    {
        // Figure out what actions the admin gets
        return [
            'exportSelected' => __('Export'),
        ];
    }

    public function exportSelected()
    {
        if ($this->selectedRowsQuery->count() > 0) {
            $data = $this->selectedRowsQuery->get();
            
            $Contact = [];
            foreach ($data as $user) {
                $users = [
                    'name'  => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ];
                $Contact[] = $users;
            }
            return Excel::download(new ContactExport($Contact), 'Contact.csv');
    
        }
        
        $message = __('messages.post.select_post');

        $this->dispatchBrowserEvent('error', $message);
      
    }
}
