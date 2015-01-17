<?php

namespace Checker\Services;

class Memcache
{
    private $memcache;

    public function __construct()
    {
        if (class_exists('Memcache')) {
            $this->memcache = new \Memcache();
            $this->memcache->connect('127.0.0.1', 11211);
            return $this->memcache;
        }
    }

    public function fetch($key)
    {
        if ($this->memcache) {
            return $this->memcache->get($key);
        }
    }

    public function store($key, $value, $expiration = 0)
    {
        if ($this->memcache) {
            return $this->memcache->set($key, $value, 0, $expiration);
        }
    }

    public function remove($key)
    {
        if ($this->memcache) {
            return $this->memcache->delete($key, 0);
        }
    }

    public function flush()
    {
        if ($this->memcache) {
            return $this->memcache->flush();
        }
    }
}
