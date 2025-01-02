<?php



namespace App\Http\Livewire;

use App\Models\Post;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class FrontUserTable extends SearchableComponent
{
    public $user;
    
    protected $listeners = ['refresh' => '$refresh', 'resetPage'];

    public $paginationTheme = 'bootstrap';

    
    /**
     * @var mixed
     */
    public function mount($user = null)
    {
      
        $this->user = $user;
    }

    /**
     * @return  Application|Factory|View
     */
    public function render()
    {
        $posts = $this->postsData();

        return view('livewire.front-user', compact('posts'));
    }

    /**
     * @return LengthAwarePaginator
     */
    public function postsData()
    {
        $this->getQuery()->where('created_by', $this->user)->whereVisibility(Post::VISIBILITY_ACTIVE);

        return $this->paginate();
    }

    public function model()
    {
        return Post::class;
    }

    public function searchableFields()
    {
        return ['category_id'];
    }
}
