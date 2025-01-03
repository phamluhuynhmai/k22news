'use strict';
let source = null;
let jsrender = require('jsrender');
import 'flatpickr/dist/l10n'

// Khởi tạo các components khi trang được load
document.addEventListener('turbo:load', function () {
    IOInitSidebar();
    IOInitImageComponent();
    tooltip()
    select2initialize();
    inputFocus();
    // Xóa instance cũ của tinymce nếu tồn tại
    if (tinymce && $('textarea').length){
        tinymce.remove();
    }
    // Tự động ẩn alert sau 5 giây
    $('.alert').delay(5000).slideUp(300);
    
    // Thay đổi theme của CKEditor trong dark mode
    if (darkMode == 1){
        CKEDITOR.config.skin = 'moono-dark';
        CKEDITOR.addCss('.cke_editable { background-color: #14151F; color: white; border-color: black; }');
    }
});

// Khởi tạo tooltips bootstrap
function tooltip() {
    var tooltipTriggerList =
        [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
}

// Cấu hình mặc định cho các Ajax request
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    },
});

// Khởi tạo lại tooltips sau mỗi ajax request
$(document).ajaxComplete(function () {
    $('[data-toggle="tooltip"]').tooltip({
        'html': true,
        'offset': 10,
    });
});

// Xử lý sự kiện thay đổi sắp xếp dữ liệu
listenChange('.data-sorting',function (){
    window.livewire.emit('resetPageTable')
})

// Tự động focus vào input đầu tiên
const inputFocus = () => {
    $('input:text:not([readonly="readonly"]):not([name="search"]):not(.front-input)').first().focus();
}

// Focus vào input đầu tiên khi mở modal
$(function () {
    $(document).on('shown.bs.modal','.modal', function () {
        if ($(this).find('input:text')[0]){
            $(this).find('input:text')[0].focus();
        }
    });
});

// Cấu hình mặc định cho toastr notifications
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000", 
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

// Reset form modal
window.resetModalForm = function (formId, validationBox) {
    $(formId)[0].reset();
    $('select.select2Selector').each(function (index, element) {
        let drpSelector = '#' + $(this).attr('id');
        $(drpSelector).val('');
        $(drpSelector).trigger('change');
    });
    $(validationBox).hide();
};

// Hiển thị thông báo lỗi
window.printErrorMessage = function (selector, errorResult) {
    $(selector).show().html('');
    $(selector).text(errorResult.responseJSON.message);
};

// Xử lý lỗi Ajax
window.manageAjaxErrors = function (data) {
    var errorDivId = arguments.length > 1 && arguments[1] !== undefined
        ? arguments[1]
        : 'editValidationErrorsBox';
    if (data.status == 404) {
        toastr.error(data.responseJSON.message);
    } else {
        printErrorMessage('#' + errorDivId, data);
    }
};

// Hiển thị thông báo thành công
window.displaySuccessMessage = function (message) {
    toastr.success(message);
};

// Hiển thị thông báo lỗi
window.displayErrorMessage = function (message) {
    toastr.error(message);
};

// Xử lý sự kiện thành công
document.addEventListener('success', function (data){
    displaySuccessMessage(data.detail)
})

// Xác nhận xóa item
window.deleteItem = function (url, header) {
    var callFunction = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;
    swal({
        title: Lang.get('messages.delete'),
        text: Lang.get('messages.common.delete_warning') + ' "' + header + '"' + ' ?',
        buttons: {
            confirm:Lang.get('messages.common.delete'),
            cancel: Lang.get('messages.common.cancel_delete'),
        },
        reverseButtons: true,
        icon: 'warning',
    }).then(function (willDelete) {
        if(willDelete){
            deleteItemAjax(url, header, callFunction);
        }
    });
};

