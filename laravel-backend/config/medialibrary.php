<?php

return [

    /*
     * The disk on which to store added files and derived images by default. Choose
     * one or more of the disks you've configured in config/filesystems.php.
     */
    'disk_name' => env('MEDIA_DISK', 'public'),

    /*
     * The maximum file size of an addition in bytes.
     * If not set the file size is unlimited.
     */
    'max_file_size' => 1024 * 1024 * 10,

    /*
     * The fully qualified class name of the media model.
     */
    'media_model' => Spatie\MediaLibrary\MediaCollections\Models\Media::class,

    /*
     * The fully qualified class name of the model used for temporary uploads.
     */
    'temporary_upload_model' => Spatie\MediaLibrary\MediaCollections\Models\Media::class,

    /*
     * When enabled, media collections will be serialised using the media library.
     * This can be useful when using media collections in Nova.
     */
    'enable_vapor' => false,

    /*
     * When converting Media instances to response, media library will add
     * a `loading` attribute to the `img` tag. Here you can set the default value
     * of that attribute. Possible values: 'auto', 'eager', 'lazy' and null.
     */
    'default_loading_attribute_value' => 'lazy',

    /*
     * This is the class that is responsible for naming conversion files. By default,
     * it will use the filename of the original and concatenate the conversion name to it.
     */
    'conversion_file_namer' => \Spatie\MediaLibrary\Conversions\DefaultConversionFileNamer::class,

    /*
     * This is the class that is responsible for preparing the media library database.
     */
    'path_generator' => \Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator::class,

    /*
     * Here you can specify which path generator should be used when urls
     * to files get generated.
     */
    'url_generator' => \Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator::class,

    's3' => [
        /*
         * The domain that should be prepended when generating urls.
         */
        'domain' => 'https://xxxxxxx.s3.amazonaws.com',
    ],

    'remote' => [
        /*
         * Any extra headers that should be included when uploading media to
         * a remote disk. Even though supported headers may vary between
         * different drivers, a sensible default has been provided.
         *
         * Supported by S3: CacheControl, Expires, StorageClass,
         * ServerSideEncryption, Metadata, ACL, ContentEncoding
         */
        'extra_headers' => [
            'CacheControl' => 'max-age=604800',
        ],
    ],

    'responsive_images' => [
        /*
         * This class is responsible for calculating the target widths of the responsive
         * images. By default we optimize for filesizes and create variations that each are 10%
         * smaller than the previous one. More info in the documentation.
         *
         * https://docs.spatie.be/laravel-medialibrary/v9/advanced-usage/generating-responsive-images
         */
        'width_calculator' => \Spatie\MediaLibrary\ResponsiveImages\WidthCalculator\FileSizeOptimizedWidthCalculator::class,

        /*
         * By default rendering media to a responsive image is turned off. Set this to true
         * to turn it on globally.
         */
        'use_tiny_placeholders' => true,

        /*
         * This class will generate the tiny placeholder used for progressive image loading. By default
         * the media library will use a tiny blurred jpg image.
         */
        'tiny_placeholder_generator' => \Spatie\MediaLibrary\ResponsiveImages\TinyPlaceholderGenerator\Blurred::class,
    ],

    /*
     * When urls to files get generated, this class will be called. Use the default
     * if your files are stored locally above the site root or on s3.
     */
    'default_path_generator' => \Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator::class,

    /*
     * Medialibrary will try to optimize all converted images by removing
     * metadata and applying a little bit of compression. These are
     * the optimizers that will be used by default.
     */
    'image_optimizers' => [
        Spatie\ImageOptimizer\Optimizers\Jpegoptim::class => [
            '-m85', // set maximum quality to 85%
            '--force', // ensure that the image will be overwritten
            '--strip-all',  // this strips out all text information such as comments and EXIF data
            '--all-progressive', // this will make sure the resulting image is a progressive one
        ],

        Spatie\ImageOptimizer\Optimizers\Pngquant::class => [
            '--force', // required parameter for this package
        ],

        Spatie\ImageOptimizer\Optimizers\Optipng::class => [
            '-i0', // this will result in a non-interlaced, progressive scanned image
            '-o2', // this set the optimization level to two (multiple IDAT compression trials)
            '-quiet', // required parameter for this package
        ],

        Spatie\ImageOptimizer\Optimizers\Svgo::class => [
            '--disable=cleanupIDs', // disabling because it is known to cause troubles
        ],

        Spatie\ImageOptimizer\Optimizers\Gifsicle::class => [
            '-b', // required parameter for this package
            '-O3', // this produces the slowest but best results
        ],

        Spatie\ImageOptimizer\Optimizers\Cwebp::class => [
            '-m', '6', // for the slowest compression method in order to get the best compression.
            '-pass', '10', // for maximizing the amount of analysis pass.
            '-mt', // multithreading for some speed improvements.
            '-q', '90', //quality factor that brings the least noticeable changes.
        ],
    ],

    /*
     * These generators will be used to create an image of media files.
     */
    'image_generators' => [
        Spatie\MediaLibrary\Conversions\ImageGenerators\Image::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Webp::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Pdf::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Svg::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Video::class,
    ],

    /*
     * The class that contains the strategy for determining a media file's type.
     */
    'media_type_determiner' => \Spatie\MediaLibrary\MediaTypes\MediaTypesDeterminer::class,

    /*
     * The engine that should perform the image conversions.
     * Should be either `gd` or `imagick`.
     */
    'image_driver' => env('IMAGE_DRIVER', 'gd'),

    /*
     * FFMPEG & FFProbe binaries paths, only used if you try to generate video
     * thumbnails and have installed the php-ffmpeg/php-ffmpeg composer package.
     */
    'ffmpeg_path' => env('FFMPEG_PATH', '/usr/bin/ffmpeg'),
    'ffprobe_path' => env('FFPROBE_PATH', '/usr/bin/ffprobe'),

    /*
     * Here you can override the class names of the jobs used by this package. Make sure
     * your custom jobs extend the ones provided by the package.
     */
    'jobs' => [
        'perform_conversions' => \Spatie\MediaLibrary\Conversions\Jobs\PerformConversionsJob::class,
        'generate_responsive_images' => \Spatie\MediaLibrary\ResponsiveImages\Jobs\GenerateResponsiveImagesJob::class,
    ],

    /*
     * When using the addMediaFromUrl method you may want to replace the default downloader.
     * This is particularly useful when the url of the image is behind a paywall or
     * protection.
     */
    'media_downloader' => \Spatie\MediaLibrary\Downloaders\DefaultDownloader::class,

    'remote_path_generator' => \Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator::class,

    /*
     * The class that contains the strategy for determining how to add uploads
     * to the media library.
     */
    'file_adder' => \Spatie\MediaLibrary\FileAdder\FileAdder::class,

    /*
     * The class that contains the strategy for naming files.
     */
    'file_namer' => \Spatie\MediaLibrary\Support\FileNamer\DefaultFileNamer::class,

    /*
     * The class that contains the strategy for determining the human readable
     * file name.
     */
    'file_name_cleaner' => \Spatie\MediaLibrary\Support\FileNamer\FileNameCleaner::class,

];