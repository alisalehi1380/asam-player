<?php

use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\Route;
use ProtoneMedia\LaravelFFMpeg\Exporters\HLSVideoFilters;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

Route::get('/', function () {
    $config = [
        'ffmpeg.binaries'  => public_path('ffmpeg/bin/ffmpeg.exe'),
        'ffprobe.binaries' => public_path('ffmpeg/bin/ffprobe.exe'),
        'timeout'          => 3600, // The timeout for the underlying process
        'ffmpeg.threads'   => 12,   // The number of threads that FFmpeg should use
    ];
    $log = new Logger('FFmpeg_Streaming');
    $log->pushHandler(new StreamHandler(public_path('ffmpeg-streaming.log')));
    $ffmpeg = \Streaming\FFMpeg::create($config, $log);

    $video = $ffmpeg->open(storage_path('uploads/nature.mkv'));
//    dd($video);

    $save_to = storage_path('secrets/stream.key');
    $url = url('storage/secrets/stream.key');
    $video->hls()
        ->encryption($save_to, $url, 10)
        ->x264()
        ->autoGenerateRepresentations([360])
        ->save(storage_path('apvideos/stream.m3u8'));
});




//Route::get('/convert', function () {
//    $lowBitrate = (new X264())->setKiloBitrate(250);
//    $midBitrate = (new X264)->setKiloBitrate(500);
//    $highBitrate = (new X264)->setKiloBitrate(1000);
//
//    FFMpeg::fromDisk('uploads')
//        ->open('nature.mkv')
//        ->exportForHLS()
//        ->setSegmentLength(10)
//        ->withRotatingEncryptionKey(function ($filename, $contents) {
//            Storage::disk('secrets')->put($filename, $contents);
//        })
//        ->addFormat($lowBitrate, function (HLSVideoFilters $filters) {
//            $filters->resize(1280, 720);
//        })
//        //        ->addFormat($midBitrate)
//        //        ->addFormat($highBitrate)
//        ->toDisk('public')
//        ->save('videos/nature.m3u8');
//});

Route::get('/video/playlist/{playlist}', function ($playlist) {
    return FFMpeg::dynamicHLSPlaylist()
        ->fromDisk('public')
        ->open("videos/{$playlist}")
        ->setKeyUrlResolver(function ($key) {
            return route('video.key', ['key' => $key]);
        })
        ->setPlaylistUrlResolver(function ($playlist) {
            return route('video.playlist', ['playlist' => $playlist]);
        })
        ->setMediaUrlResolver(function ($segment) {
            return Storage::disk('public')->url("videos/{$segment}");
        });
})->name('video.playlist');

Route::get('/video/key/{key}', function ($key) {
//    Auth::check() ? Storage::disk('secrets')->download($key) : abort(403);
        return storage_path('secrets/FFM/')->download($key);
})->name('video.key');


Route::get('/safe-player', function () {
    //        return view('safe-player', ['video_src' => 'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8']);
    return view('safe-player');
});
