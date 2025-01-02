<?php

// Import các model và class cần thiết
use App\Models\AdSpaces;
use App\Models\Analytic;
use App\Models\Category;
use App\Models\Followers;
use App\Models\Language;
use App\Models\MailSetting;
use App\Models\Menu;
use App\Models\Navigation;
use App\Models\Page;
use App\Models\PaymentGateway;
use App\Models\Plan;
use App\Models\Poll;
use App\Models\PollResult;
use App\Models\Post;
use App\Models\SeoTool;
use App\Models\Setting;
use App\Models\SubCategory;
use App\Models\Subscription;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Models\Currency;
use Jenssegers\Agent\Agent;
use Stripe\Stripe;

/**
 * Lấy thông tin người dùng đang đăng nhập
 * @return Authenticatable|null
 */
function getLogInUser()
{
    return Auth::user();
}

/**
 * Lấy tên ứng dụng từ cài đặt
 * @return mixed
 */
function getAppName()
{
    static $appName;

    if (empty($appName)) {
        $appName = Setting::where('key', '=', 'application_name')->first()->value;
    }

    return $appName;
}

/**
 * Lấy ID của người dùng đang đăng nhập
 * @return int
 */
function getLogInUserId()
{
    return Auth::user()->id;
}

/**
 * Lấy URL dashboard tương ứng với role của user
 * @return string
 */
function getDashboardURL()
{
    if (Auth::user()->hasRole('customer')) {
        return RouteServiceProvider::CUSTOMER;
    }
    if (Auth::user()->hasRole('clinic_admin')) {
        return RouteServiceProvider::HOME;
    }

    return RouteServiceProvider::HOME;
}

/**
 * Lấy danh sách ngôn ngữ dưới dạng collection
 * @return \Illuminate\Support\Collection
 */
function getLanguage()
{
    static $language;
    if (empty($language)) {
        $language = Language::pluck('name', 'id');
    }

    return $language;
}

/**
 * Lấy danh sách ngôn ngữ với mã ISO
 * @return \Illuminate\Support\Collection
 */
function getLanguageSet()
{
    $language = Language::pluck('name', 'iso_code');

    return $language;
}

/**
 * Lấy danh sách album theo ngôn ngữ
 * @param $langId
 * @return mixed
 */
function getAlbums($langId)
{
    return \App\Models\Album::where('lang_id', $langId)->toBase()->pluck('name', 'id')->toArray();
}

/**
 * Lấy danh mục album theo ngôn ngữ và ID album
 * @param $albumId
 * @param $langId
 * @return array
 */
function getAlbumCategory($albumId, $langId): array
{
    return \App\Models\AlbumCategory::where('lang_id', $langId)->where('album_id', $albumId)->pluck('name',
        'id')->toArray();
}

/**
 * Lấy màu ngẫu nhiên từ danh sách màu có sẵn
 * @param $index
 * @return string
 */
function getRandomColor($index): string
{
    $badgeColors = [
        'primary',
        'success', 
        'info',
        'danger',
        'warning',
    ];
    $number = ceil($index % 5);

    return $badgeColors[$number];
}

/**
 * Lấy danh sách menu cha
 * @param $index
 * @return string
 */
function getParentMenu()
{
    $menu = Menu::whereNotNull('link')->pluck('link', 'id')->sort();

    return $menu;
}

/**
 * Lấy các phần tử header của trang web
 * Bao gồm menu chính và menu con
 * @return mixed
 */
function getHeaderElement()
{
    // Lấy danh sách navigation cha không có parent_id
    $data['navigations'] = Navigation::with('navigationable')
        ->whereHas('navigationable', function ($q) {
            $q->where('show_in_menu', 1);
        })->whereNull('parent_id')->orderBy('order_id')->get();

    // Lấy danh sách navigation con
    $data['navigationsTakeData'] = [];
    foreach ($data['navigations'] as $item) {
        $navigationType = $item->navigationable_type == Category::class ? SubCategory::class : $item->navigationable_type;
        $data['navigationsTakeData'][$item->id] = Navigation::with('navigationable')
            ->whereHas('navigationable', function ($q) {
                $q->where('show_in_menu', 1);
            })->where('navigationable_type', $navigationType)
            ->where('parent_id', $item->navigationable_id)->orderBy('order_id')->get();
    }

    // Lấy danh sách trang hiển thị trên menu chính
    $data['pages'] = Page::where('location', Page::MAIN_MENU)->where('visibility', 1)->get()->sort();

    return $data;
}

