<?php

use App\Providers\{
    AppServiceProvider,
    BindServiceProvider,
    EventServiceProvider,
    MacroServiceProvider,
    ScrambleServiceProvider,
};

return [
    AppServiceProvider::class,
    BindServiceProvider::class,
    MacroServiceProvider::class,
    EventServiceProvider::class,
    ScrambleServiceProvider::class,
];