// Gọi API xóa item
function deleteItemAjax (url, header, callFunction = null) {
    $.ajax({
        url: url,
        type: 'DELETE',
        dataType: 'json',
        success: function (obj) {
            if (obj.success) {
                window.livewire.emit('refresh')
                window.livewire.emit('resetPage')
            }
            swal({
                icon: 'success',
                title: Lang.get('messages.common.deleted'),
                text: header + ' ' + Lang.get('messages.common.delete_message'),
                buttons: {
                    confirm:Lang.get('messages.ok'),
                },
                timer: 2000,
            })
            if (callFunction) {
                eval(callFunction);
            }
        },
        error: function (data) {
            swal({
                title: 'Error',
                icon: 'error',
                text: data.responseJSON.message,
                type: 'error',
                timer: 4000,
            });
        },
    });
}

// Khởi tạo select2
function select2initialize() {
    $('[data-control=select2]').each(function () {
        $(this).select2();
    });
}

// Xử lý trước khi cache
document.addEventListener('turbo:before-cache', function () {
    let currentSelect2 = '.select2-hidden-accessible';
    $(currentSelect2).each(function () {
        $(this).select2('destroy');
    });

    $(currentSelect2).each(function () {
        $(this).select2();
    });

    $('.toast').addClass('d-none');
});

// Format date time
window.format = function (dateTime) {
    var format = arguments.length > 1 && arguments[1] !== undefined
        ? arguments[1]
        : 'DD-MMM-YYYY';
    return moment(dateTime).format(format);
};

// Xử lý nút loading
window.processingBtn = function (selecter, btnId, state = null) {
    var loadingButton = $(selecter).find(btnId);
    if (state === 'loading') {
        loadingButton.button('loading');
    } else {
        loadingButton.button('reset');
    }
};

// Render template
window.prepareTemplateRender = function (templateSelector, data) {
    let template = jsrender.templates(templateSelector);
    return template.render(data);
};

// Kiểm tra file hợp lệ
window.isValidFile = function (inputSelector, validationMessageSelector) {
    let ext = $(inputSelector).val().split('.').pop().toLowerCase();
    if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
        $(inputSelector).val('');
        $(validationMessageSelector).removeClass('d-none');
        $(validationMessageSelector).html(Lang.get('messages.common.allowed_types')).show();
        $(validationMessageSelector).delay(5000).slideUp(300);

        return false;
    }
    $(validationMessageSelector).hide();
    return true;
};

// Hiển thị ảnh preview
window.displayPhoto = function (input, selector) {
    let displayPreview = true;
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function (e) {
            let image = new Image();
            image.src = e.target.result;
            image.onload = function () {
                $(selector).attr('src', e.target.result);
                displayPreview = true;
            };
        };
        if (displayPreview) {
            reader.readAsDataURL(input.files[0]);
            $(selector).show();
        }
    }
};

// Xóa dấu phẩy trong chuỗi
window.removeCommas = function (str) {
    return str.replace(/,/g, '');
};

// Cấu hình mặc định cho datetime picker
window.DatetimepickerDefaults = function (opts) {
    return $.extend({}, {
        sideBySide: true,
        ignoreReadonly: true,
        icons: {
            close: 'fa fa-times',
            time: 'fa fa-clock-o',
            date: 'fa fa-calendar',
            up: 'fa fa-arrow-up',
            down: 'fa fa-arrow-down',
            previous: 'fa fa-chevron-left',
            next: 'fa fa-chevron-right',
            today: 'fa fa-clock-o',
            clear: 'fa fa-trash-o',
        },
    }, opts);
};

// Kiểm tra giá trị rỗng
window.isEmpty = (value) => {
    return value === undefined || value === null || value === '';
};

// Khóa màn hình
window.screenLock = function () {
    $('#overlay-screen-lock').show();
    $('body').css({ 'pointer-events': 'none', 'opacity': '0.6' });
};

// Mở khóa màn hình
window.screenUnLock = function () {
    $('body').css({ 'pointer-events': 'auto', 'opacity': '1' });
    $('#overlay-screen-lock').hide();
};

