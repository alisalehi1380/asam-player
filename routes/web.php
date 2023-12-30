<?php

use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\Route;
use ProtoneMedia\LaravelFFMpeg\Exporters\HLSVideoFilters;
use ProtoneMedia\LaravelFFMpeg\Filters\WatermarkFactory;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

//Route::get('/1', function () {
//    $config = [
//        'ffmpeg.binaries' => public_path('ffmpeg/bin/ffmpeg.exe'),
//        'ffprobe.binaries' => public_path('ffmpeg/bin/ffprobe.exe'),
//        'timeout' => 3600, // The timeout for the underlying process
//        'ffmpeg.threads' => 12,   // The number of threads that FFmpeg should use
//    ];
//    $log = new Logger('FFmpeg_Streaming');
//    $log->pushHandler(new StreamHandler(public_path('ffmpeg-streaming.log')));
//    $ffmpeg = \Streaming\FFMpeg::create($config, $log);
//
//    $video = $ffmpeg->open(storage_path('uploads/nature.mkv'));
//    //    dd($video);
//
//    $save_to = storage_path('secrets/1');
//    $url = 'key';
//    $video->hls()
//        ->encryption($save_to, $url, 10)
//        ->x264()
//        ->autoGenerateRepresentations([360, 480])
//        ->save(storage_path('app/public/videos/1/nature.m3u8'));
//});
//
//
//Route::get('/2', function () {
//    $lowBitrate = (new X264())->setKiloBitrate(250);
//    $midBitrate = (new X264)->setKiloBitrate(500);
//    $highBitrate = (new X264)->setKiloBitrate(1000);
//
//    FFMpeg::fromDisk('uploads')
//        ->open('nature.mkv')
//        ->exportForHLS()
//        ->setSegmentLength(15)
//        ->withRotatingEncryptionKey(function ($filename, $contents) {
//            Storage::disk('secrets')->put($filename, $contents);
//        })
//        ->addFormat($lowBitrate, function (HLSVideoFilters $filters) {
//            $filters->resize(1280, 720);
//        })
//        ->addFormat($midBitrate)
//        ->addFormat($highBitrate)
//        ->toDisk('public')
//        ->save('videos/nature.m3u8');
//});
//
//Route::get('/video/playlist/{playlist}', function ($playlist) {
//    return FFMpeg::dynamicHLSPlaylist()
//        ->fromDisk('public')
//        ->open("videos/{$playlist}")
//        ->setKeyUrlResolver(function ($key) {
//            return route('video.key', ['key' => $key]);
//        })
//        ->setPlaylistUrlResolver(function ($playlist) {
//            return route('video.playlist', ['playlist' => $playlist]);
//        })
//        ->setMediaUrlResolver(function ($segment) {
////            Auth::check() ? Storage::disk('public')->url("videos/{$segment}") : abort(403);
//            return Storage::disk('public')->url("videos/{$segment}");
//        });
//})->name('video.playlist');
//
//Route::get('/video/key/{key}', function ($key) {
////        Auth::check() ? Storage::disk('secrets')->download($key) : abort(403);
//    return Storage::disk('secrets')->download($key);
//})->name('video.key');

//---------------------------------------
Route::get('/ali', function () {
    return 'ali';
});
Route::get('/createHLS', function () {

    $lowBitrate = (new X264)->setKiloBitrate(250);
    $midBitrate = (new X264)->setKiloBitrate(500);
    $highBitrate = (new X264)->setKiloBitrate(1000);

    FFMpeg::fromDisk('public')
        ->open('videos/nature.mp4')
        ->addWatermark(function (WatermarkFactory $watermark) {
            $watermark->fromDisk('public')
                ->open('videos/watermark.png')
                ->horizontalAlignment(WatermarkFactory::CENTER)
                ->verticalAlignment(WatermarkFactory::CENTER);
        });
    FFMpeg::fromDisk('public')
        ->open('videos/nature.mp4')
        ->exportForHLS()
        ->useSegmentFilenameGenerator(function ($name, $format, $key, callable $segments, callable $playlist) {
            $segments("{$name}-{$format->getKiloBitrate()}-{$key}-%03d.ts");
            $playlist("{$name}-{$format->getKiloBitrate()}-{$key}.m3u8");
        })
        ->withRotatingEncryptionKey(function ($filename, $contents) {
            Storage::disk('secrets')->put($filename, $contents);
        }, 3)
        ->setSegmentLength(10) // optional
        ->setKeyFrameInterval(48) // optional
        ->addFormat($lowBitrate)
        ->addFormat($midBitrate)
        ->addFormat($highBitrate)
        ->save('stream.m3u8');
});


Route::get('/video/secret/{key}', function ($key) {
    return Storage::disk('secrets')->download($key);
})->name('video.key');

Route::get('/video/{playlist}', function ($playlist) {
    return FFMpeg::dynamicHLSPlaylist()
        ->fromDisk('public')
        ->open($playlist)
        ->setKeyUrlResolver(function ($key) {
            return route('video.key', ['key' => $key]);
        })
        ->setMediaUrlResolver(function ($mediaFilename) {
            return Storage::disk('public')->url($mediaFilename);
        })
        ->setPlaylistUrlResolver(function ($playlistFilename) {
            return route('video.playlist', ['playlist' => $playlistFilename]);
        });
})->name('video.playlist');


Route::get('/safe-player', function () {
    return view('safe-player');
});
