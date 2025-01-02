<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\RssFeed;
use App\Models\User;
use App\Repositories\DashboardRepository;
use App\Scopes\LanguageScope;
use App\Scopes\PostDraftScope;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends AppBaseController
{
    /* @var DashboardRepository */
    private DashboardRepository $dashboardRepository;

    /**
     * DashboardController constructor.
     *
     * @param  DashboardRepository  $dashboardRepo
     */
    public function __construct(DashboardRepository $dashboardRepo)
    {
        $this->dashboardRepository = $dashboardRepo;
    }

    /**
     * @return Application|Factory|View
     */
    // hiển trị trang chính
    public function index()
    {
        // đếm số lượng bài viết    
        $posts = Post::withoutGlobalScope(LanguageScope::class)->withoutGlobalScope(PostDraftScope::class)->count();
        // đếm số lượng bài nháp
        $postsDraft = Post::withoutGlobalScope(LanguageScope::class)->withoutGlobalScope(PostDraftScope::class)->where('status', Post::STATUS_DRAFT)->count();
        // đếm số lượng người dùng mới nhất
        $users = User::with('media')->where('type', 2)
            ->latest()->orderBy('id', 'desc')->take(5)->get();
        // đếm số lượng feed rss
        $rss = RssFeed::count();
        // đếm số lượng bài viết có feed rss    
        $rssPost = Post::withoutGlobalScope(LanguageScope::class)->withoutGlobalScope(PostDraftScope::class)->whereIsRss(true)->count();

        return view('dashboard.index', compact('posts', 'postsDraft', 'users', 'rss', 'rssPost'));
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function getChart(Request $request)
    {
        $input = $request->all();
        $language = $this->dashboardRepository->updateChartRange($input);

        return $this->sendResponse($language, __('messages.placeholder.chart_updated_successfully'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'section' => 'required|string',
                'content' => 'required|string',
                // Add other validation rules
            ]);

            // Process your form data
            $result = null; // Initialize $result or add your processing logic here

            return response()->json([
                'success' => true,
                'message' => 'Content saved successfully',
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Gallery creation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error processing request',
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
