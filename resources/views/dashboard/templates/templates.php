<script id="adminDashboardTemplate" type="text/x-jsrender">

<tr>
    <td>
        <!-- hiển thị avt -->
        <div class="symbol symbol-45px me-2">
            <img src="{{:image}}" class="h-50 align-self-center" alt="">
        </div>
    </td>
    <td>
        <!-- hiển thị tên -->
        <a href="#" class="text-dark fw-bolder text-hover-primary mb-1 fs-6">{{:name}}</a>
        <span class="text-muted fw-bold d-block">{{:email}}</span>
    </td>
    <td class="text-start">
        <!-- hiển thị id -->
        <span class="badge badge-light-success">{{:patientId}}</span>
    </td>
    <td class="text-start text-muted fw-bold">
        <!-- hiển thị ngày đăng ký -->
        {{:registered}}
    </td>
</tr>



</script>

<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$('form').on('submit', function(e) {
    e.preventDefault();
    
    // Get CKEditor content
    var editorContent = CKEDITOR.instances.editor.getData();
    
    var formData = $(this).serializeArray();
    formData.push({
        name: 'content',
        value: editorContent
    });
    
    $.ajax({
        url: '/admin/post-type',
        method: 'POST',
        data: $.param(formData),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            // Handle success
            console.log('Success:', response);
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                var errors = xhr.responseJSON.errors;
                $.each(errors, function(key, value) {
                    $('#' + key + '_error').text(value[0]);
                });
            }
            console.error('Error:', xhr);
        }
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    try {
        CKEDITOR.replace('editor', {
            // Add any configuration options you need
            height: 300,
            removePlugins: 'resize',
            // Wait for the DOM to be fully loaded
            on: {
                instanceReady: function(evt) {
                    var editor = evt.editor;
                    // Ensure the container is fully rendered
                    setTimeout(function() {
                        editor.resize('100%', editor.container.$.clientHeight);
                    }, 100);
                }
            }
        });
    } catch (e) {
        console.error('CKEditor initialization error:', e);
    }
});

// Destroy CKEditor instance before removing from DOM
window.addEventListener('beforeunload', function() {
    for (var instanceName in CKEDITOR.instances) {
        CKEDITOR.instances[instanceName].destroy();
    }
});
</script>

<div class="editor-container">
    <textarea id="editor" name="content"></textarea>
</div>

<style>
.editor-container {
    min-height: 300px;
    width: 100%;
    margin-bottom: 20px;
}
</style>

<script>
CKEDITOR.timestamp = new Date().getTime();
</script>
