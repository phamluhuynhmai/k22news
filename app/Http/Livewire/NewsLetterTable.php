<?php

namespace App\Http\Livewire;

use App\Exports\SubscriberExport;
use App\Mail\ManualPaymentGuideMail;
use App\Mail\SubscriberMail;
use App\Mail\TestMail;
use App\Models\Setting;
use App\Models\BulkMail;
use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\Views\Column;

class NewsLetterTable extends LivewireTableComponent
{
    public $search = '';

    public $orderBy = 'desc';  // default

    protected $listeners = ['refresh' => '$refresh', 'resetPage', 'sendBulkMail',];

    public string $tableName = 'newsLatter';

    public string $pageName = 'news-latter';

    /**
     * @var \null[][]
     */
    protected $queryString = []; //url

    public function columns(): array
    {
        return [
            Column::make(__('messages.emails.email'), 'email')
                ->searchable(),
            Column::make(__('messages.common.action'), 'id')
                ->addClass('text-center'),
        ];
    }

    public function query(): Builder
    {
        return Subscriber::query();
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.news_letter_table';
    }

    public function render()
    {
        return view('livewire-tables::' . config('livewire-tables.theme') . '.datatable')
            ->with([
                'columns' => $this->columns(),
                'rowView' => $this->rowView(),
                'filtersView' => $this->filtersView(),
                'customFilters' => $this->filters(),
                'rows' => $this->rows,
                'modalsView' => $this->modalsView(),
                'bulkActions' => $this->bulkActions,
            ]);
    }
    public function bulkActions(): array
    {
        // Figure out what actions the admin gets
        return [
            'exportSelected' => __('Export'),
            'bulkMail' => __('Send Mail'),
        ];
    }

    public function exportSelected()
    {
        if ($this->selectedRowsQuery->count() > 0) {
            $data = $this->selectedRowsQuery->get();
            $Contact = [];
            foreach ($data as $user) {
                $users = [
                    'email' => $user->email,
                ];
                $Contact[] = $users;
            }
            krsort($Contact);
            return Excel::download(new SubscriberExport($Contact), 'Subscriber.csv');
        }

        $message = __('messages.mails.select_mail');

        $this->dispatchBrowserEvent('error', $message);
    }
    public function bulkMail()
    {
        if ($this->selectedRowsQuery->count() > 0) {
            if ($this->selectedRowsQuery->count()) {
                $data = $this->selectedRowsQuery->get()->pluck('email')->toArray();
                $this->dispatchBrowserEvent('sendMail', $data);
            } else {
                $message = __('messages.mails.you_can_not_send_more_than_5_mails');
                $this->dispatchBrowserEvent('error', $message);
            }
        } else {
            $message = __('messages.mails.select_mail');
            $this->dispatchBrowserEvent('error', $message);
        }
    }

    public function sendBulkMail($emailArray, $content, $subject)
    {

        foreach ($emailArray as $email) {
            BulkMail::create([
                'email'   =>  $email,
                'body'    =>  $content,
                'subject' =>  $subject,
            ]);
        }
        $this->resetAll();
        $this->dispatchBrowserEvent('sendMailClose', true);
        $message = __('messages.placeholder.email_send_successfully');
        $this->dispatchBrowserEvent('success', $message);
    }
}