/**
 * Lấy danh sách bài viết gần đây
 * @return mixed
 */
function getRecentPost()
{
    return Post::with('language', 'category')->whereVisibility(Post::VISIBILITY_ACTIVE)->latest('id')->take(3)->get();
}

/**
 * Lấy danh sách danh mục đang hoạt động
 * @return Category[]|Collection
 */
function getCategory()
{
    return Category::active()->where('show_in_menu', 1)->get();
}

/**
 * Lấy giá trị cài đặt từ bảng settings
 * @return mixed
 */
function getSettingValue()
{
    static $settingValues = [];

    if (empty($settingValues)) {
        $settingValues = Setting::pluck('value', 'key')->toArray();
    }

    return $settingValues;
}

/**
 * Lấy URL hiện tại
 * @return string
 */
function getUrl()
{
    return Request::url();
}

/**
 * Lấy chi tiết navigation cho menu
 * Bao gồm menu chính và menu con
 * @return array
 */
function getNavigationDetails(): array
{
    // Lấy navigation cha
    $data['navigations'] = Navigation::with('navigationable')
        ->whereHas('navigationable', function ($q) {
            $q->where('show_in_menu', 1);
        })->whereNull('parent_id')->orderBy('order_id')->get();

    $data['menus'] = [];

    // Lọc menu theo ngôn ngữ hiện tại
    foreach ($data['navigations'] as $menu) {
        if ($menu['navigationable']['lang_id'] == getFrontSelectLanguage()) {
            $data['menus'][] = $menu;
        } elseif ($menu->navigationable_type == Menu::class) {
            $data['menus'][] = $menu;
        }
    }

    // Giới hạn 6 menu chính
    $data['navigations'] = collect($data['menus'])->take(6);

    // Lấy menu con
    $data['navigationsTakeData'] = [];
    foreach ($data['navigations'] as $item) {
        $navigationType = $item->navigationable_type == Category::class ? SubCategory::class : $item->navigationable_type;
        $data['navigationsTakeData'][$item->id] = Navigation::with('navigationable')
            ->whereHas('navigationable', function ($q) {
                $q->where('show_in_menu', 1);
            })->where('navigationable_type', $navigationType)
            ->where('parent_id', $item->navigationable_id)->orderBy('order_id')->get();
    }

    // Đếm số menu không có menu con
    $data['menuCount'] = [];
    foreach ($data['navigationsTakeData'] as $menuGet) {
        if ($menuGet->isEmpty()) {
            $data['menuCount'];
        }
    }

    // Lấy các navigation còn lại (ngoài 6 menu chính)
    $data['navigationsSkipData'] = Navigation::with('navigationable')
        ->whereHas('navigationable', function ($q) {
            $q->where('show_in_menu', 1);
        })->whereNull('parent_id')
        ->whereNotIn('id', $data['navigations']->pluck('id')->toArray())->orderBy('order_id')->get();

    // Lấy menu con cho các navigation còn lại
    $data['navigationsSkipItem'] = [];
    foreach ($data['navigationsSkipData'] as $item) {
        $navigationType = $item->navigationable_type == Category::class ? SubCategory::class : $item->navigationable_type;
        $data['navigationsSkipItem'][$item->id] = Navigation::with('navigationable')
            ->whereHas('navigationable', function ($q) {
                $q->where('show_in_menu', 1);
            })->where('navigationable_type', $navigationType)
            ->where('parent_id', $item->navigationable_id)->orderBy('order_id')->get();
    }

    // Đếm tổng số menu không bao gồm menu ngôn ngữ khác
    $countMenu = Category::whereShowInMenu(1)->where('lang_id', '!=', getFrontSelectLanguage())->count();
    $data['navigationsCount'] = $data['navigationsSkipData']->count() + $data['navigations']->count() - $countMenu;

    // Lấy danh sách trang theo ngôn ngữ hiện tại
    $data['pages'] = Page::whereLangId(getFrontSelectLanguage())->where('location', Page::MAIN_MENU)->where('visibility', 1)->get()->sort();

    return $data;
}

/**
 * Lấy danh sách tin tức phổ biến dựa trên số lượt xem
 * @return array
 */
