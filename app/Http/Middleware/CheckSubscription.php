<?php

namespace App\Http\Middleware;

use App\Models\Post;
use App\Models\Subscription;
use App\Models\Vcard;
use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class CheckSubscription
{
    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return Application|RedirectResponse|Redirector|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $request = $next($request);

        $subscription = Subscription::with('plan')
            ->where('status', Subscription::ACTIVE)
            ->where('user_id', getLogInUser()->id)
            ->first();

        if (!$subscription) {
            Post::where('created_by', getLogInUser()->id)->update([
                'status' => 0,
            ]);

            return redirect(route('subscription.upgrade'))
                ->withErrors(__('messages.placeholder.your_plan_is_expired_Please_choose_a_plan_to_continue_the_services'));
        }

        if ($subscription->isExpired()) {
            Post::where('created_by', getLogInUser()->id)->update([
                'status' => 0,
            ]);

            return redirect(route('subscription.upgrade'))
                ->withErrors(__('messages.placeholder.your_plan_is_expired_Please_choose_a_plan_to_continue_the_services'));
        }

        return $request;
    }
}
