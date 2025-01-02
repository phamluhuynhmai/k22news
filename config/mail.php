<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cấu hình Mailer Mặc định
    |--------------------------------------------------------------------------
    |
    | Tùy chọn này điều khiển mailer mặc định được sử dụng để gửi bất kỳ email
    | nào từ ứng dụng của bạn. Các mailer thay thế có thể được thiết lập
    | và sử dụng khi cần; tuy nhiên, mailer này sẽ được sử dụng mặc định.
    |
    */

    'default' => env('MAIL_MAILER', 'smtp'),

    /*
    |--------------------------------------------------------------------------
    | Cấu hình các Mailer
    |--------------------------------------------------------------------------
    |
    | Ở đây bạn có thể cấu hình tất cả các mailer được sử dụng bởi ứng dụng
    | cùng với các cài đặt tương ứng. Một số ví dụ đã được cấu hình sẵn
    | và bạn có thể tự do thêm mailer của riêng mình khi ứng dụng yêu cầu.
    |
    | Laravel hỗ trợ nhiều driver "transport" mail khác nhau để sử dụng khi
    | gửi email. Bạn sẽ chỉ định driver nào bạn đang sử dụng cho các
    | mailer của mình dưới đây. Bạn có thể thêm mailer bổ sung nếu cần.
    |
    | Hỗ trợ: "smtp", "sendmail", "mailgun", "ses",
    |         "postmark", "log", "array", "failover"
    |
    */

    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
            'port' => env('MAIL_PORT', 587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN'),
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'mailgun' => [
            'transport' => 'mailgun',
        ],

        'postmark' => [
            'transport' => 'postmark',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Địa chỉ "From" Toàn cục
    |--------------------------------------------------------------------------
    |
    | Bạn có thể muốn tất cả email được gửi bởi ứng dụng của mình đều được gửi từ
    | cùng một địa chỉ. Tại đây, bạn có thể chỉ định tên và địa chỉ được
    | sử dụng toàn cục cho tất cả email được gửi bởi ứng dụng của bạn.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cài đặt Mail Markdown
    |--------------------------------------------------------------------------
    |
    | Nếu bạn đang sử dụng việc render email dựa trên Markdown, bạn có thể cấu hình
    | đường dẫn theme và component ở đây, cho phép tùy chỉnh thiết kế
    | của email. Hoặc bạn có thể đơn giản sử dụng mặc định của Laravel!
    |
    */

    'markdown' => [
        'theme' => 'default',

        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],

];