function getPopularNews()
{
    // Đếm số lượt xem cho mỗi bài viết, giới hạn 6 bài
    $countPosts = DB::table('analytics')->select(
        'post_id',
        DB::raw('count("post_id") as total_count')
        )->limit(6)
        ->groupBy('post_id')
        ->orderBy('total_count', 'desc')
        ->get();

    // Lấy thông tin danh mục
    $categories = Category::toBase()->pluck('name', 'id')->toArray();
    
    // Lấy chi tiết bài viết kèm video
    $postData = Post::with('postVideo')->whereVisibility(Post::VISIBILITY_ACTIVE)
        ->whereIn('id', $countPosts->pluck('post_id')->toArray())->get();
    
    static $popularNews = [];
    if (empty($popularNews)) {
        foreach ($countPosts as $countPost) {
            $post = $postData->where('id', $countPost->post_id)->first();
            if (! empty($post)) {
                $post = $post->toArray();
                $post['category'] = ['name' => ! empty($categories[$post['category_id']]) ? $categories[$post['category_id']] : ''];
                $popularNews[] = $post;
            }
        }
    }

    return $popularNews;
}

/**
 * Lấy số lượt xem của một bài viết
 * @param $id
 * @return int
 */
function getPostViewCount($id)
{
    $postViewCount = Analytic::wherePostId($id)->count();

    return $postViewCount;
}

/**
 * Lấy danh sách tag phổ biến từ các bài viết được xem nhiều
 * @return array
 */
function getPopularTags()
{
    // Lấy 6 bài viết được xem nhiều nhất
    $countPostsTags = DB::table('analytics')->select('post_id',
        DB::raw('count("post_id") as total_count'))
            ->limit(6)
            ->groupBy('post_id')
            ->orderBy('total_count', 'desc')
            ->get();

    static $popularTags = [];
    $postData = Post::toBase()->whereVisibility(Post::VISIBILITY_ACTIVE)
        ->whereIn('id', $countPostsTags->pluck('post_id')->toArray())->get();
    
    // Tạo mảng các tag từ bài viết phổ biến
    if (empty($popularTags)) {
        foreach ($countPostsTags as $countPostsTag) {
            $postTag = $postData->where('id', $countPostsTag->post_id)->pluck('tags', 'id')->sort()->first();
            if (! empty($postTag)) {
                $popularTags[] = $postTag;
            }
        }
    }

    // Tách các tag thành mảng riêng lẻ
    $tagArr = [];
    foreach (array_filter($popularTags) as $tags) {
        foreach (explode(',', $tags) as $tag) {
            $tagArr[] = $tag;
        }
    }

    return array_unique($tagArr);
}

/**
 * Lấy danh sách cuộc thăm dò ý kiến
 * @return Poll[]|Builder[]|Collection
 */
function getPoll()
{
    if (! Auth::check()) {
        // Nếu chưa đăng nhập, chỉ lấy các poll cho phép vote không cần đăng nhập
        return Poll::where('lang_id', getFrontSelectLanguage())
            ->where('vote_permission', 1)
            ->whereStatus(1)
            ->limit(3)
            ->get();
    } else {
        // Nếu đã đăng nhập, lấy tất cả poll đang hoạt động
        return Poll::where('lang_id', getFrontSelectLanguage())
            ->whereStatus(1)
            ->limit(3)
            ->get();
    }
}

/**
 * Lấy danh sách các tùy chọn trả lời cho poll
 * @return string[]
 */
function getOption(): array
{
    return [
        'option1', 'option2', 'option3', 'option4', 'option5', 
        'option6', 'option7', 'option8', 'option9', 'option10',
    ];
}

/**
 * Lấy thống kê kết quả của một cuộc thăm dò
 * @param int $pollId
 * @return array
 */
function getPollStatistics($pollId): array
{
    // Lấy tất cả kết quả của poll
    $pollResults = PollResult::with('poll')->wherePollId($pollId)->get();
    $resultsAns = $pollResults->pluck('answer')->toArray();
    $totalPollResults = count($pollResults);
    $totalPerAns = array_count_values($resultsAns);
    
    // Tính phần trăm cho từng câu trả lời
    $optionAns = [];
    foreach ($pollResults as $result) {
        $poll = $result->poll;
        foreach (getOption() as $option) {
            if (! empty($poll->$option)) {
                $optionAns[$poll->$option] = ! empty($totalPerAns[$poll->$option])
                    ? intval($totalPerAns[$poll->$option] * 100 / $totalPollResults) : 0;
            }
        }
    }

    $data['totalPollResults'] = $totalPollResults;
    $data['optionAns'] = $optionAns;
    $data['pollId'] = $pollId;

    return $data;
}