// Xử lý loading khi tải trang
window.onload = function () {
    window.startLoader = function () {
        $('.k22-loader').show();
    };

    window.stopLoader = function () {
        $('.k22-loader').hide();
    };

    stopLoader();
};

// Xử lý active menu
$(document).ready(function () {
    let hasActiveMenu = $(document).
    find('.nav-item.dropdown ul li').
    hasClass('active');
    if (hasActiveMenu) {
        $(document).
        find('.nav-item.dropdown ul li.active').
        parent('ul').
        css('display', 'block');
        $(document).
        find('.nav-item.dropdown ul li.active').
        parent('ul').
        parent('li').
        addClass('active');
    }
});

// Kiểm tra URL hợp lệ
window.urlValidation = function (value, regex) {
    let urlCheck = (value == '' ? true : (value.match(regex)
        ? true
        : false));
    if (!urlCheck) {
        return false;
    }

    return true;
};

// Xử lý chọn ngôn ngữ
if ($('.languageSelection').length) {
    listen('click', '.languageSelection', function () {
        let languageName = $(this).data('prefix-value');

        $.ajax({
            type: 'POST',
            url: '/change-language',
            data: {languageName: languageName},
            success: function () {
                location.reload();
            },
        });
    });
}

// Xử lý hover menu trên desktop
if ($(window).width() > 992) {
    $('.no-hover').on('click', function () {
        $(this).toggleClass('open');
    });
}

// Xử lý đọc thông báo
if ($('#readNotification').length) {
    listen('click', '#readNotification', function (e) {
        e.preventDefault();
        let notificationId = $(this).data('id');
        let notification = $(this);
        $.ajax({
            type: 'POST',
            url: readNotification + '/' + notificationId + '/read',
            data: {notificationId: notificationId},
            success: function () {
                notification.remove();
                let notificationCounter = document.getElementsByClassName(
                    'readNotification').length;
                if (notificationCounter == 0) {
                    $('#readAllNotification').addClass('d-none');
                    $('.empty-state').removeClass('d-none');
                    $('.notification-toggle').removeClass('beep');
                }
            },
            error: function (error) {
                manageAjaxErrors(error);
            },
        });
    });
}

// Xử lý đọc tất cả thông báo
if ($('#readAllNotification').length) {
    listen('click', '#readAllNotification', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: readAllNotifications,
            success: function () {
                $('.readNotification').remove();
                $('#readAllNotification').addClass('d-none');
                $('.empty-state').removeClass('d-none');
                $('.notification-toggle').removeClass('beep');
            },
            error: function (error) {
                manageAjaxErrors(error);
            },
        });
    });
}

// Xử lý đăng ký
if ($('#register').length) {
    listen('click','#register', function (e) {
        e.preventDefault();
        $('.open #dropdownLanguage').trigger('click');
        $('.open #dropdownLogin').trigger('click');
    });
}

// Xử lý ngôn ngữ
if ($('#language').length) {
    listen('click','#language', function (e) {
        e.preventDefault();
        $('.open #dropdownRegister').trigger('click');
        $('.open #dropdownLogin').trigger('click');
    });
}

// Xử lý đăng nhập
if ($('#login').length) {
    listen('click','#login', function (e) {
        e.preventDefault();
        $('.open #dropdownRegister').trigger('click');
        $('.open #dropdownLanguage').trigger('click');
    });
}

// Kiểm tra nội dung summernote
window.checkSummerNoteEmpty = function (
    selectorElement, errorMessage, isRequired = 0) {
    if ($(selectorElement).summernote('isEmpty') && isRequired === 1) {
        displayErrorMessage(errorMessage);
        $(document).find('.note-editable').html('<p><br></p>');
        return false;
    } else if (!$(selectorElement).summernote('isEmpty')) {
        $(document).find('.note-editable').contents().each(function () {
            if (this.nodeType === 3) { // text node
                this.textContent = this.textContent.replace(/\u00A0/g, '');
            }
        });
        if ($(document).find('.note-editable').text().trim().length == 0) {
            $(document).find('.note-editable').html('<p><br></p>');
            $(selectorElement).val(null);
            if (isRequired === 1) {
                displayErrorMessage(errorMessage);

                return false;
            }
        }
    }

    return true;
};

