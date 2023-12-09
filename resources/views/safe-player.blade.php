<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>


    <link rel="stylesheet" href="{{ asset('assets/css/plyr.css') }}">
    <style>
        :root {
            --plyr-color-main: #43d477
        }
    </style>
</head>
<body>

<script src="{{ asset('assets/js/plyr.js') }}"></script>
<script src="{{ asset('assets/js/hls.js') }}"></script>

<video id="safe-player" controls></video>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        var player;
        const videoSrc = "{{ $video_src }}";
        const video = document.getElementById('safe-player');
        const defaultOptions = {};

        if (Hls.isSupported()) {
            var hls = new Hls();
            hls.loadSource(videoSrc);
            hls.on(Hls.Events.MANIFEST_PARSED, function (event, data) {
                const availableQualities = hls.levels.map((l) => l.height)
                availableQualities.unshift(0)

                defaultOptions.quality = {
                    default: 0,
                    options: availableQualities,
                    forced: true,
                    onChange: (e) => updateQuality(e)
                }
                defaultOptions.i18n = {
                    qualityLabel: {
                        0: "Auto"
                    },
                }

                player = new Plyr(video, defaultOptions);
            });
            hls.attachMedia(video);
            window.hls = hls;
        }

        function updateQuality(newQuality) {
            if (newQuality === 0) {
                window.hls.currentLevel = 0
            } else {
                window.hls.levels.forEach((level, levelIndex) => {
                    if (level.height === newQuality) {
                        window.hls.currentLevel = levelIndex;
                    }
                })
            }
        }
    });
</script>
</body>
</html>