/**
 * Lấy class màu ngẫu nhiên cho các thẻ
 * @param $id
 * @return string
 */
function getColorClass($id)
{
    $randomClass = ['world', 'technology', 'travel', 'fashion', 'music', 'animal'];
    $index = $id % 5;

    return $randomClass[$index];
}

/**
 * Lấy danh sách danh mục phổ biến dựa trên lượt xem bài viết
 * @return array
 */
function getPopulerCategories()
{
    // Đếm số lượt xem cho mỗi bài viết, giới hạn 10 bài
    $postCount = DB::table('analytics')->select(
        'post_id',
        DB::raw('count("post_id") as total_count')
        )->limit(10)
        ->groupBy('post_id')
        ->orderBy('total_count', 'desc')
        ->get();

    $popularCategory = [];

    // Lấy bài viết và nhóm theo danh mục
    $posts = Post::toBase()->whereIn('id', $postCount->pluck('post_id')->toArray())
        ->where('visibility', Post::VISIBILITY_ACTIVE)
        ->get()
        ->groupBy('category_id');
    
    // Lấy thông tin danh mục đang hiển thị trên menu
    $categories = Category::toBase()->where('show_in_menu', Category::SHOW_IN_MENU_ACTIVE)->get();
    
    $cnt = 0;
    foreach ($posts as $id => $post) {
        $category = $categories->where('id', $id)->first();
        if (! empty($category)) {
            if ($cnt > 10) {
                continue;
            }
            $popularCategory[$id]['name'] = $category->name;
            $popularCategory[$id]['slug'] = $category->slug;
            $popularCategory[$id]['posts_count'] = $post->count();
            $cnt++;
        }
    }

    return array_values($popularCategory);
}

/**
 * Kiểm tra và thêm http vào URL nếu cần
 * @param $url
 */
function getNavUrl($url)
{
    $contain = Str::contains($url, 'https');
    if ($contain) {
        return $url;
    } else {
        return 'http://'.$url;
    }
}

/**
 * Tính thời gian đọc dựa trên độ dài nội dung
 * @param $body
 * @return string
 */
function getReadingTime($body)
{
    $myContent = $body;
    $word = str_word_count(strip_tags($myContent));
    $m = floor($word / 200);  // Giả định tốc độ đọc 200 từ/phút
    $s = floor($word % 200 / (200 / 60));

    if ($s > 30) {
        $m += 1;
        $s = 00;
    } else {
        $s = 00;
    }

    if ($m == 0) {
        $m += 1;
    }

    $time = $m.' minute'.($m == 1 ? '' : 's');

    return $time;
}

/**
 * Lấy danh sách bài viết xu hướng
 * @return array
 */
function getTrendingPost()
{
    start_measure('render', 'getTrendingPost');
    start_measure('render', 'postsAnalytics');
    
    // Lấy 10 bài viết có lượt xem cao nhất
    $postsAnalytics = DB::table('analytics')->select(
        'post_id',
        DB::raw('count("post_id") as total_count')
        )->limit(10)
        ->groupBy('post_id')
        ->orderBy('total_count', 'desc')
        ->get();
    stop_measure('render', 'postsAnalytics');
    
    $postIds = $postsAnalytics->pluck('post_id')->toArray();
    
    // Lấy thông tin chi tiết của 6 bài viết
    start_measure('render', 'posts');
    $posts = Post::with('category', 'postVideo')
        ->whereVisibility(Post::VISIBILITY_ACTIVE)
        ->whereIn('id', $postIds)
        ->limit(6)
        ->get(['id', 'category_id', 'post_types', 'slug', 'title', 'created_at'])
        ->toArray();
    stop_measure('render', 'posts');
    
    static $trendingPosts = [];
    if (empty($trendingPosts)) {
        $trendingPosts = $posts;
    }
    stop_measure('render', 'getTrendingPost');
    return $trendingPosts;
}

/**
 * Lấy danh sách bài viết tin nóng
 * @return Post[]|Builder[]|Collection
 */
