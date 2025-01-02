<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateMailSettingRequest;
use App\Mail\ManualPaymentGuideMail;
use App\Mail\TestMail;
use App\Models\MailSetting;
use App\Repositories\MailSettingRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Laracasts\Flash\Flash;

class MailSettingController extends Controller
{
    /**
     * @var MailSettingRepository
     */
    private $MailSettingRepository;

    /**
     * CategoryRepository constructor.
     *
     * @param  MailSettingRepository  $MailSettingRepository
     */
    public function __construct(MailSettingRepository $MailSettingRepository)
    {
        $this->MailSettingRepository = $MailSettingRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory
     */
    public function index()
    {
        $mailsetting = MailSetting::first();

        return view('mail_setting.index', compact('mailsetting'));
    }

    /**
     * @return RedirectResponse
     */
    public function update(UpdateMailSettingRequest $request)
    {
        $id = Auth::id();

        $this->MailSettingRepository->update($request->all(), $id);

        Flash::success(__('messages.placeholder.mail_updated_successfully'));

        return Redirect::back();
    }

    /**
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function mail(Request $request)
    {
        $id = Auth::id();

        $this->MailSettingRepository->update($request->all(), $id);

        Flash::success(__('messages.placeholder.mail_updated_successfully'));

        return Redirect::back();
    }

    /**
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function contactMessage(Request $request)
    {

        $request->validate([
            'contact_mail' => 'nullable|email:filter',
        ]);

        $id = Auth::id();

        $this->MailSettingRepository->update($request->all(), $id);

        Flash::success(__('messages.placeholder.mail_updated_successfully'));

        return Redirect::back();
    }

    /**
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function sendTestemail(Request $request)
    {

        $request->validate([
            'test_mails' => 'required|email:filter',
        ]);

        $id = Auth::id();
        Mail::to($request['test_mails'])
            ->send(new TestMail($request['test_mails'], $id));
//        $this->MailSettingRepository->update($request->all(), $id);

        Flash::success(__('messages.placeholder.test_mail_send_successfully'));

        return Redirect::back();
    }
}