// Chuẩn bị template
window.preparedTemplate = function () {
    let source = $('#actionTemplate').html();
    window.preparedTemplate = Handlebars.compile(source);
};

// Đánh dấu ajax đang chạy
window.ajaxCallInProgress = function () {
    ajaxCallIsRunning = true;
};

// Đánh dấu ajax đã hoàn thành
window.ajaxCallCompleted = function () {
    ajaxCallIsRunning = false
}

// Tránh nhập khoảng trắng
window.avoidSpace = function (event) {
    let k = event ? event.which : window.event.keyCode
    if (k == 32) {
        return false
    }
}

// Xử lý tải ảnh
window.imageLoad = function () {
    var KTImageInput = function (e, t) {
        var n = this
        if (null != e) {
            var i = {}, r = function () {
                n.options = KTUtil.deepExtend({}, i,
                    t), n.uid = KTUtil.getUniqueId(
                    'image-input'), n.element = e, n.inputElement = KTUtil.find(
                    e,
                    'input[type="file"]'), n.wrapperElement = KTUtil.find(e,
                    '.image-input-wrapper'), n.cancelElement = KTUtil.find(e,
                    '[data-kt-image-input-action="cancel"]'), n.removeElement = KTUtil.find(
                    e,
                    '[data-kt-image-input-action="remove"]'), n.hiddenElement = KTUtil.find(
                    e, 'input[type="hidden"]'), n.src = KTUtil.css(
                    n.wrapperElement,
                    'backgroundImage'), n.element.setAttribute(
                    'data-kt-image-input', 'true'), o(), KTUtil.data(n.element).
                    set('image-input', n)
            }, o = function () {
                KTUtil.addEvent(n.inputElement, 'change', a), KTUtil.addEvent(
                    n.cancelElement, 'click', l), KTUtil.addEvent(
                    n.removeElement,
                    'click', s)
            }, a = function (e) {
                if (e.preventDefault(), null !== n.inputElement &&
                n.inputElement.files && n.inputElement.files[0]) {
                    if (!1 ===
                        KTEventHandler.trigger(n.element,
                            'kt.imageinput.change',
                            n)) return
                    var t = new FileReader
                    t.onload = function (e) {
                        KTUtil.css(n.wrapperElement, 'background-image',
                            'url(' + e.target.result + ')')
                    }, t.readAsDataURL(
                        n.inputElement.files[0]), KTUtil.addClass(
                        n.element, 'image-input-changed'), KTUtil.removeClass(
                        n.element, 'image-input-empty'), KTEventHandler.trigger(
                        n.element, 'kt.imageinput.changed', n)
                }
            }, l = function (e) {
                e.preventDefault(), !1 !==
                KTEventHandler.trigger(n.element, 'kt.imageinput.cancel', n) &&
                (KTUtil.removeClass(n.element,
                    'image-input-changed'), KTUtil.removeClass(n.element,
                    'image-input-empty'), KTUtil.css(n.wrapperElement,
                    'background-image',
                    n.src), n.inputElement.value = '', null !==
                n.hiddenElement &&
                (n.hiddenElement.value = '0'), KTEventHandler.trigger(n.element,
                    'kt.imageinput.canceled', n))
            }, s = function (e) {
                e.preventDefault(), !1 !==
                KTEventHandler.trigger(n.element, 'kt.imageinput.remove', n) &&
                (KTUtil.removeClass(n.element,
                    'image-input-changed'), KTUtil.addClass(n.element,
                    'image-input-empty'), KTUtil.css(n.wrapperElement,
                    'background-image',
                    'none'), n.inputElement.value = '', null !==
                n.hiddenElement &&
                (n.hiddenElement.value = '1'), KTEventHandler.trigger(n.element,
                    'kt.imageinput.removed', n))
            }
            !0 === KTUtil.data(e).has('image-input')
                ? n = KTUtil.data(e).
                    get('image-input')
                : r(), n.getInputElement = function () {return n.inputElement}, n.goElement = function () {return n.element}, n.on = function (
                e, t) {
                return KTEventHandler.on(n.element, e, t)
            }, n.one = function (e, t) {
                return KTEventHandler.one(n.element, e, t)
            }, n.off = function (e) {
                return KTEventHandler.off(n.element, e)
            }, n.trigger = function (e, t) {
                return KTEventHandler.trigger(n.element, e, t, n, t)
            }
        }
    }
    KTImageInput.getInstance = function (e) {
        return null !== e && KTUtil.data(e).has('image-input') ? KTUtil.data(e).
            get('image-input') : null
    }, KTImageInput.createInstances = function (e) {
        var t = document.querySelectorAll(e)
        if (t && t.length > 0) for (var n = 0, i = t.length; n <
        i; n++) new KTImageInput(t[n])
    }, KTImageInput.init = function () {
        KTImageInput.createInstances('[data-kt-image-input]')
    }, 'loading' === document.readyState
        ? document.addEventListener(
            'DOMContentLoaded', KTImageInput.init)
        : KTImageInput.init(), 'undefined' !=
    typeof module && void 0 !== module.exports &&
    (module.exports = KTImageInput)
}

