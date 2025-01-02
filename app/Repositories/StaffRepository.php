<?php

namespace App\Repositories;

use App\Models\Plan;
use App\Models\Role;
use App\Models\Staff;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Quản lý các thao tác liên quan đến nhân viên
 *
 * @version 
 */
class StaffRepository extends BaseRepository
{
    /**
     * Danh sách các trường có thể tìm kiếm
     * @var array
     */
    protected $fieldSearchable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'password',
        'gender',
        'role',
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Staff::class;
    }

    /**
     * Lấy danh sách vai trò để hiển thị trong form
     * @return mixed Collection của roles với key là id và value là display_name
     */
    public function getRole()
    {
        $roles = Role::pluck('display_name', 'id');

        return $roles;
    }

    /**
     * Tạo mới một nhân viên trong hệ thống
     * @param array $input Dữ liệu đầu vào từ form
     * @return bool
     */
    public function store($input)
    {
        try {
            DB::beginTransaction();

            $input['password'] = Hash::make($input['password']);
            $input['status'] = ! empty($input['status']) ? Staff::ACTIVE : Staff::DEACTIVE;
            $input['type'] = User::STAFF;
            $staff = User::create($input);

            if (isset($input['role']) && ! empty($input['role'])) {
                $staff->assignRole($input['role']);
            }
            if($staff->hasRole('customer')){
                $plan = Plan::whereIsDefault(true)->first();
                Subscription::create([
                    'plan_id'        => $plan->id,
                    'plan_amount'    => $plan->price,
                    'plan_frequency' => Plan::MONTHLY,
                    'starts_at'      => Carbon::now(),
                    'ends_at'        => Carbon::now()->addDays($plan->trial_days),
                    'trial_ends_at'  => Carbon::now()->addDays($plan->trial_days),
                    'status'         => Subscription::ACTIVE,
                    'user_id'        => $staff->id,
                    'no_of_post'     => $plan->post_count,
                ]);
            }
            if (isset($input['profile']) && ! empty($input['profile'])) {
                $staff->addMedia($input['profile'])->toMediaCollection(Staff::PROFILE);
            }
            if (isset($input['cover_image']) && ! empty($input['cover_image'])) {
                $staff->addMedia($input['cover_image'])->toMediaCollection(Staff::COVER_IMG);
            }
            $staff->sendEmailVerificationNotification();

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * Cập nhật thông tin nhân viên
     * @param array $input Dữ liệu cập nhật từ form
     * @param int $id ID của nhân viên
     * @return bool
     */
    public function update($input, $id)
    {
        try {
            DB::beginTransaction();

            $staff = User::find($id);
            $input['password'] = $staff->password;
            if (isset($input['password']) && ! empty($input['password'])) {
                $input['password'] = Hash::make($input['password']);
            }

            $input['type'] = User::STAFF;
            $staff->update($input);
            if (isset($input['role']) && ! empty($input['role'])) {
                $staff->syncRoles($input['role']);
            }

            if (isset($input['profile']) && ! empty($input['profile'])) {
                $staff->clearMediaCollection(Staff::PROFILE);
                $staff->addMedia($input['profile'])->toMediaCollection(Staff::PROFILE);
            }
            if (isset($input['cover_image']) && ! empty($input['cover_image'])) {
                $staff->clearMediaCollection(Staff::COVER_IMG);
                $staff->addMedia($input['cover_image'])->toMediaCollection(Staff::COVER_IMG);
            }
            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * Gửi lại email xác thực cho người dùng
     * @param int $id ID của người dùng
     * @return bool
     */
    public function resendEmail($id)
    {
        $user = User::whereId($id)->first();
        $user->sendEmailVerificationNotification();

        return true;
    }
}
