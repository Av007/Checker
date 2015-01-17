<?php

namespace Checker\Modules;

use Checker\Services;

class Memcache
{
    /** @var array $output output format */
    protected $output = array();

    public function __construct()
    {
        $classname = __CLASS__;

        if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) {
            $classname = $matches[1];
        }

        $this->output['title'] = $classname;
        $this->output['operation'] = 'check connection';
    }

    public function init()
    {
        return $this->output;
    }

    public function run()
    {
        $result = array();
        $key = 'test_memcache_' . substr(md5(uniqid(rand(), true)), 12, 12);
        $value = 'this is test value';
        $memcacheService = new Services\Memcache();
        $memcacheService->store($key, $value);

        $result[] = ($memcacheService->fetch($key) == $value);

        $memcacheService->remove($key);
        $result[] = ($memcacheService->fetch($key) === null);

        $memcacheService->store($key, $value);
        $memcacheService->flush();
        $result[] = ($memcacheService->fetch($key) === null);

        $this->output['result'] = in_array(false, $result, true) ? 'False' : 'Ok';

        return $this->output;
    }
}