function getBreakingPost()
{
    $getBreakingPost = Post::whereBreaking(1)->whereVisibility(Post::VISIBILITY_ACTIVE)->get();

    return $getBreakingPost;
}

/**
 * Lấy danh sách bài viết được đề xuất
 * @return Post[]|Builder[]|Collection
 */
function getRecommendedPost()
{
    $recommendedPosts = Post::with('category', 'postVideo')
        ->whereRecommended(1)
        ->whereVisibility(Post::VISIBILITY_ACTIVE)
        ->latest()
        ->orderBy('created_at', 'desc')
        ->take(6)
        ->get();

    return $recommendedPosts;
}

/**
 * Lấy ngôn ngữ được chọn trong admin panel
 * @return mixed|null
 */
function getSelectLanguage()
{
    $langIdLanguage = empty(Session::get('languageChange')['data']);

    if ($langIdLanguage) {
        $langId = 1;
    } else {
        $langId = Session::get('languageChange')['data'];
    }

    return $langId;
}

/**
 * Lấy tên ngôn ngữ được chọn trong admin panel
 * @return mixed
 */
function getSelectLanguageName()
{
    return Language::find(getSelectLanguage())->name;
}

/**
 * Lấy ngôn ngữ được chọn ở frontend
 * @return mixed
 */
function getFrontSelectLanguage()
{
    $langIdLanguage = empty(Session::get('frontLanguageChange'));

    if ($langIdLanguage) {
        $langId = getSettingValue()['front_language'];
    } else {
        $langId = Session::get('frontLanguageChange');
    }

    return $langId;
}

/**
 * Lấy tên ngôn ngữ được chọn ở frontend
 * @return mixed
 */
function getFrontSelectLanguageName()
{
    static $languageName;

    if (empty($languageName)) {
        $languageName = ! empty(Language::find(getFrontSelectLanguage())) 
            ? Language::find(getFrontSelectLanguage())->name 
            : '';
    }

    return $languageName;
}

/**
 * Khởi tạo và trả về đối tượng reCaptcha
 * @return \Anhskohbo\NoCaptcha\NoCaptcha
 */
function reCaptcha()
{
    $settings = Setting::pluck('value', 'key')->toArray();
    $secret = $settings['secret_key'];
    $sitekey = $settings['site_key'];
    $captcha = new Anhskohbo\NoCaptcha\NoCaptcha($secret, $sitekey);

    return $captcha;
}

/**
 * Lấy thông tin SEO Tools theo ngôn ngữ hiện tại
 * @return mixed
 */
function getSEOTools()
{
    static $seoTool;

    if (empty($seoTool)) {
        $seoTool = SeoTool::with('language')->first();
    }
    if ($seoTool->language->name == getFrontSelectLanguageName()) {
        return $seoTool;
    }
}

/**
 * Tạo mảng số cho danh mục
 * @param $range
 * @return array
 */
function getCategoryNumbers($range): array
{
    $result = [];
    $count = 1;
    $start = 1;
    foreach ($range as $val) {
        if ($val % 2 == 0) {
            $skip = 1;
        } else {
            $skip = 3;
        }
        $result[] = $start;
        $start += $skip;
        $count++;
    }

    return array_values(array_unique($result));
}

/**
 * Lấy phiên bản hiện tại của ứng dụng từ composer.json
 * @return mixed
 */
function getCurrentVersion()
{
    $composerFile = file_get_contents('../composer.json');
    $composerData = json_decode($composerFile, true);
    return $composerData['version'];
}

/**
 * Kiểm tra trạng thái của quảng cáo
 * @param $name
 * @return mixed
 */
function checkAdSpaced($name)
{
    $check = Setting::where('key', $name)->pluck('value')->first();
    return $check;
}

/**
 * Lấy hình ảnh quảng cáo theo thiết bị (desktop/mobile)
 * @param $id
 * @return mixed
 */
function getAdImageDesktop($id)
{
    $agent = new Agent();
    if($agent->isMobile()){
        $image = AdSpaces::whereAdSpaces($id)->whereAdView(AdSpaces::MOBILE)->first();
    }else {
        $image = AdSpaces::whereAdSpaces($id)->whereAdView(AdSpaces::DESKTOP)->first();
    }

    return $image;
}

