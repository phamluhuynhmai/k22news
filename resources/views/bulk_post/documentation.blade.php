<div class="modal-content">
    <div class="modal-header">
        <h3 class="modal-title" id="categoriesExampleModalLabel">{{ __('messages.bulk_post.documentation') }}</h3>
        <button type="button" class="btn-close" data-bs-dismiss="modal"
                aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <table class="table">
            <tr>
                <th>{{__('messages.bulk_post.field')}}</th>
                <th>{{__('messages.post.description')}}</th>
            </tr>
            <tr>
                <td>title</td>
                <td>
                    {{__('messages.bulk_post.data_type')}}: {{__('messages.bulk_post.string')}}
                    <br>
                    <strong>{{__('messages.required')}}</strong>
                    <br>
                    <span>{{__('messages.bulk_post.example')}}: Test Title</span>
                </td>
            </tr>
            <tr>
                <td>description</td>
                <td>
                    {{__('messages.bulk_post.data_type')}}: {{__('messages.bulk_post.longText')}}
                    <br>
                    <strong>{{__('messages.required')}}</strong>
                    <br>
                    <span>{{__('messages.bulk_post.example')}}:Test description About this post</span>
                </td>
            </tr> 
            <tr>
                <td>keywords</td>
                <td>
                    {{__('messages.bulk_post.data_type')}}: {{__('messages.bulk_post.string')}}
                    <br>
                    <strong>{{__('messages.required')}}</strong>
                    <br>
                    <span>{{__('messages.bulk_post.example')}}: examination, careful, goals</span>
                </td>
            </tr>
            <tr>
                <td>image</td>
                <td>
                    {{__('messages.bulk_post.data_type')}}: {{__('messages.bulk_post.string')}}
                    <br>
                    <strong>{{__('messages.required')}}</strong>
                    <br>
                    <strong>{{__('messages.bulk_post.the_images_must_be_a_file_of_type_article_post')}}</strong>
                    <br>
                    <span>{{__('messages.bulk_post.example')}}: </span>
                </td>
            </tr>
            <tr>
                <td>lang_id</td>
                <td>
                    {{__('messages.bulk_post.data_type')}}: {{__('messages.bulk_post.integer')}}
                    <br>
                    <strong>{{__('messages.required')}}</strong>
                    <br>
                    <span>{{__('messages.bulk_post.example')}}: 1 </span>
                </td>
            </tr>
            <tr>
                <td>category_id</td>
                <td>
                    {{__('messages.bulk_post.data_type')}}: {{__('messages.bulk_post.integer')}}
                    <br>
                    <strong>{{__('messages.required')}}</strong>
                    <br>
                    <span>{{__('messages.bulk_post.example')}}: 1 </span>
                </td>
            </tr> 
            <tr>
                <td>sub_category_id</td>
                <td>
                    {{__('messages.bulk_post.data_type')}}: {{__('messages.bulk_post.integer')}}
                    <br>
                    <strong>Optional</strong>
                    <br>
                    <span>{{__('messages.bulk_post.example')}}: 1 </span>
                </td>
            </tr>
            <tr>
                <td>tags</td>
                <td>
                    {{__('messages.bulk_post.data_type')}}: {{__('messages.bulk_post.string')}}
                    <br>
                    <strong>{{__('messages.required')}}</strong>
                    <br>
                    <span>{{__('messages.bulk_post.example')}}:advantages,power</span>
                </td>
            </tr>
            <tr>
                <td>visibility</td>
                <td>
                    {{__('messages.bulk_post.data_type')}}:  {{__('messages.bulk_post.boolean')}}
                    <br>
                    <strong>{{__('messages.required')}}</strong>
                    <br>
                    <span>1 {{__('messages.bulk_post.or')}} 0</span>
                </td>
            </tr>
        </table>
    </div>
</div>
