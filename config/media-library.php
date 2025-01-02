<?php

return [

    /*
     * Tên disk để lưu trữ file và hình ảnh được tạo ra. Có thể chọn một hoặc nhiều
     * disk đã được cấu hình trong config/filesystems.php
     */
    'disk_name' => env('MEDIA_DISK', 'public'),

    /*
     * Kích thước tối đa của file tính bằng bytes.
     * Nếu upload file lớn hơn sẽ báo lỗi.
     */
    'max_file_size' => 1024 * 1024 * 10, // 10MB

    /*
     * Kết nối queue sẽ được sử dụng để tạo các hình ảnh derived và responsive.
     * Để trống để sử dụng kết nối queue mặc định.
     */
    'queue_connection_name' => env('QUEUE_CONNECTION', 'sync'),

    /*
     * Queue này sẽ được sử dụng để tạo hình ảnh derived và responsive.
     * Để trống để sử dụng queue mặc định.
     */
    'queue_name' => '',

    /*
     * Mặc định tất cả các chuyển đổi sẽ được thực hiện trên queue.
     */
    'queue_conversions_by_default' => env('QUEUE_CONVERSIONS_BY_DEFAULT', true),

    /*
     * Tên đầy đủ của class model media.
     */
    'media_model' => Spatie\MediaLibrary\MediaCollections\Models\Media::class,

    /*
     * Khi bật, các bộ sưu tập media sẽ được serialize sử dụng hành vi
     * serialize model mặc định của Laravel.
     * 
     * Giữ tùy chọn này tắt nếu sử dụng các thành phần Media Library Pro
     */
    'use_default_collection_serialization' => false,

    /*
     * Tên đầy đủ của class model được sử dụng cho tải lên tạm thời.
     *
     * Model này chỉ được sử dụng trong Media Library Pro
     */
    'temporary_upload_model' => Spatie\MediaLibraryPro\Models\TemporaryUpload::class,

    /*
     * Khi bật, Media Library Pro sẽ chỉ xử lý các tải lên tạm thời được tải lên
     * trong cùng một phiên. Bạn có thể tắt tính năng này để sử dụng stateless
     * cho các thành phần pro.
     */
    'enable_temporary_uploads_session_affinity' => true,

    /*
     * Khi bật, Media Library Pro sẽ tạo thumbnail cho file đã tải lên.
     */
    'generate_thumbnails_for_temporary_uploads' => true,

    /*
     * Đây là class chịu trách nhiệm đặt tên cho các file được tạo ra.
     */
    'file_namer' => Spatie\MediaLibrary\Support\FileNamer\DefaultFileNamer::class,

    /*
     * Class chứa chiến lược để xác định đường dẫn của file media.
     */
    'path_generator' => Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator::class,

    /*
     * Ở đây bạn có thể chỉ định path generator nào sẽ được sử dụng cho class nào.
     */
    'custom_path_generators' => [
        // Model::class => PathGenerator::class
        // hoặc
        // 'model_morph_alias' => PathGenerator::class
    ],

    /*
     * Khi URL đến file được tạo ra, class này sẽ được gọi. Sử dụng mặc định
     * nếu file của bạn được lưu trữ cục bộ trên thư mục gốc hoặc trên s3.
     */
    'url_generator' => Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator::class,

    /*
     * Di chuyển media khi cập nhật để giữ đường dẫn nhất quán. Chỉ bật nó với
     * PathGenerator tùy chỉnh sử dụng UUID của media.
     */
    'moves_media_on_update' => false,

    /*
     * Có kích hoạt versioning khi URL đến file được tạo ra hay không.
     * Khi kích hoạt, điều này sẽ thêm ?v=xx vào URL.
     */
    'version_urls' => false,

    /*
     * Cấu hình tối ưu hóa hình ảnh
     * Package sẽ cố gắng tối ưu hóa tất cả các hình ảnh đã chuyển đổi bằng cách xóa
     * metadata và áp dụng một chút nén. Đây là các trình tối ưu hóa mặc định.
     */
    'image_optimizers' => [
        Spatie\ImageOptimizer\Optimizers\Jpegoptim::class => [
            '-m85', // đặt chất lượng tối đa là 85%
            '--force', // đảm bảo việc tạo progressive luôn được thực hiện
            '--strip-all', // xóa tất cả thông tin văn bản như comments và EXIF data
            '--all-progressive', // đảm bảo hình ảnh kết quả là dạng progressive
        ],
        Spatie\ImageOptimizer\Optimizers\Pngquant::class => [
            '--force', // tham số bắt buộc cho package này
        ],
        Spatie\ImageOptimizer\Optimizers\Optipng::class => [
            '-i0', // tạo ra hình ảnh non-interlaced, quét theo kiểu progressive
            '-o2', // đặt mức độ tối ưu hóa là 2 (thực hiện nhiều lần nén IDAT)
            '-quiet', // tham số bắt buộc cho package này
        ],
        Spatie\ImageOptimizer\Optimizers\Svgo::class => [
            '--disable=cleanupIDs', // tắt tính năng này vì nó có thể gây ra vấn đề
        ],
        Spatie\ImageOptimizer\Optimizers\Gifsicle::class => [
            '-b', // tham số bắt buộc cho package này
            '-O3', // tạo ra kết quả chậm nhất nhưng tốt nhất
        ],
        Spatie\ImageOptimizer\Optimizers\Cwebp::class => [
            '-m 6', // phương pháp nén chậm nhất để có được sự nén tốt nhất
            '-pass 10', // tối đa hóa số lượng phân tích
            '-mt', // đa luồng để cải thiện tốc độ
            '-q 90', // hệ số chất lượng tạo ra ít thay đổi nhận thấy nhất
        ],
        Spatie\ImageOptimizer\Optimizers\Avifenc::class => [
            '-a cq-level=23', // mức chất lượng không đổi, giá trị thấp hơn nghĩa là chất lượng tốt hơn và kích thước file lớn hơn (0-63)
            '-j all', // số lượng luồng xử lý ("all" sử dụng tất cả các lõi có sẵn)
            '--min 0', // lượng tử hóa tối thiểu cho màu sắc (0-63)
            '--max 63', // lượng tử hóa tối đa cho màu sắc (0-63)
            '--minalpha 0', // lượng tử hóa tối thiểu cho độ trong suốt (0-63)
            '--maxalpha 63', // lượng tử hóa tối đa cho độ trong suốt (0-63)
            '-a end-usage=q', // chế độ điều khiển tốc độ được đặt thành chế độ Chất lượng Không đổi
            '-a tune=ssim', // SSIM như điều chỉnh bộ mã hóa cho số đo biến dạng
        ],
    ],

    /*
     * Các generators này sẽ được sử dụng để tạo hình ảnh từ các file media
     */
    'image_generators' => [
        Spatie\MediaLibrary\Conversions\ImageGenerators\Image::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Webp::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Avif::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Pdf::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Svg::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Video::class,
    ],

    /*
     * Đường dẫn để lưu trữ các file tạm thời trong quá trình chuyển đổi hình ảnh.
     * Nếu để null, sẽ sử dụng storage_path('media-library/temp')
     */
    'temporary_directory_path' => null,

    /*
     * Driver xử lý hình ảnh.
     * Có thể là 'gd' hoặc 'imagick'.
     */
    'image_driver' => env('IMAGE_DRIVER', 'gd'),

    /*
     * Đường dẫn đến FFMPEG & FFProbe, chỉ sử dụng khi bạn cần tạo thumbnail cho video
     * và đã cài đặt php-ffmpeg/php-ffmpeg qua composer
     */
    'ffmpeg_path' => env('FFMPEG_PATH', '/usr/bin/ffmpeg'),
    'ffprobe_path' => env('FFPROBE_PATH', '/usr/bin/ffprobe'),

    /*
     * Ở đây bạn có thể ghi đè tên class của các job được sử dụng bởi package này.
     * Đảm bảo các job tùy chỉnh của bạn mở rộng từ các job được cung cấp bởi package.
     */
    'jobs' => [
        'perform_conversions' => Spatie\MediaLibrary\Conversions\Jobs\PerformConversionsJob::class,
        'generate_responsive_images' => Spatie\MediaLibrary\ResponsiveImages\Jobs\GenerateResponsiveImagesJob::class,
    ],

    /*
     * Khi sử dụng phương thức addMediaFromUrl, bạn có thể muốn thay thế trình tải xuống mặc định.
     * Điều này đặc biệt hữu ích khi URL của hình ảnh nằm sau tường lửa và
     * cần thêm các flag bổ sung, có thể sử dụng curl.
     */
    'media_downloader' => Spatie\MediaLibrary\Downloaders\DefaultDownloader::class,

    'remote' => [
        /*
         * Bất kỳ header bổ sung nào cần được bao gồm khi tải lên media vào
         * disk từ xa. Mặc dù các header được hỗ trợ có thể khác nhau giữa
         * các driver khác nhau, một giá trị mặc định hợp lý đã được cung cấp.
         *
         * Được S3 hỗ trợ: CacheControl, Expires, StorageClass,
         * ServerSideEncryption, Metadata, ACL, ContentEncoding
         */
        'extra_headers' => [
            'CacheControl' => 'max-age=604800',
        ],
    ],

    /*
     * Cấu hình cho responsive images
     */
    'responsive_images' => [
        /*
         * Class này chịu trách nhiệm tính toán chiều rộng mục tiêu của responsive
         * images. Mặc định chúng tôi tối ưu hóa cho kích thước file và tạo các biến thể
         * mỗi cái nhỏ hơn 30% so với cái trước đó.
         */
        'width_calculator' => Spatie\MediaLibrary\ResponsiveImages\WidthCalculator\FileSizeOptimizedWidthCalculator::class,

        /*
         * Mặc định khi render media thành responsive image sẽ thêm một số javascript và placeholder nhỏ.
         * Điều này đảm bảo trình duyệt có thể xác định layout chính xác ngay lập tức.
         */
        'use_tiny_placeholders' => true,

        /*
         * Class này sẽ tạo placeholder nhỏ được sử dụng cho tải hình ảnh progressive.
         * Mặc định thư viện media sẽ sử dụng hình ảnh jpg mờ nhỏ.
         */
        'tiny_placeholder_generator' => Spatie\MediaLibrary\ResponsiveImages\TinyPlaceholderGenerator\Blurred::class,
    ],

    /*
     * Khi bật tùy chọn này, một route sẽ được đăng ký cho phép
     * các component Vue và React của Media Library Pro di chuyển các file đã tải lên
     * trong bucket S3 đến đúng vị trí của chúng.
     */
    'enable_vapor_uploads' => env('ENABLE_MEDIA_LIBRARY_VAPOR_UPLOADS', false),

    /*
     * Khi chuyển đổi các instance Media thành response, thư viện media sẽ thêm
     * thuộc tính 'loading' vào thẻ 'img'. Ở đây bạn có thể đặt giá trị mặc định
     * của thuộc tính đó.
     *
     * Các giá trị có thể: 'lazy', 'eager', 'auto' hoặc null nếu bạn không muốn đặt bất kỳ chỉ dẫn tải nào.
     */
    'default_loading_attribute_value' => null,

    /*
     * Tiền tố được sử dụng cho việc lưu trữ tất cả media.
     * Nếu đặt là '/my-subdir', tất cả media sẽ được lưu trong thư mục '/my-subdir'
     */
    'prefix' => env('MEDIA_PREFIX', ''),
];