/**
 * Lấy hình ảnh quảng cáo cho mobile
 * @param $id
 * @return mixed
 */
function getAdImageMobile($id)
{
    $image = AdSpaces::whereAdSpaces($id)->whereAdView(AdSpaces::MOBILE)->first();
    return $image;
}

/**
 * Lấy cấu hình email
 * @return mixed
 */
function GetMail()
{
    return MailSetting::first();
}

/**
 * Lấy danh sách tiền tệ
 * @return array
 */
function getCurrencies()
{
    $currencies = Currency::all();
    foreach ($currencies as $currency) {
        $currencyList[$currency->id] = $currency->currency_icon.' - '.$currency->currency_name;
    }

    return $currencyList;
}

/**
 * Xóa dấu phẩy từ số
 * @param $number
 * @return mixed
 */
function removeCommaFromNumbers($number)
{
    return (gettype($number) == 'string' && !empty($number)) ? str_replace(',', '', $number) : $number;
}

/**
 * Lấy thông tin gói đăng ký hiện tại của user
 * @return mixed
 */
function getCurrentSubscription()
{
    $subscription = Subscription::with(['plan.currency'])
        ->whereUserId(getLogInUserId())
        ->where('status', Subscription::ACTIVE)->latest()->first();

    return $subscription;
}

/**
 * Định dạng số tiền theo định dạng tiền tệ
 * @param $number
 * @param $currencyCode
 * @return string
 */
function currencyFormat($number, $currencyCode = null)
{
    return  $currencyCode . number_format($number, 2);
}

/**
 * Lấy chi tiết gói đăng ký hiện tại
 * @return array
 */
function getCurrentPlanDetails()
{
    $currentSubscription = getCurrentSubscription();
    $isExpired = $currentSubscription->isExpired();
    $currentPlan = $currentSubscription->plan;

    // Kiểm tra và cập nhật giá nếu cần
    if ($currentPlan->price != $currentSubscription->plan_amount) {
        $currentPlan->price = $currentSubscription->plan_amount;
    }

    // Tính toán thời gian sử dụng
    $startsAt = Carbon::now();
    $totalDays = Carbon::parse($currentSubscription->starts_at)->diffInDays($currentSubscription->ends_at);
    $usedDays = Carbon::parse($currentSubscription->starts_at)->diffInDays($startsAt);
    if ($totalDays > $usedDays) {
        $usedDays = Carbon::parse($currentSubscription->starts_at)->diffInDays($startsAt);
    } else {
        $usedDays = $totalDays;
    }
    if ($totalDays > $usedDays) {
        $remainingDays = $totalDays - $usedDays;
    } else {
        $remainingDays = 0;
    }

    if ($totalDays == 0) {
        $totalDays = 1;
    }

    $frequency = $currentSubscription->plan_frequency == Plan::MONTHLY ? 'Monthly' : 'Yearly';

    // Tính toán số tiền còn lại
    $perDayPrice = round($currentPlan->price / $totalDays, 2);
    if (!empty($currentSubscription->trial_ends_at) || $isExpired) {
        $remainingBalance = 0.00;
        $usedBalance = 0.00;
    } else {
        $isJPYCurrency = !empty($currentPlan->currency) && isJPYCurrency($currentPlan->currency->currency_code);
        $remainingBalance = $currentPlan->price - ($perDayPrice * $usedDays);
        $remainingBalance = $isJPYCurrency ? round($remainingBalance) : $remainingBalance;
        $usedBalance = $currentPlan->price - $remainingBalance;
        $usedBalance = $isJPYCurrency ? round($usedBalance) : $usedBalance;
    }

    return [
        'name'             => $currentPlan->name.' / '.$frequency,
        'trialDays'        => $currentPlan->trial_days,
        'startAt'          => Carbon::parse($currentSubscription->starts_at)->format('jS M, Y'),
        'endsAt'           => Carbon::parse($currentSubscription->ends_at)->format('jS M, Y'),
        'usedDays'         => $usedDays,
        'remainingDays'    => $remainingDays,
        'totalDays'        => $totalDays,
        'usedBalance'      => $usedBalance,
        'remainingBalance' => $remainingBalance,
        'isExpired'        => $isExpired,
        'currentPlan'      => $currentPlan,
    ];
}

/**
 * Tính toán chi tiết gói đăng ký mới khi nâng cấp/hạ cấp
 * @param $planIDChosenByUser
 * @return array
 */
