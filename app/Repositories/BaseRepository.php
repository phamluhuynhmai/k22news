<?php

namespace App\Repositories;

use Exception;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseRepository
 * Lớp trừu tượng cơ sở cho tất cả các Repository
 * Cung cấp các phương thức CRUD cơ bản và xử lý truy vấn
 */
abstract class BaseRepository
{
    /**
     * Model instance
     * @var Model
     */
    protected $model;

    /**
     * Application container instance
     * @var Application
     */
    protected $app;

    /**
     * @param  Application  $app
     *
     * @throws \Exception
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->makeModel();
    }

    /**
     * Định nghĩa các trường có thể tìm kiếm được
     * Cần được implement bởi các lớp con
     * @return array
     */
    abstract public function getFieldsSearchable();

    /**
     * Định nghĩa Model được sử dụng
     * Cần được implement bởi các lớp con
     * @return string
     */
    abstract public function model();

    /**
     * Khởi tạo instance của Model
     * @throws Exception nếu class không phải là instance của Eloquent Model
     */
    public function makeModel()
    {
        $model = $this->app->make($this->model());

        if (! $model instanceof Model) {
            throw new \Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    /**
     * Phân trang dữ liệu
     * @param int $perPage Số bản ghi trên mỗi trang
     * @param array $columns Các cột cần lấy
     */
    public function paginate($perPage, $columns = ['*'])
    {
        $query = $this->allQuery();

        return $query->paginate($perPage, $columns);
    }

    /**
     * Xây dựng query để lấy tất cả bản ghi
     * @param array $search Điều kiện tìm kiếm
     * @param int|null $skip Số bản ghi bỏ qua
     * @param int|null $limit Giới hạn số bản ghi
     */
    public function allQuery($search = [], $skip = null, $limit = null)
    {
        $query = $this->model->newQuery();

        if (count($search)) {
            foreach ($search as $key => $value) {
                if (in_array($key, $this->getFieldsSearchable())) {
                    $query->where($key, $value);
                }
            }
        }

        if (! is_null($skip)) {
            $query->skip($skip);
        }

        if (! is_null($limit)) {
            $query->limit($limit);
        }

        return $query;
    }

    /**
     * Lấy tất cả bản ghi theo điều kiện
     * @param array $search Điều kiện tìm kiếm
     * @param int|null $skip Số bản ghi bỏ qua
     * @param int|null $limit Giới hạn số bản ghi
     * @param array $columns Các cột cần lấy
     */
    public function all($search = [], $skip = null, $limit = null, $columns = ['*'])
    {
        $query = $this->allQuery($search, $skip, $limit);

        return $query->get($columns);
    }

    /**
     * Tạo mới một bản ghi
     * @param array $input Dữ liệu đầu vào
     * @return Model
     */
    public function create($input)
    {
        $model = $this->model->newInstance($input);

        $model->save();

        return $model;
    }

    /**
     * Tìm bản ghi theo ID
     * @param int $id ID cần tìm
     * @param array $columns Các cột cần lấy
     */
    public function find($id, $columns = ['*'])
    {
        $query = $this->model->newQuery();

        return $query->find($id, $columns);
    }

    /**
     * Cập nhật bản ghi
     * @param array $input Dữ liệu cập nhật
     * @param int $id ID bản ghi cần cập nhật
     * @throws ModelNotFoundException nếu không tìm thấy bản ghi
     */
    public function update($input, $id)
    {
        $query = $this->model->newQuery();

        $model = $query->findOrFail($id);

        $model->fill($input);

        $model->save();

        return $model;
    }

    /**
     * Xóa bản ghi
     * @param int $id ID bản ghi cần xóa
     * @throws ModelNotFoundException nếu không tìm thấy bản ghi
     * @throws Exception
     */
    public function delete($id)
    {
        $query = $this->model->newQuery();

        $model = $query->findOrFail($id);

        return $model->delete();
    }

    /**
     * Tìm bản ghi theo ID nhưng không ném ra exception
     * @param int $id ID cần tìm
     * @param array $columns Các cột cần lấy
     * @return mixed|null Trả về null nếu không tìm thấy
     */
    public function findWithoutFail($id, $columns = ['*'])
    {
        try {
            return $this->find($id, $columns);
        } catch (Exception $e) {
            return;
        }
    }
}
