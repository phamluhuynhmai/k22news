'use strict';

// Thêm sự kiện lắng nghe khi trang được tải
document.addEventListener('turbo:load', loadCommentData)

// Hàm xử lý các chức năng liên quan đến bình luận
function loadCommentData() {
    let comments = 'comment'
    // Lắng nghe sự kiện click vào nút xóa bình luận
    listen('click', '.comment-delete-btn', function (event) {
        // Lấy ID của bình luận cần xóa
        let deleteCommentId = $(event.currentTarget).data('id');
        // Lấy vai trò của người dùng đang đăng nhập
        let role = $('#loginUserRole').val()
        let url
        // Kiểm tra vai trò để xác định đường dẫn URL phù hợp
        if (role) {
            url = route('customer.post-comments.destroy',deleteCommentId)
        } else {
            url = route('post-comments.destroy',deleteCommentId)
        }
        // Gọi hàm xóa bình luận
        deleteItem(url,  Lang.get('messages.comment.comment'));
    });
};

// Xử lý sự kiện thay đổi trạng thái bình luận
$(document).on('change', '.set-comment-btn', function (e) {
    // Lấy vai trò của người dùng đang đăng nhập
    let role = $('#loginUserRole').val()
    // Xác định trạng thái mới (0: kích hoạt, 1: vô hiệu hóa)
    const status = ($(this).prop('checked')) ? 0 : 1;
    let url
    // Kiểm tra vai trò để xác định đường dẫn URL phù hợp
    if(role){
        url = route('customer.comment-status', status)
    }else{
        url = route('admin.comment-status',status)
    }
    // Gửi yêu cầu AJAX để cập nhật trạng thái
    $.ajax({
        url: url,
        type: 'GET',
        success: function (result) {
            // Hiển thị thông báo thành công nếu cập nhật thành công
            if (result.success) {
                displaySuccessMessage(result.message);
            }
        }
    });
});