function getProratedPlanData($planIDChosenByUser)
{
    /** @var Plan $subscriptionPlan */
    $subscriptionPlan = Plan::findOrFail($planIDChosenByUser);

    // Xác định số ngày và tần suất của gói mới
    if ($subscriptionPlan->frequency == Plan::MONTHLY) {
        $newPlanDays = 30;
        $frequency = 'Monthly';
    } else {
        if ($subscriptionPlan->frequency == Plan::YEARLY) {
            $newPlanDays = 365;
            $frequency = 'Yearly';
        } else {
            $newPlanDays = 36500;
            $frequency = 'Unlimited';
        }
    }

    $currentSubscription = getCurrentSubscription();
    $startsAt = Carbon::now();

    // Tính toán thời gian sử dụng của gói hiện tại
    $carbonParseStartAt = Carbon::parse($currentSubscription->starts_at);
    $currentSubsTotalDays = $carbonParseStartAt->diffInDays($currentSubscription->ends_at);
    $usedDays = $carbonParseStartAt->copy()->diffInDays($startsAt);
    $totalExtraDays = 0;
    $totalDays = $newPlanDays;

    $endsAt = Carbon::now()->addDays($newPlanDays);
    $startsAt = $startsAt->copy()->format('jS M, Y');

    if ($usedDays <= 0) {
        $startsAt = $carbonParseStartAt->copy()->format('jS M, Y');
    }

    // Tính toán số tiền và thời gian cho gói mới
    if (!$currentSubscription->isExpired() && !checkIfPlanIsInTrial($currentSubscription)) {
        $amountToPay = 0;
        $currentPlan = $currentSubscription->plan;

        // Kiểm tra giá và tần suất của gói hiện tại
        $planPrice = $currentPlan->price;
        $planFrequency = $currentPlan->frequency;
        if ($planPrice != $currentSubscription->plan_amount || $planFrequency != $currentSubscription->plan_frequency) {
            $planPrice = $currentSubscription->plan_amount;
            $planFrequency = $currentSubscription->plan_frequency;
        }

        // Tính toán số tiền còn lại và số tiền cần trả thêm
        $perDayPrice = round($planPrice / $currentSubsTotalDays, 2);
        $isJPYCurrency = !empty($subscriptionPlan->currency) && isJPYCurrency($subscriptionPlan->currency->currency_code);

        $remainingBalance = $isJPYCurrency
            ? round($planPrice - ($perDayPrice * $usedDays))
            : round($planPrice - ($perDayPrice * $usedDays), 2);

        if ($remainingBalance < $subscriptionPlan->price) {
            $amountToPay = $isJPYCurrency
                ? round($subscriptionPlan->price - $remainingBalance)
                : round($subscriptionPlan->price - $remainingBalance, 2);
        } else {
            $perDayPriceOfNewPlan = round($subscriptionPlan->price / $newPlanDays, 2);
            $totalExtraDays = round($remainingBalance / $perDayPriceOfNewPlan);
            $endsAt = Carbon::now()->addDays($totalExtraDays);
            $totalDays = $totalExtraDays;
        }

        return [
            'startDate'        => $startsAt,
            'name'             => $subscriptionPlan->name.' / '.$frequency,
            'trialDays'        => $subscriptionPlan->trial_days,
            'remainingBalance' => $remainingBalance,
            'endDate'          => $endsAt->format('jS M, Y'),
            'amountToPay'      => $amountToPay,
            'usedDays'         => $usedDays,
            'totalExtraDays'   => $totalExtraDays,
            'totalDays'        => $totalDays,
        ];
    }

    return [
        'name'             => $subscriptionPlan->name.' / '.$frequency,
        'trialDays'        => $subscriptionPlan->trial_days,
        'startDate'        => $startsAt,
        'endDate'          => $endsAt->format('jS M, Y'),
        'remainingBalance' => 0,
        'amountToPay'      => $subscriptionPlan->price,
        'usedDays'         => $usedDays,
        'totalExtraDays'   => $totalExtraDays,
        'totalDays'        => $totalDays,
    ];
}

/**
 * Kiểm tra xem gói đăng ký có đang trong thời gian dùng thử không
 * @param $currentSubscription
 * @return bool
 */
