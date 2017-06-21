<?php

/*
 * ffprobe class helper for ffmpeg 0.9+ (JSON support)
 * Written by Paulo Freitas <me@paulofreitas.me> under CC BY-SA 3.0 license
 */
class ffprobe
{
    public function __construct($filename, $prettify = false)
    {
        if (!file_exists($filename)) {
            throw new Exception(sprintf('File not exists: %s', $filename));
        }

        $this->__metadata = $this->__probe($filename, $prettify);
    }

    private function __probe($filename, $prettify)
    {
        // Start time
        $init = microtime(true);

        // Default options
        $options = '-loglevel quiet -show_format -show_streams -print_format json';

        if ($prettify) {
            $options .= ' -pretty';
        }

        // Avoid escapeshellarg() issues with UTF-8 filenames
        setlocale(LC_CTYPE, 'en_US.UTF-8');

        // Run the ffprobe, save the JSON output then decode
		if (substr(php_uname(), 0, 7) == "Windows"){
			$pathFfprobe = docRoot.'plugin/media/ffprobe.exe';
		} else {
			$pathFfprobe = 'ffprobe';
		}
        $json = json_decode(shell_exec(sprintf($pathFfprobe.' %s %s', $options,
            escapeshellarg($filename))));

        if (!isset($json->format)) {
            throw new Exception('Unsupported file type');
        }

        // Save parse time (milliseconds)
        $this->parse_time = round((microtime(true) - $init) * 1000);

        return $json;
    }

    public function __get($key)
    {
        if (isset($this->__metadata->$key)) {
            return $this->__metadata->$key;
        }

        throw new Exception(sprintf('Undefined property: %s', $key));
    }
}

class ffprobe_ext extends ffprobe
{
    public function __construct($filename)
    {
        parent::__construct($filename);
    }

    public function getVideoStream()
    {
        foreach ($this->streams as $stream) {
            if ($stream->codec_type == 'video') {
                return $stream;
            }
        }
    }

    public function getVideoInfo()
    {
        $stream = $this->getVideoStream();
        $info   = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        $info->duration     = (float) $stream->duration;
        $info->frame_height = (int) $stream->height;
        $info->frame_width  = (int) $stream->width;
        eval("\$frame_rate = {$stream->r_frame_rate};");
        $info->frame_rate   = (float) $frame_rate;

        return $info;
    }
}