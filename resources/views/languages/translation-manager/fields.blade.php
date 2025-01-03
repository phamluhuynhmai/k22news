<div class="row">
    <!-- tạo dropdown để chọn file -->
     <!-- $allFiles là ds file có thể chọn -->
     <!-- $selectedFile là file đã chọn -->
    <div class="col-sm-3 mb-5">
        {{Form::select('file_name', $allFiles,$selectedFile, ['class' => 'form-select form-select-solid translate-language-files','placeholder' => 'Select File', 'id'=>'subFolderFiles']) }}
    </div>
    <!-- tạo button save -->
    <div class="col-sm-3 mb-5 d-flex justify-content-end offset-3 ms-auto">
        {{ Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary me-2','name' => 'save', 'id' => 'saveJob']) }}
    </div>
    <hr><br>
    <!-- tạo form để nhập dữ liệu -->
    @foreach($languages as $key => $value)
        <!-- trường hợp không là mảng   --> 
        @if(!is_array($value))
            <div class="form-group col-lg-4 col-md-6 col-12 mb-5">
                {{ Form::label('title', str_replace('_',' ',ucfirst($key)).':', ['class' => 'form-label']) }}
                {{ Form::text($key, $value, ['class' => 'form-control','required','placeholder' => str_replace('_',' ',ucfirst($key))]) }}
            </div>
        @else
        <!-- trường hợp là mảng lồng nhau --> 
            @foreach($value as $nestedKey => $nestedValue)
                @if(!is_array($nestedValue))
                    <div class="form-group col-lg-4 col-md-6 col-12 mb-4">
                        {{ Form::label('title',  str_replace('_',' ',ucfirst($nestedKey)) .':', ['class' => 'form-label']) }}
                        <input type="text" class="form-control" name="{{$key}}[{{$nestedKey}}]"
                               value="{{ $nestedValue }}" placeholder="{{str_replace('_',' ',ucfirst($nestedKey))}}"/>
                    </div>
                @endif
            @endforeach
        @endif
    @endforeach
</div>
