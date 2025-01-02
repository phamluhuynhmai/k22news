<?php

namespace App\Models;

use Database\Factories\StaffFactory;
use Eloquent as Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class Staff - Lớp quản lý thông tin nhân viên
 *
 * @version 2024-12-30 03:43:34
 *
 * @property string $first_name - Tên
 * @property string $last_name - Họ
 * @property string $email - Địa chỉ email
 * @property string $phone_number - Số điện thoại
 * @property string $password - Mật khẩu
 * @property string $gender - Giới tính
 * @property string $role - Vai trò
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static StaffFactory factory(...$parameters)
 * @method static Builder|Staff newModelQuery()
 * @method static Builder|Staff newQuery()
 * @method static Builder|Staff query()
 * @method static Builder|Staff whereCreatedAt($value)
 * @method static Builder|Staff whereEmail($value)
 * @method static Builder|Staff whereFirstName($value)
 * @method static Builder|Staff whereGender($value)
 * @method static Builder|Staff whereId($value)
 * @method static Builder|Staff whereLastName($value)
 * @method static Builder|Staff wherePassword($value)
 * @method static Builder|Staff wherePhoneNumber($value)
 * @method static Builder|Staff whereUpdatedAt($value)
 * @mixin Model
 *
 * @property-read MediaCollection|Media[]
 *     $media
 * @property-read int|null $media_count
 * @property-read Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read int|null $roles_count
 *
 * @method static Builder|Staff permission($permissions)
 * @method static Builder|Staff role($roles, $guard = null)
 */
class Staff extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasRoles;

    // Tên bảng trong database
    protected $table = 'staff';

    // Các hằng số định nghĩa
    const PROFILE = 'profile';     // Ảnh đại diện
    const COVER_IMG = 'cover_img'; // Ảnh bìa
    const ACTIVE = 1;             // Trạng thái hoạt động
    const DEACTIVE = 0;           // Trạng thái không hoạt động

    // Các trường có thể điền vào
    public $fillable = [
        'first_name',    // Tên
        'last_name',     // Họ
        'email',         // Email
        'phone_number',  // Số điện thoại
        'password',      // Mật khẩu
        'gender',        // Giới tính
        'role',          // Vai trò
        'about_us',      // Thông tin giới thiệu
        'username',      // Tên đăng nhập
    ];

    /**
     * Định nghĩa kiểu dữ liệu cho các trường
     *
     * @var array
     */
    protected $casts = [
        'first_name' => 'string',
        'last_name' => 'string',
        'email' => 'string',
        'phone_number' => 'string',
        'password' => 'string',
        'gender' => 'string',
        'role' => 'string',
    ];

    /**
     * Các quy tắc xác thực dữ liệu
     *
     * @var array
     */
    public static $rules = [
        'first_name' => 'required|max:190',    // Tên: bắt buộc, tối đa 190 ký tự
        'last_name' => 'required|max:190',     // Họ: bắt buộc, tối đa 190 ký tự
        'email' => 'required|email|unique:users,email', // Email: bắt buộc, phải là email hợp lệ và không trùng lặp
        'password' => 'required|same:password_confirmation|min:6|max:190', // Mật khẩu: bắt buộc, giống với xác nhận mật khẩu, 6-190 ký tự
        'contact' => 'required',               // Liên hệ: bắt buộc
        'gender' => 'required',                // Giới tính: bắt buộc
        'role' => 'required',                  // Vai trò: bắt buộc
    ];
}
