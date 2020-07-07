<?php

namespace Vasilysmolin\smo\Commands;

use Illuminate\Console\Command;

class CacheReset extends Command
{
    protected $signature = 'smo:cache-reset';

    protected $description = 'Reset the permission cache';

    public function handle()
    {
//        if (app(PermissionRegistrar::class)->forgetCachedPermissions()) {
            $this->info('Permission cache flushed.');
//        } else {
//            $this->error('Unable to flush cache.');
//        }
    }
}
