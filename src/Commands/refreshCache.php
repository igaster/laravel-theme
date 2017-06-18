<?php namespace Igaster\LaravelTheme\Commands;

use Illuminate\Console\Command;

class refreshCache extends baseCommand
{
    protected $signature = 'theme:refresh-cache';
    protected $description = 'Rebuilds the cache of "theme.json" files for each theme';

    public function handle() {
        // Rebuild Themes Cache
        \Theme::rebuildCache();

        $this->info("Themes cache was refreshed. Currently theme caching is: ".(\Theme::cacheEnabled() ? "ENABLED" : "DISABLED"));
    }




}