function checkIfPlanIsInTrial($currentSubscription)
{
    $now = Carbon::now();
    if (!empty($currentSubscription->trial_ends_at)) {
        return true;
    }
    return false;
}

/**
 * Kiểm tra xem mã tiền tệ có phải là JPY không
 * @param $code
 * @return bool
 */
function isJPYCurrency($code)
{
    return Currency::JPY_CODE == $code;
}

/**
 * Lấy danh sách cổng thanh toán được kích hoạt
 * @return array
 */
function getPaymentGateway()
{
    $paymentGateway = Subscription::PAYMENT_GATEWAY;
    $selectedPaymentGateways = PaymentGateway::pluck('payment_gateway')->toArray();
    foreach ($selectedPaymentGateways as $key => $gateway) {
        $gateWayKey = array_search($gateway, $paymentGateway, true);

        if (!checkPaymentGateway($gateWayKey)) {
            unset($selectedPaymentGateways[$key]);
        }
    }

    return array_intersect($paymentGateway, $selectedPaymentGateways);
}

/**
 * Danh sách các loại tiền tệ không có số thập phân
 * @return array
 */
function zeroDecimalCurrencies(): array
{
    return [
        'BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF',
    ];
}

/**
 * Thiết lập API key cho Stripe
 */
function setStripeApiKey()
{
    Stripe::setApiKey(config('services.stripe.secret_key'));
}

/**
 * Danh sách các loại tiền tệ được PayPal hỗ trợ
 * @return array
 */
function getPayPalSupportedCurrencies()
{
    return [
        'AUD', 'BRL', 'CAD', 'CNY', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'JPY', 'MYR', 'MXN', 'TWD', 'NZD', 'NOK',
        'PHP', 'PLN', 'GBP', 'RUB', 'SGD', 'SEK', 'CHF', 'THB', 'USD',
    ];
}

/**
 * Lấy thông tin gói đăng ký của người dùng đang đăng nhập
 * @return mixed
 */
function getloginuserplan()
{
    return Subscription::with('plan')->whereUserId(getLogInUserId())->whereStatus(Subscription::ACTIVE)->first();
}

/**
 * Kiểm tra trạng thái cổng thanh toán
 * @param $paymentGateway
 * @return bool
 */
function checkPaymentGateway($paymentGateway): bool
{
    if ($paymentGateway == Plan::STRIPE) {
        if (config('services.stripe.key') && config('services.stripe.secret_key')) {
            return true;
        }
        return false;
    }

    if ($paymentGateway == Plan::PAYPAL) {
        if (config('paypal.mode') == 'sandbox') {
            if (config('paypal.sandbox.client_id') && config('paypal.sandbox.client_secret')) {
                return true;
            }
        }
        if (config('paypal.mode') == 'live') {
            if (config('paypal.live.client_id') && config('paypal.live.client_secret')) {
                return true;
            }
        }
        return false;
    }

    return true;
}

/**
 * Kiểm tra trạng thái thanh toán thủ công
 * @return mixed
 */
function checkManuallyPaymentStatus()
{
    return Subscription::whereUserId(getLogInUserId())->latest()->first();
}

/**
 * Lấy danh mục theo ngôn ngữ
 * @param $langId
 * @return array
 */
function getLanguageCategory($langId)
{
    $category = Category::whereLangId($langId)->pluck('name','id')->toArray();
    return $category;
}

/**
 * Lấy danh mục con theo danh mục cha
 * @param $categoryId
 * @return array
 */
function getCategorySubCategory($categoryId)
{
    $subCategory = SubCategory::whereParentCategoryId($categoryId)->pluck('name','id')->toArray();
    return $subCategory;
}

/**
 * Lấy danh sách ngôn ngữ frontend đang hoạt động
 * @return mixed
 */
function getFrontLanguage()
{
    $language = Language::whereFrontLanguageStatus(Language::ACTIVE)->pluck('name', 'id');
    return $language;
}

/**
 * Lấy tên vai trò của người dùng đang đăng nhập
 * @return mixed
 */
function getLoginUserRole()
{
    return getLogInUser()->role_name;
}

/**
 * Kiểm tra xem người dùng đăng nhập có theo dõi user khác không
 * @param $userId
 * @return mixed
 */
function checkLoginUserFollow($userId)
{
    $following = Followers::whereFollowing(getLogInUserId())->whereFollowers($userId)->first();
    return $following;
}

