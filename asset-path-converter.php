<?php

class work
{
    public $arr = [];

    public function getfiles($path)
    {
        $files = scandir($path);
        for ($i = 2; $i < count($files); $i++) {
            if (is_dir($path . "/" . $files[$i]))
                $this->getfiles($path . "/" . $files[$i]);
            else   array_push($this->arr,
                explode("../public/assets/", $path . "/" . $files[$i])[1]);
        }

    }

    public $paths = [];
    public $pattern;

    public function getstrings($file)
    {
        $matches = [];
        $re = "/(?<=['\"\\(])[^\"()\\n']*?(?=[\\)\"'])/m";
        $found = false;
        $newlines = [];
        $lines = @file($file) ?? [];
        foreach ($lines as $line) {
            foreach ($this->arr as $item) {
                if (str_contains($line, $item))
                    preg_match_all($re, $line, $matches);
            }


            foreach ($matches as $match) {
                foreach ($match as $cur) {
                    if (in_array($cur, $this->arr)) {
                        $line = str_replace($cur, "{{asset('assets/{$cur}')}}", $line);
                        $found = true;
                    }

                }
//                $match=@explode($match,`\\`);
            }
            $newlines [] = $line;
        }
        file_put_contents($file, $newlines);

        if ($found)
            dd($file);
//        $newdata = array();
//        $lookfor = 'replaceme';
//        $newtext = 'withme';
//
//        foreach ($lines as $filerow) {
//            if (strstr($filerow, $lookfor) !== false)
//                $filerow = $newtext;
//            $newdata[] = $filerow;
//        }
//

    }

    public function main()
    {
        $this->getfiles("../public/assets"); // get all asset files
        $files = scandir('../public');
        for ($i = 2; $i < count($files); $i++) {
            $cur = "../public" . $files[$i]; // put public path here
            if (!is_dir($cur) && str_contains($cur, "html"))
                $this->getstrings($cur);
        }
    }


}