// Xử lý tooltip
window.tooltipLoad = function () {
    var tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
}

// Xử lý thay đổi ngôn ngữ backend
$(document).on('click', '.backendLanguage', function (event) {
    let languageId = $(event.currentTarget).data('id');
    $.ajax({
        url: route('change-Language', languageId),
        type: 'GET',
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                location.reload();
            }
        }
    })
});

// Xử lý hiển thị/ẩn mật khẩu
$(document).on('click', '.change-type', function (e) {
    let inputField = $(this).siblings()
    let oldType = inputField.attr('type')
    let type = !isEmpty(oldType) === 'password' ? oldType : 'password'

    if (type == 'password') {
        $(this).children().addClass('fa-eye')
        $(this).children().removeClass('fa-eye-slash')
        inputField.attr('type', 'text')
    } else {
        $(this).children().removeClass('fa-eye')
        $(this).children().addClass('fa-eye-slash')
        inputField.attr('type', 'password')
    }
})

// Xử lý focus select2
listen('focus', '.select2.select2-container', function (e) {
    let isOriginalEvent = e.originalEvent 
    let isSingleSelect = $(this).find('.select2-selection--single').length > 0

    if (isOriginalEvent && isSingleSelect) {
        $(this).siblings('select:enabled').select2('open')
    }
})

// Focus vào ô tìm kiếm select2
listen('select2:open', () => {
    let allFound = document.querySelectorAll(
        '.select2-container--open .select2-search__field')
    allFound[allFound.length - 1].focus()
})

// Xử lý dark mode
listen('click','.apply-dark-mode', function (e) {
    e.preventDefault()
    $.ajax({
        url: route('update-dark-mode'),
        type: 'get',
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message)
                setTimeout(function () {
                    location.reload();
                }, 500);
            }
        }, error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
    })
})

// Xử lý menu collapse
jQuery(document).ready(function($) {
    $(".aside-item-collapse > ul").hide();
    $(document).on('click',".aside-collapse-btn", function() {
        $(this).parent().toggleClass('collapse-submenu').siblings().removeClass('collapse-submenu');
        var $menuItem = $(this).next('.aside-submenu');
        $menuItem.stop(true, true).slideToggle("slow");
        $('.aside-submenu').not($menuItem).slideUp();
    });

    $('.aside-submenu').each(function() {
        var $collapseMenu = $(this);
        $(this).find('li').each(function() {
            if ($collapseMenu.parent().hasClass('show')){
                $collapseMenu.show();
                $collapseMenu.parents('li').addClass('active collapse-submenu');
            }
        });
    });
});
