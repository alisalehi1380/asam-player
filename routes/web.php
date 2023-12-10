<?php

use Illuminate\Support\Facades\Route;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Streaming\FFMpeg;

//Route::get('/', function () {
//    $config = [
//        'ffmpeg.binaries'  => public_path('ffmpeg/bin/ffmpeg.exe'),
//        'ffprobe.binaries' => public_path('ffmpeg/bin/ffprobe.exe'),
//        'timeout'          => 3600, // The timeout for the underlying process
//        'ffmpeg.threads'   => 12,   // The number of threads that FFmpeg should use
//    ];
//    $log = new Logger('FFmpeg_Streaming');
//    $log->pushHandler(new StreamHandler(public_path('ffmpeg-streaming.log')));
//    $ffmpeg = FFMpeg::create($config, $log);
//
//    $video = $ffmpeg->open(public_path('video/nature.mp4'));
////    dd($video);
//
//    $save_to = public_path('video/hls/stream.key');
//    $url = asset('video/hls/stream.key');
//    $video->hls()
//        ->encryption($save_to, $url, 10)
//        ->x264()
//        ->autoGenerateRepresentations([720])
//        ->save(public_path('video/hls/nature/stream.m3u8'));
//});
//
//Route::get('/safe-player', function () {
//    return view('safe-player', ['video_src' => asset('video/hls/nature/stream.m3u8')]);
//});

Route::get('/', function () {
    echo 'hello';
});
