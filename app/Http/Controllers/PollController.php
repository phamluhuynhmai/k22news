<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePollReqeust;
use App\Models\Poll;
use App\Models\PollResult;
use App\Repositories\PollRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;

class PollController extends AppBaseController
{
    /**
     * @var PollRepository
     */
    private $PollRepository;

    /**
     * PollRepository constructor.
     *
     * @param  PollRepository  $PollRepository
     */
    public function __construct(PollRepository $PollRepository)
    {
        $this->PollRepository = $PollRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(Request $request): Application|Factory|View
    {
        return view('Polls.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(): View|Factory|Application
    {
        return view('Polls.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Application|RedirectResponse|Redirector
     */
    public function store(CreatePollReqeust $request): Redirector|RedirectResponse|Application
    {
        $input = $request->all();
        $this->PollRepository->create($input);

        Flash::success(__('messages.placeholder.poll_created_successfully'));

        return redirect(route('polls.index'));
    }
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View|Response
     */
    public function edit(Poll $poll): View|Factory|Response|Application
    {
        return view('Polls.edit', compact('poll'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return Application|Redirector|RedirectResponse
     */
    public function update(CreatePollReqeust $request, Poll $poll): Application|RedirectResponse|Redirector
    {
        $request['status'] = isset($request['status']);
        $this->PollRepository->update($request->all(), $poll->id);

        Flash::success(__('messages.placeholder.poll_updated_successfully'));

        return redirect(route('polls.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Poll $poll)
    {
        $poll->delete();

        return $this->sendSuccess(__('messages.placeholder.poll_deleted_successfully'));
    }

    public function status($id)
    {
        $poll = Poll::findOrFail($id);

        $status = ! $poll->status;

        $poll->update(['status' => $status]);

        return $this->sendSuccess(__('messages.placeholder.status_updated_successfully'));
    }

    public function votePoll(Request $request)
    {
        $input = $request->all();
        $input['ip_address'] = $request->ip();

        $poll = Poll::findOrFail($input['poll_id']);
        if ($poll->vote_permission == 2 && ! Auth::check()) {
            return $this->sendError('Please login');
        }
        $isExist = PollResult::wherePollId($poll->id)->whereIpAddress($input['ip_address'])->exists();
        if ($isExist) {
            return $this->sendError(__('messages.placeholder.You_already_voted'), ['poll_id' => $poll->id]);
        }

        PollResult::create($input);

        $statistics = getPollStatistics($poll->id);

        return $this->sendResponse($statistics, __('messages.placeholder.poll_voted_successfully'));
    }

    public function pollResult($id): Factory|View|Application
    {
        return view('Polls.poll_result', compact('id'));
    }
}
