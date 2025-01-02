<?php return array (
  'app' => 
  array (
    'name' => 'ETS and RAC',
    'env' => 'local',
    'debug' => false,
    'url' => 'http://127.0.0.1:8000/',
    'timezone' => 'UTC',
    'locale' => 'English-en',
    'fallback_locale' => 'English-en',
    'faker_locale' => 'en_US',
    'key' => 'base64:maxQFpmbI6x32vwUXFjpYdELc+wBpMb9y8PaXugSaf8=',
    'cipher' => 'AES-256-CBC',
    'log' => 'daily',
    'log_level' => 'error',
    'providers' => 
    array (
      0 => 'Barryvdh\\DomPDF\\ServiceProvider',
      1 => 'Illuminate\\Auth\\AuthServiceProvider',
      2 => 'Illuminate\\Broadcasting\\BroadcastServiceProvider',
      3 => 'Illuminate\\Bus\\BusServiceProvider',
      4 => 'Illuminate\\Cache\\CacheServiceProvider',
      5 => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
      6 => 'Illuminate\\Cookie\\CookieServiceProvider',
      7 => 'Illuminate\\Database\\DatabaseServiceProvider',
      8 => 'Illuminate\\Encryption\\EncryptionServiceProvider',
      9 => 'Illuminate\\Filesystem\\FilesystemServiceProvider',
      10 => 'Illuminate\\Foundation\\Providers\\FoundationServiceProvider',
      11 => 'Illuminate\\Hashing\\HashServiceProvider',
      12 => 'Illuminate\\Mail\\MailServiceProvider',
      13 => 'Illuminate\\Notifications\\NotificationServiceProvider',
      14 => 'Illuminate\\Pagination\\PaginationServiceProvider',
      15 => 'Illuminate\\Pipeline\\PipelineServiceProvider',
      16 => 'Illuminate\\Queue\\QueueServiceProvider',
      17 => 'Illuminate\\Redis\\RedisServiceProvider',
      18 => 'Illuminate\\Auth\\Passwords\\PasswordResetServiceProvider',
      19 => 'Illuminate\\Session\\SessionServiceProvider',
      20 => 'Illuminate\\Translation\\TranslationServiceProvider',
      21 => 'Illuminate\\Validation\\ValidationServiceProvider',
      22 => 'Illuminate\\View\\ViewServiceProvider',
      23 => 'Laravel\\Tinker\\TinkerServiceProvider',
      24 => 'App\\Providers\\AppServiceProvider',
      25 => 'App\\Providers\\AuthServiceProvider',
      26 => 'App\\Providers\\EventServiceProvider',
      27 => 'App\\Providers\\RouteServiceProvider',
      28 => 'Collective\\Html\\HtmlServiceProvider',
      29 => 'Laravel\\Passport\\PassportServiceProvider',
      30 => 'Spatie\\Permission\\PermissionServiceProvider',
      31 => 'Kreait\\Laravel\\Firebase\\ServiceProvider',
      32 => 'Maatwebsite\\Excel\\ExcelServiceProvider',
      33 => 'Spatie\\Backup\\BackupServiceProvider',
      34 => 'NotificationChannels\\WebPush\\WebPushServiceProvider',
      35 => 'Edujugon\\PushNotification\\Providers\\PushNotificationServiceProvider',
      36 => 'Yajra\\DataTables\\DataTablesServiceProvider',
    ),
    'aliases' => 
    array (
      'App' => 'Illuminate\\Support\\Facades\\App',
      'Artisan' => 'Illuminate\\Support\\Facades\\Artisan',
      'Auth' => 'Illuminate\\Support\\Facades\\Auth',
      'Blade' => 'Illuminate\\Support\\Facades\\Blade',
      'Broadcast' => 'Illuminate\\Support\\Facades\\Broadcast',
      'Bus' => 'Illuminate\\Support\\Facades\\Bus',
      'Cache' => 'Illuminate\\Support\\Facades\\Cache',
      'Cookie' => 'Illuminate\\Support\\Facades\\Cookie',
      'Crypt' => 'Illuminate\\Support\\Facades\\Crypt',
      'DB' => 'Illuminate\\Support\\Facades\\DB',
      'Eloquent' => 'Illuminate\\Database\\Eloquent\\Model',
      'Event' => 'Illuminate\\Support\\Facades\\Event',
      'File' => 'Illuminate\\Support\\Facades\\File',
      'Gate' => 'Illuminate\\Support\\Facades\\Gate',
      'Hash' => 'Illuminate\\Support\\Facades\\Hash',
      'Lang' => 'Illuminate\\Support\\Facades\\Lang',
      'Log' => 'Illuminate\\Support\\Facades\\Log',
      'Mail' => 'Illuminate\\Support\\Facades\\Mail',
      'Notification' => 'Illuminate\\Support\\Facades\\Notification',
      'Password' => 'Illuminate\\Support\\Facades\\Password',
      'Queue' => 'Illuminate\\Support\\Facades\\Queue',
      'Redirect' => 'Illuminate\\Support\\Facades\\Redirect',
      'Redis' => 'Illuminate\\Support\\Facades\\Redis',
      'Request' => 'Illuminate\\Support\\Facades\\Request',
      'Response' => 'Illuminate\\Support\\Facades\\Response',
      'Route' => 'Illuminate\\Support\\Facades\\Route',
      'Schema' => 'Illuminate\\Support\\Facades\\Schema',
      'Session' => 'Illuminate\\Support\\Facades\\Session',
      'Storage' => 'Illuminate\\Support\\Facades\\Storage',
      'URL' => 'Illuminate\\Support\\Facades\\URL',
      'Validator' => 'Illuminate\\Support\\Facades\\Validator',
      'View' => 'Illuminate\\Support\\Facades\\View',
      'Form' => 'Collective\\Html\\FormFacade',
      'Html' => 'Collective\\Html\\HtmlFacade',
      'Input' => 'Illuminate\\Support\\Facades\\Input',
      'Hyvikk' => 'App\\Model\\Hyvikk',
      'Excel' => 'Maatwebsite\\Excel\\Facades\\Excel',
      'DataTables' => 'Yajra\\DataTables\\Facades\\DataTables',
      'PDF' => 'Barryvdh\\DomPDF\\Facade',
    ),
    'debug_blacklist' => 
    array (
      '_COOKIE' => 
      array (
      ),
      '_SERVER' => 
      array (
        0 => 'ALLUSERSPROFILE',
        1 => 'APPDATA',
        2 => 'CHROME_CRASHPAD_PIPE_NAME',
        3 => 'CommonProgramFiles',
        4 => 'CommonProgramFiles(x86)',
        5 => 'CommonProgramW6432',
        6 => 'COMPUTERNAME',
        7 => 'ComSpec',
        8 => 'DriverData',
        9 => 'EFC_7888',
        10 => 'HOMEDRIVE',
        11 => 'HOMEPATH',
        12 => 'LOCALAPPDATA',
        13 => 'LOGONSERVER',
        14 => 'NUMBER_OF_PROCESSORS',
        15 => 'OneDrive',
        16 => 'OneDriveConsumer',
        17 => 'ORIGINAL_XDG_CURRENT_DESKTOP',
        18 => 'OS',
        19 => 'Path',
        20 => 'PATHEXT',
        21 => 'PROCESSOR_ARCHITECTURE',
        22 => 'PROCESSOR_IDENTIFIER',
        23 => 'PROCESSOR_LEVEL',
        24 => 'PROCESSOR_REVISION',
        25 => 'ProgramData',
        26 => 'ProgramFiles',
        27 => 'ProgramFiles(x86)',
        28 => 'ProgramW6432',
        29 => 'PSModulePath',
        30 => 'PUBLIC',
        31 => 'SESSIONNAME',
        32 => 'SystemDrive',
        33 => 'SystemRoot',
        34 => 'TEMP',
        35 => 'TMP',
        36 => 'USERDOMAIN',
        37 => 'USERDOMAIN_ROAMINGPROFILE',
        38 => 'USERNAME',
        39 => 'USERPROFILE',
        40 => 'windir',
        41 => 'TERM_PROGRAM',
        42 => 'TERM_PROGRAM_VERSION',
        43 => 'LANG',
        44 => 'COLORTERM',
        45 => 'GIT_ASKPASS',
        46 => 'VSCODE_GIT_ASKPASS_NODE',
        47 => 'VSCODE_GIT_ASKPASS_EXTRA_ARGS',
        48 => 'VSCODE_GIT_ASKPASS_MAIN',
        49 => 'VSCODE_GIT_IPC_HANDLE',
        50 => 'VSCODE_INJECTION',
        51 => 'PHP_SELF',
        52 => 'SCRIPT_NAME',
        53 => 'SCRIPT_FILENAME',
        54 => 'PATH_TRANSLATED',
        55 => 'DOCUMENT_ROOT',
        56 => 'REQUEST_TIME_FLOAT',
        57 => 'REQUEST_TIME',
        58 => 'argv',
        59 => 'argc',
        60 => 'APP_NAME',
        61 => 'APP_URL',
        62 => 'DB_CONNECTION',
        63 => 'DB_PORT',
        64 => 'MAIL_MAILER',
        65 => 'MAIL_HOST',
        66 => 'MAIL_PORT',
        67 => 'MAIL_USERNAME',
        68 => 'MAIL_PASSWORD',
        69 => 'MAIL_ENCRYPTION',
        70 => 'MAIL_FROM_ADDRESS',
        71 => 'MAIL_FROM_NAME',
        72 => 'PUSHER_APP_ID',
        73 => 'PUSHER_APP_KEY',
        74 => 'PUSHER_APP_SECRET',
        75 => 'PUSHER_APP_CLUSTER',
        76 => 'FIREBASE_CREDENTIALS',
        77 => 'front_enable',
        78 => 'DB_HOST',
        79 => 'DB_DATABASE',
        80 => 'DB_USERNAME',
        81 => 'DB_PASSWORD',
        82 => 'SHELL_VERBOSITY',
      ),
      '_ENV' => 
      array (
        0 => 'APP_NAME',
        1 => 'APP_URL',
        2 => 'DB_CONNECTION',
        3 => 'DB_PORT',
        4 => 'MAIL_MAILER',
        5 => 'MAIL_HOST',
        6 => 'MAIL_PORT',
        7 => 'MAIL_USERNAME',
        8 => 'MAIL_PASSWORD',
        9 => 'MAIL_ENCRYPTION',
        10 => 'MAIL_FROM_ADDRESS',
        11 => 'MAIL_FROM_NAME',
        12 => 'PUSHER_APP_ID',
        13 => 'PUSHER_APP_KEY',
        14 => 'PUSHER_APP_SECRET',
        15 => 'PUSHER_APP_CLUSTER',
        16 => 'FIREBASE_CREDENTIALS',
        17 => 'front_enable',
        18 => 'DB_HOST',
        19 => 'DB_DATABASE',
        20 => 'DB_USERNAME',
        21 => 'DB_PASSWORD',
        22 => 'SHELL_VERBOSITY',
      ),
    ),
  ),
  'auth' => 
  array (
    'defaults' => 
    array (
      'guard' => 'web',
      'passwords' => 'users',
    ),
    'guards' => 
    array (
      'web' => 
      array (
        'driver' => 'session',
        'provider' => 'users',
      ),
      'api' => 
      array (
        'driver' => 'token',
        'provider' => 'users',
      ),
      'backend' => 
      array (
        'driver' => 'passport',
        'provider' => 'users',
      ),
    ),
    'providers' => 
    array (
      'users' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\Model\\User',
      ),
    ),
    'passwords' => 
    array (
      'users' => 
      array (
        'provider' => 'users',
        'table' => 'password_resets',
        'expire' => 60,
      ),
    ),
  ),
  'backup' => 
  array (
    'backup' => 
    array (
      'name' => 'ETS and RAC',
      'source' => 
      array (
        'files' => 
        array (
          'include' => 
          array (
            0 => 'C:\\xampp\\htdocs\\fleetRepo\\framework',
          ),
          'exclude' => 
          array (
            0 => 'C:\\xampp\\htdocs\\fleetRepo\\framework\\vendor',
            1 => 'C:\\xampp\\htdocs\\fleetRepo\\framework\\node_modules',
          ),
          'follow_links' => false,
        ),
        'databases' => 
        array (
          0 => 'mysql',
        ),
      ),
      'database_dump_compressor' => NULL,
      'destination' => 
      array (
        'filename_prefix' => '',
        'disks' => 
        array (
          0 => 'backup',
        ),
      ),
      'temporary_directory' => 'C:\\xampp\\htdocs\\fleetRepo\\framework\\storage\\backup',
    ),
    'notifications' => 
    array (
      'notifications' => 
      array (
        'Spatie\\Backup\\Notifications\\Notifications\\BackupHasFailed' => 
        array (
          0 => '',
        ),
        'Spatie\\Backup\\Notifications\\Notifications\\UnhealthyBackupWasFound' => 
        array (
          0 => '',
        ),
        'Spatie\\Backup\\Notifications\\Notifications\\CleanupHasFailed' => 
        array (
          0 => '',
        ),
        'Spatie\\Backup\\Notifications\\Notifications\\BackupWasSuccessful' => 
        array (
          0 => '',
        ),
        'Spatie\\Backup\\Notifications\\Notifications\\HealthyBackupWasFound' => 
        array (
          0 => '',
        ),
        'Spatie\\Backup\\Notifications\\Notifications\\CleanupWasSuccessful' => 
        array (
          0 => '',
        ),
      ),
      'notifiable' => 'Spatie\\Backup\\Notifications\\Notifiable',
      'mail' => 
      array (
        'to' => 'info@hyvikk.com',
        'from' => 
        array (
          'address' => 'dheerajkumarp777@gmail.com',
          'name' => 'ETS',
        ),
      ),
      'slack' => 
      array (
        'webhook_url' => '',
        'channel' => NULL,
        'username' => NULL,
        'icon' => NULL,
      ),
    ),
    'monitor_backups' => 
    array (
      0 => 
      array (
        'name' => 'ETS and RAC',
        'disks' => 
        array (
          0 => 'backup',
        ),
        'health_checks' => 
        array (
          'Spatie\\Backup\\Tasks\\Monitor\\HealthChecks\\MaximumAgeInDays' => 1,
          'Spatie\\Backup\\Tasks\\Monitor\\HealthChecks\\MaximumStorageInMegabytes' => 5000,
        ),
      ),
    ),
    'cleanup' => 
    array (
      'strategy' => 'Spatie\\Backup\\Tasks\\Cleanup\\Strategies\\DefaultStrategy',
      'default_strategy' => 
      array (
        'keep_all_backups_for_days' => 7,
        'keep_daily_backups_for_days' => 16,
        'keep_weekly_backups_for_weeks' => 8,
        'keep_monthly_backups_for_months' => 4,
        'keep_yearly_backups_for_years' => 2,
        'delete_oldest_backups_when_using_more_megabytes_than' => 5000,
      ),
    ),
  ),
  'broadcasting' => 
  array (
    'default' => 'null',
    'connections' => 
    array (
      'pusher' => 
      array (
        'driver' => 'pusher',
        'app_id' => '1840292',
        'key' => 'b7ddce027ab88bb3a477',
        'secret' => 'e77a3699434091bc03f6',
        'options' => 
        array (
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
      ),
      'log' => 
      array (
        'driver' => 'log',
      ),
      'null' => 
      array (
        'driver' => 'null',
      ),
    ),
  ),
  'cache' => 
  array (
    'default' => 'file',
    'stores' => 
    array (
      'apc' => 
      array (
        'driver' => 'apc',
      ),
      'array' => 
      array (
        'driver' => 'array',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'cache',
        'connection' => NULL,
      ),
      'file' => 
      array (
        'driver' => 'file',
        'path' => 'C:\\xampp\\htdocs\\fleetRepo\\framework\\storage\\framework/cache/data',
      ),
      'memcached' => 
      array (
        'driver' => 'memcached',
        'persistent_id' => NULL,
        'sasl' => 
        array (
          0 => NULL,
          1 => NULL,
        ),
        'options' => 
        array (
        ),
        'servers' => 
        array (
          0 => 
          array (
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 100,
          ),
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
      ),
    ),
    'prefix' => 'laravel',
  ),
  'currency' => 
  array (
    'INR' => 'INR',
    'USD' => 'USD',
    'AED' => 'AED',
    'ALL' => 'ALL',
    'AMD' => 'AMD',
    'ARS' => 'ARS',
    'AUD' => 'AUD',
    'AWG' => 'AWG',
    'BBD' => 'BBD',
    'BDT' => 'BDT',
    'BMD' => 'BMD',
    'BND' => 'BND',
    'BOB' => 'BOB',
    'BSD' => 'BSD',
    'BWP' => 'BWP',
    'BZD' => 'BZD',
    'CAD' => 'CAD',
    'CHF' => 'CHF',
    'CNY' => 'CNY',
    'COP' => 'COP',
    'CRC' => 'CRC',
    'CZK' => 'CZK',
    'DKK' => 'DKK',
    'DOP' => 'DOP',
    'DZD' => 'DZD',
    'EGP' => 'EGP',
    'ETB' => 'ETB',
    'EUR' => 'EUR',
    'FJD' => 'FJD',
    'GBP' => 'GBP',
    'GIP' => 'GIP',
    'GMD' => 'GMD',
    'GTQ' => 'GTQ',
    'GYD' => 'GYD',
    'HKD' => 'HKD',
    'HNL' => 'HNL',
    'HRK' => 'HRK',
    'HTG' => 'HTG',
    'HUF' => 'HUF',
    'IDR' => 'IDR',
    'ILS' => 'ILS',
    'JMD' => 'JMD',
    'KES' => 'KES',
    'KGS' => 'KGS',
    'KHR' => 'KHR',
    'KYD' => 'KYD',
    'KZT' => 'KZT',
    'LAK' => 'LAK',
    'LBP' => 'LBP',
    'LKR' => 'LKR',
    'LRD' => 'LRD',
    'LSL' => 'LSL',
    'MAD' => 'MAD',
    'MDL' => 'MDL',
    'MKD' => 'MKD',
    'MMK' => 'MMK',
    'MNT' => 'MNT',
    'MOP' => 'MOP',
    'MUR' => 'MUR',
    'MVR' => 'MVR',
    'MWK' => 'MWK',
    'MXN' => 'MXN',
    'MYR' => 'MYR',
    'NAD' => 'NAD',
    'NGN' => 'NGN',
    'NIO' => 'NIO',
    'NOK' => 'NOK',
    'NPR' => 'NPR',
    'NZD' => 'NZD',
    'PEN' => 'PEN',
    'PGK' => 'PGK',
    'PHP' => 'PHP',
    'PKR' => 'PKR',
    'QAR' => 'QAR',
    'RUB' => 'RUB',
    'SAR' => 'SAR',
    'SCR' => 'SCR',
    'SEK' => 'SEK',
    'SGD' => 'SGD',
    'SLL' => 'SLL',
    'SOS' => 'SOS',
    'SZL' => 'SZL',
    'TTD' => 'TTD',
    'TZS' => 'TZS',
    'UYU' => 'UYU',
    'UZS' => 'UZS',
    'YER' => 'YER',
    'ZAR' => 'ZAR',
    'AFN' => 'AFN',
    'ANG' => 'ANG',
    'AOA' => 'AOA',
    'AZN' => 'AZN',
    'BAM' => 'BAM',
    'BGN' => 'BGN',
    'BIF' => 'BIF',
    'BRL' => 'BRL',
    'CDF' => 'CDF',
    'CLP' => 'CLP',
    'CVE' => 'CVE',
    'DJF' => 'DJF',
    'FKP' => 'FKP',
    'GEL' => 'GEL',
    'GNF' => 'GNF',
    'ISK' => 'ISK',
    'JPY' => 'JPY',
    'KMF' => 'KMF',
    'KRW' => 'KRW',
    'MGA' => 'MGA',
    'MRO' => 'MRO',
    'MZN' => 'MZN',
    'PAB' => 'PAB',
    'PLN' => 'PLN',
    'PYG' => 'PYG',
    'RON' => 'RON',
    'RSD' => 'RSD',
    'RWF' => 'RWF',
    'SBD' => 'SBD',
    'SHP' => 'SHP',
    'SRD' => 'SRD',
    'STD' => 'STD',
    'THB' => 'THB',
    'TJS' => 'TJS',
    'TOP' => 'TOP',
    'TRY' => 'TRY',
    'TWD' => 'TWD',
    'UAH' => 'UAH',
    'UGX' => 'UGX',
    'VND' => 'VND',
    'VUV' => 'VUV',
    'WST' => 'WST',
    'XAF' => 'XAF',
    'XCD' => 'XCD',
    'XOF' => 'XOF',
    'XPF' => 'XPF',
    'ZMW' => 'ZMW',
    'CUP' => 'CUP',
    'GHS' => 'GHS',
    'SSP' => 'SSP',
    'SVC' => 'SVC',
  ),
  'database' => 
  array (
    'default' => 'mysql',
    'connections' => 
    array (
      'sqlite' => 
      array (
        'driver' => 'sqlite',
        'database' => 'fleet',
        'prefix' => '',
      ),
      'mysql' => 
      array (
        'driver' => 'mysql',
        'host' => 'localhost',
        'port' => '3306',
        'database' => 'fleet',
        'username' => 'root',
        'password' => '',
        'unix_socket' => '',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => '',
        'strict' => false,
        'engine' => NULL,
      ),
      'pgsql' => 
      array (
        'driver' => 'pgsql',
        'host' => 'localhost',
        'port' => '3306',
        'database' => 'fleet',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'public',
        'sslmode' => 'prefer',
      ),
    ),
    'migrations' => 'migrations',
    'redis' => 
    array (
      'client' => 'predis',
      'default' => 
      array (
        'host' => '127.0.0.1',
        'password' => NULL,
        'port' => 6379,
        'database' => 0,
      ),
    ),
  ),
  'datatables' => 
  array (
    'search' => 
    array (
      'smart' => true,
      'multi_term' => true,
      'case_insensitive' => true,
      'use_wildcards' => false,
      'starts_with' => false,
    ),
    'index_column' => 'DT_RowIndex',
    'engines' => 
    array (
      'eloquent' => 'Yajra\\DataTables\\EloquentDataTable',
      'query' => 'Yajra\\DataTables\\QueryDataTable',
      'collection' => 'Yajra\\DataTables\\CollectionDataTable',
      'resource' => 'Yajra\\DataTables\\ApiResourceDataTable',
    ),
    'builders' => 
    array (
    ),
    'nulls_last_sql' => ':column :direction NULLS LAST',
    'error' => NULL,
    'columns' => 
    array (
      'excess' => 
      array (
        0 => 'rn',
        1 => 'row_num',
      ),
      'escape' => '*',
      'raw' => 
      array (
        0 => 'action',
      ),
      'blacklist' => 
      array (
        0 => 'password',
        1 => 'remember_token',
      ),
      'whitelist' => '*',
    ),
    'json' => 
    array (
      'header' => 
      array (
      ),
      'options' => 0,
    ),
    'callback' => 
    array (
      0 => '$',
      1 => '$.',
      2 => 'function',
    ),
  ),
  'dompdf' => 
  array (
    'show_warnings' => false,
    'public_path' => NULL,
    'convert_entities' => true,
    'options' => 
    array (
      'font_dir' => 'C:\\xampp\\htdocs\\fleetRepo\\framework\\storage\\fonts',
      'font_cache' => 'C:\\xampp\\htdocs\\fleetRepo\\framework\\storage\\fonts',
      'temp_dir' => 'C:\\Users\\madhu\\AppData\\Local\\Temp',
      'chroot' => 'C:\\xampp\\htdocs\\fleetRepo\\framework',
      'allowed_protocols' => 
      array (
        'file://' => 
        array (
          'rules' => 
          array (
          ),
        ),
        'http://' => 
        array (
          'rules' => 
          array (
          ),
        ),
        'https://' => 
        array (
          'rules' => 
          array (
          ),
        ),
      ),
      'log_output_file' => NULL,
      'enable_font_subsetting' => false,
      'pdf_backend' => 'CPDF',
      'default_media_type' => 'screen',
      'default_paper_size' => 'a4',
      'default_paper_orientation' => 'portrait',
      'default_font' => 'serif',
      'dpi' => 96,
      'enable_php' => false,
      'enable_javascript' => true,
      'enable_remote' => true,
      'font_height_ratio' => 1.1,
      'enable_html5_parser' => true,
    ),
  ),
  'elfinder' => 
  array (
    'dir' => 
    array (
      0 => 'files',
    ),
    'disks' => 
    array (
    ),
    'route' => 
    array (
      'prefix' => 'elfinder',
      'middleware' => 
      array (
        0 => 'web',
        1 => 'auth',
      ),
    ),
    'access' => 'Barryvdh\\Elfinder\\Elfinder::checkAccess',
    'roots' => NULL,
    'options' => 
    array (
    ),
    'root_options' => 
    array (
    ),
  ),
  'excel' => 
  array (
    'exports' => 
    array (
      'chunk_size' => 1000,
      'pre_calculate_formulas' => false,
      'strict_null_comparison' => false,
      'csv' => 
      array (
        'delimiter' => ',',
        'enclosure' => '"',
        'line_ending' => '
',
        'use_bom' => false,
        'include_separator_line' => false,
        'excel_compatibility' => false,
      ),
      'properties' => 
      array (
        'creator' => '',
        'lastModifiedBy' => '',
        'title' => '',
        'description' => '',
        'subject' => '',
        'keywords' => '',
        'category' => '',
        'manager' => '',
        'company' => '',
      ),
    ),
    'imports' => 
    array (
      'read_only' => true,
      'ignore_empty' => false,
      'heading_row' => 
      array (
        'formatter' => 'slug',
      ),
      'csv' => 
      array (
        'delimiter' => ',',
        'enclosure' => '"',
        'escape_character' => '\\',
        'contiguous' => false,
        'input_encoding' => 'UTF-8',
      ),
      'properties' => 
      array (
        'creator' => '',
        'lastModifiedBy' => '',
        'title' => '',
        'description' => '',
        'subject' => '',
        'keywords' => '',
        'category' => '',
        'manager' => '',
        'company' => '',
      ),
    ),
    'extension_detector' => 
    array (
      'xlsx' => 'Xlsx',
      'xlsm' => 'Xlsx',
      'xltx' => 'Xlsx',
      'xltm' => 'Xlsx',
      'xls' => 'Xls',
      'xlt' => 'Xls',
      'ods' => 'Ods',
      'ots' => 'Ods',
      'slk' => 'Slk',
      'xml' => 'Xml',
      'gnumeric' => 'Gnumeric',
      'htm' => 'Html',
      'html' => 'Html',
      'csv' => 'Csv',
      'tsv' => 'Csv',
      'pdf' => 'Dompdf',
    ),
    'value_binder' => 
    array (
      'default' => 'Maatwebsite\\Excel\\DefaultValueBinder',
    ),
    'cache' => 
    array (
      'driver' => 'memory',
      'batch' => 
      array (
        'memory_limit' => 60000,
      ),
      'illuminate' => 
      array (
        'store' => NULL,
      ),
    ),
    'transactions' => 
    array (
      'handler' => 'db',
    ),
    'temporary_files' => 
    array (
      'local_path' => 'C:\\xampp\\htdocs\\fleetRepo\\framework\\storage\\framework/laravel-excel',
      'remote_disk' => NULL,
      'remote_prefix' => NULL,
      'force_resync_remote' => NULL,
    ),
  ),
  'filesystems' => 
  array (
    'default' => 'local',
    'cloud' => 's3',
    'disks' => 
    array (
      'local' => 
      array (
        'driver' => 'local',
        'root' => 'C:\\xampp\\htdocs\\fleetRepo\\framework\\storage\\app',
      ),
      'backup' => 
      array (
        'driver' => 'local',
        'root' => 'C:\\xampp\\htdocs\\fleetRepo\\framework\\storage\\backup',
      ),
      'public' => 
      array (
        'driver' => 'local',
        'root' => 'C:\\xampp\\htdocs\\fleetRepo\\framework\\storage\\app/public',
        'url' => 'http://127.0.0.1:8000//storage',
        'visibility' => 'public',
      ),
      'views' => 
      array (
        'driver' => 'local',
        'root' => 'C:\\xampp\\htdocs\\fleetRepo\\framework\\resources/lang',
      ),
      'public_uploads' => 
      array (
        'driver' => 'local',
        'root' => 'public/uploads',
      ),
      'public_img' => 
      array (
        'driver' => 'local',
        'root' => 'img',
      ),
      'public_files' => 
      array (
        'driver' => 'local',
        'root' => 'files',
      ),
      'public_files2' => 
      array (
        'driver' => 'local',
        'root' => '../files',
      ),
      'public_img2' => 
      array (
        'driver' => 'local',
        'root' => '../img',
      ),
      's3' => 
      array (
        'driver' => 's3',
        'key' => NULL,
        'secret' => NULL,
        'region' => NULL,
        'bucket' => NULL,
      ),
    ),
  ),
  'firebase' => 
  array (
    'default' => 'app',
    'projects' => 
    array (
      'app' => 
      array (
        'credentials' => 
        array (
          'file' => 'C:\\xampp\\htdocs\\fleetRepo\\framework\\storage\\firebase/firebase_credentials.json',
          'auto_discovery' => true,
        ),
        'auth' => 
        array (
          'tenant_id' => NULL,
        ),
        'database' => 
        array (
          'url' => NULL,
        ),
        'dynamic_links' => 
        array (
          'default_domain' => NULL,
        ),
        'storage' => 
        array (
          'default_bucket' => NULL,
        ),
        'cache_store' => 'file',
        'logging' => 
        array (
          'http_log_channel' => NULL,
          'http_debug_log_channel' => NULL,
        ),
        'http_client_options' => 
        array (
          'proxy' => NULL,
          'timeout' => NULL,
        ),
        'debug' => false,
      ),
    ),
  ),
  'flare' => 
  array (
    'key' => NULL,
    'flare_middleware' => 
    array (
      0 => 'Spatie\\FlareClient\\FlareMiddleware\\RemoveRequestIp',
      1 => 'Spatie\\FlareClient\\FlareMiddleware\\AddGitInformation',
      2 => 'Spatie\\LaravelIgnition\\FlareMiddleware\\AddNotifierName',
      3 => 'Spatie\\LaravelIgnition\\FlareMiddleware\\AddEnvironmentInformation',
      4 => 'Spatie\\LaravelIgnition\\FlareMiddleware\\AddExceptionInformation',
      5 => 'Spatie\\LaravelIgnition\\FlareMiddleware\\AddDumps',
      'Spatie\\LaravelIgnition\\FlareMiddleware\\AddLogs' => 
      array (
        'maximum_number_of_collected_logs' => 200,
      ),
      'Spatie\\LaravelIgnition\\FlareMiddleware\\AddQueries' => 
      array (
        'maximum_number_of_collected_queries' => 200,
        'report_query_bindings' => true,
      ),
      'Spatie\\LaravelIgnition\\FlareMiddleware\\AddJobs' => 
      array (
        'max_chained_job_reporting_depth' => 5,
      ),
      'Spatie\\FlareClient\\FlareMiddleware\\CensorRequestBodyFields' => 
      array (
        'censor_fields' => 
        array (
          0 => 'password',
          1 => 'password_confirmation',
        ),
      ),
      'Spatie\\FlareClient\\FlareMiddleware\\CensorRequestHeaders' => 
      array (
        'headers' => 
        array (
          0 => 'API-KEY',
        ),
      ),
    ),
    'send_logs_as_events' => true,
  ),
  'ignition' => 
  array (
    'editor' => 'phpstorm',
    'theme' => 'auto',
    'enable_share_button' => true,
    'register_commands' => false,
    'solution_providers' => 
    array (
      0 => 'Spatie\\Ignition\\Solutions\\SolutionProviders\\BadMethodCallSolutionProvider',
      1 => 'Spatie\\Ignition\\Solutions\\SolutionProviders\\MergeConflictSolutionProvider',
      2 => 'Spatie\\Ignition\\Solutions\\SolutionProviders\\UndefinedPropertySolutionProvider',
      3 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\IncorrectValetDbCredentialsSolutionProvider',
      4 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingAppKeySolutionProvider',
      5 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\DefaultDbNameSolutionProvider',
      6 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\TableNotFoundSolutionProvider',
      7 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingImportSolutionProvider',
      8 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\InvalidRouteActionSolutionProvider',
      9 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\ViewNotFoundSolutionProvider',
      10 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\RunningLaravelDuskInProductionProvider',
      11 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingColumnSolutionProvider',
      12 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\UnknownValidationSolutionProvider',
      13 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingMixManifestSolutionProvider',
      14 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingViteManifestSolutionProvider',
      15 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingLivewireComponentSolutionProvider',
      16 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\UndefinedViewVariableSolutionProvider',
      17 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\GenericLaravelExceptionSolutionProvider',
    ),
    'ignored_solution_providers' => 
    array (
    ),
    'enable_runnable_solutions' => NULL,
    'remote_sites_path' => 'C:\\xampp\\htdocs\\fleetRepo\\framework',
    'local_sites_path' => '',
    'housekeeping_endpoint_prefix' => '_ignition',
    'settings_file_path' => '',
    'recorders' => 
    array (
      0 => 'Spatie\\LaravelIgnition\\Recorders\\DumpRecorder\\DumpRecorder',
      1 => 'Spatie\\LaravelIgnition\\Recorders\\JobRecorder\\JobRecorder',
      2 => 'Spatie\\LaravelIgnition\\Recorders\\LogRecorder\\LogRecorder',
      3 => 'Spatie\\LaravelIgnition\\Recorders\\QueryRecorder\\QueryRecorder',
    ),
    'open_ai_key' => NULL,
    'with_stack_frame_arguments' => true,
    'argument_reducers' => 
    array (
      0 => 'Spatie\\Backtrace\\Arguments\\Reducers\\BaseTypeArgumentReducer',
      1 => 'Spatie\\Backtrace\\Arguments\\Reducers\\ArrayArgumentReducer',
      2 => 'Spatie\\Backtrace\\Arguments\\Reducers\\StdClassArgumentReducer',
      3 => 'Spatie\\Backtrace\\Arguments\\Reducers\\EnumArgumentReducer',
      4 => 'Spatie\\Backtrace\\Arguments\\Reducers\\ClosureArgumentReducer',
      5 => 'Spatie\\Backtrace\\Arguments\\Reducers\\DateTimeArgumentReducer',
      6 => 'Spatie\\Backtrace\\Arguments\\Reducers\\DateTimeZoneArgumentReducer',
      7 => 'Spatie\\Backtrace\\Arguments\\Reducers\\SymphonyRequestArgumentReducer',
      8 => 'Spatie\\LaravelIgnition\\ArgumentReducers\\ModelArgumentReducer',
      9 => 'Spatie\\LaravelIgnition\\ArgumentReducers\\CollectionArgumentReducer',
      10 => 'Spatie\\Backtrace\\Arguments\\Reducers\\StringableArgumentReducer',
    ),
  ),
  'installer' => 
  array (
    'requirements' => 
    array (
      0 => 'openssl',
      1 => 'pdo',
      2 => 'mbstring',
      3 => 'tokenizer',
    ),
    'permissions' => 
    array (
      'storage/app/' => '775',
      'storage/framework/' => '775',
      'storage/logs/' => '775',
      'bootstrap/cache/' => '775',
    ),
  ),
  'logging' => 
  array (
    'default' => 'daily',
    'deprecations' => 'null',
    'channels' => 
    array (
      'stack' => 
      array (
        'driver' => 'stack',
        'channels' => 
        array (
          0 => 'single',
        ),
        'ignore_exceptions' => false,
      ),
      'single' => 
      array (
        'driver' => 'single',
        'path' => 'C:\\xampp\\htdocs\\fleetRepo\\framework\\storage\\logs/laravel.log',
        'level' => 'debug',
      ),
      'daily' => 
      array (
        'driver' => 'daily',
        'path' => 'C:\\xampp\\htdocs\\fleetRepo\\framework\\storage\\logs/laravel.log',
        'level' => 'debug',
        'days' => 14,
      ),
      'slack' => 
      array (
        'driver' => 'slack',
        'url' => NULL,
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
        'level' => 'critical',
      ),
      'papertrail' => 
      array (
        'driver' => 'monolog',
        'level' => 'debug',
        'handler' => 'Monolog\\Handler\\SyslogUdpHandler',
        'handler_with' => 
        array (
          'host' => NULL,
          'port' => NULL,
        ),
      ),
      'stderr' => 
      array (
        'driver' => 'monolog',
        'level' => 'debug',
        'handler' => 'Monolog\\Handler\\StreamHandler',
        'formatter' => NULL,
        'with' => 
        array (
          'stream' => 'php://stderr',
        ),
      ),
      'syslog' => 
      array (
        'driver' => 'syslog',
        'level' => 'debug',
      ),
      'errorlog' => 
      array (
        'driver' => 'errorlog',
        'level' => 'debug',
      ),
      'null' => 
      array (
        'driver' => 'monolog',
        'handler' => 'Monolog\\Handler\\NullHandler',
      ),
      'emergency' => 
      array (
        'path' => 'C:\\xampp\\htdocs\\fleetRepo\\framework\\storage\\logs/laravel.log',
      ),
    ),
  ),
  'mail' => 
  array (
    'driver' => 'smtp',
    'host' => 'sandbox.smtp.mailtrap.io',
    'port' => 2525,
    'from' => 
    array (
      'address' => 'from@example.com',
      'name' => 'Example',
    ),
    'username' => '681717fc279aff',
    'password' => 'd0f7e2eee41a4d',
    'encryption' => 'tls',
  ),
  'passport' => 
  array (
    'guard' => 'web',
    'private_key' => NULL,
    'public_key' => NULL,
    'client_uuids' => false,
    'personal_access_client' => 
    array (
      'id' => NULL,
      'secret' => NULL,
    ),
    'storage' => 
    array (
      'database' => 
      array (
        'connection' => 'mysql',
      ),
    ),
  ),
  'permission' => 
  array (
    'models' => 
    array (
      'permission' => 'Spatie\\Permission\\Models\\Permission',
      'role' => 'Spatie\\Permission\\Models\\Role',
    ),
    'table_names' => 
    array (
      'roles' => 'roles',
      'permissions' => 'permissions',
      'model_has_permissions' => 'model_has_permissions',
      'model_has_roles' => 'model_has_roles',
      'role_has_permissions' => 'role_has_permissions',
    ),
    'column_names' => 
    array (
      'model_morph_key' => 'model_id',
    ),
    'register_permission_check_method' => true,
    'teams' => false,
    'display_permission_in_exception' => false,
    'display_role_in_exception' => false,
    'enable_wildcard_permission' => false,
    'cache' => 
    array (
      'expiration_time' => 
      \DateInterval::__set_state(array(
         'from_string' => true,
         'date_string' => '24 hours',
      )),
      'key' => 'spatie.permission.cache',
      'model_key' => 'name',
      'store' => 'default',
    ),
  ),
  'push-notification' => 
  array (
    'appNameIOS' => 
    array (
      'environment' => 'development',
      'certificate' => '/path/to/certificate.pem',
      'passPhrase' => 'password',
      'service' => 'apns',
    ),
    'appNameAndroid' => 
    array (
      'environment' => 'development',
      'apiKey' => 'hgfdhjjdhfgjhgjdhg',
      'service' => 'gcm',
    ),
  ),
  'pushnotification' => 
  array (
    'gcm' => 
    array (
      'priority' => 'normal',
      'dry_run' => false,
      'apiKey' => NULL,
    ),
    'fcm' => 
    array (
      'priority' => 'normal',
      'dry_run' => false,
      'apiKey' => NULL,
    ),
    'apn' => 
    array (
      'certificate' => 'C:\\xampp\\htdocs\\fleetRepo\\framework\\config/iosCertificates/apns-dev-cert.pem',
      'passPhrase' => 'secret',
      'passFile' => 'C:\\xampp\\htdocs\\fleetRepo\\framework\\config/iosCertificates/yourKey.pem',
      'dry_run' => true,
    ),
  ),
  'queue' => 
  array (
    'default' => 'sync',
    'connections' => 
    array (
      'sync' => 
      array (
        'driver' => 'sync',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
      ),
      'beanstalkd' => 
      array (
        'driver' => 'beanstalkd',
        'host' => 'localhost',
        'queue' => 'default',
        'retry_after' => 90,
      ),
      'sqs' => 
      array (
        'driver' => 'sqs',
        'key' => 'your-public-key',
        'secret' => 'your-secret-key',
        'prefix' => 'https://sqs.us-east-1.amazonaws.com/your-account-id',
        'queue' => 'your-queue-name',
        'region' => 'us-east-1',
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'default',
        'retry_after' => 90,
      ),
    ),
    'failed' => 
    array (
      'database' => 'mysql',
      'table' => 'failed_jobs',
    ),
  ),
  'services' => 
  array (
    'mailgun' => 
    array (
      'domain' => NULL,
      'secret' => NULL,
    ),
    'ses' => 
    array (
      'key' => NULL,
      'secret' => NULL,
      'region' => 'us-east-1',
    ),
    'sparkpost' => 
    array (
      'secret' => NULL,
    ),
    'stripe' => 
    array (
      'model' => 'App\\Model\\User',
      'key' => NULL,
      'secret' => NULL,
    ),
    'firebase' => 
    array (
      'database_url' => NULL,
      'secret' => NULL,
    ),
  ),
  'session' => 
  array (
    'driver' => 'file',
    'lifetime' => 120,
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => 'C:\\xampp\\htdocs\\fleetRepo\\framework\\storage\\framework/sessions',
    'connection' => NULL,
    'table' => 'sessions',
    'store' => NULL,
    'lottery' => 
    array (
      0 => 2,
      1 => 100,
    ),
    'cookie' => 'ets_and_rac_session',
    'path' => '/',
    'domain' => NULL,
    'secure' => NULL,
    'http_only' => true,
    'same_site' => NULL,
  ),
  'tinker' => 
  array (
    'commands' => 
    array (
    ),
    'alias' => 
    array (
    ),
    'dont_alias' => 
    array (
      0 => 'App\\Nova',
    ),
  ),
  'view' => 
  array (
    'paths' => 
    array (
      0 => 'C:\\xampp\\htdocs\\fleetRepo\\framework\\resources\\views',
    ),
    'compiled' => 'C:\\xampp\\htdocs\\fleetRepo\\framework\\storage\\framework\\views',
  ),
  'webpush' => 
  array (
    'vapid' => 
    array (
      'subject' => NULL,
      'public_key' => NULL,
      'private_key' => NULL,
      'pem_file' => NULL,
    ),
    'model' => 'NotificationChannels\\WebPush\\PushSubscription',
    'table_name' => 'push_subscriptions',
    'database_connection' => 'mysql',
    'client_options' => 
    array (
    ),
    'gcm' => 
    array (
      'key' => NULL,
      'sender_id' => NULL,
    ),
  ),
);
