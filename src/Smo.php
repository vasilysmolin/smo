<?php

namespace Vasilysmolin\smo;

class Smo
{
    public function greet(String $name): string
    {

        $default = config('auth');
        dd($default);
        return 'HI ' . $name ;
    }
}
