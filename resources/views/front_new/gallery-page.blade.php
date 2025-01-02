@extends('front_new.layouts.app')
@section('title')
    {{__('messages.post.gallery') }} {{-- Tiêu đề trang: Gallery --}}
@endsection
@section('pageCss')
    {{-- Thêm file CSS riêng cho trang gallery --}}
    <link href="{{asset('front_web/build/scss/gallery.css')}}" rel="stylesheet" type="text/css">
@endsection
@section('content')
<div class="gallery-page">
    <!-- Bắt đầu phần gallery -->
    <section class="gallery-section py-60">
        <div class="container">
            {{-- Phần tiêu đề của trang --}}
            <div class="section-heading border-bottom-0">
                <div class="row align-items-center">
                    <div class="col-md-6 section-heading-left">
                        <h2 class="text-black mb-0">{{__('messages.post.gallery') }}</h2>
                    </div>
                </div>
            </div>
            {{-- Phần hiển thị các album ảnh --}}
            <div class="gallery-post-section pt-60">
                <div class="row">
                    @forelse($galleries as $gallery)
                    {{-- Hiển thị từng album ảnh --}}
                    <div class="col-lg-4 col-sm-6 mb-4 pb-md-3">
                        <div class="gallery-post mb-2">
                            <a href="{{route('galleryPage',$gallery->album_id)}}" data-turbo="false">
{{--                                <img data-src="{{ !empty($gallery->gallery_image['0']) ? $gallery->gallery_image['0']: asset('front_web/images/default.jpg') }}" alt="" src="{{ asset('front_web/images/bg-process.png') }}" class="w-100 h-100 lazy" />--}}
                                {{-- Hiển thị ảnh đại diện của album, nếu không có thì hiển thị ảnh mặc định --}}
                                <img src="{{ !empty($gallery->gallery_image['0']) ? $gallery->gallery_image['0']: asset('front_web/images/default.jpg') }}" alt="" class="w-100 h-100" />
                            </a>
                        </div>
                        {{-- Hiển thị tên album --}}
                        <a href="{{route('galleryPage',$gallery->album_id)}}" class="fs-16 fw-6 text-black" data-turbo="false">{!! $gallery->album->name !!}</a>
                    </div>
                    @empty
                        {{-- Hiển thị thông báo khi không có album nào --}}
                        <div class="text-center text-dark">
                            <div class="my-5">
                                <h5>{{__('messages.no_album_found')}}</h5>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
    <!-- Kết thúc phần gallery -->
</div>
@endsection
@section('script')
{{--    <script src="{{ asset('assets/js/front/gallery-page.js') }}"></script>--}}
@endsection
