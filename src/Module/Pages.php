<?php

namespace Checker\Modules;

class Pages
{
    /** @var array $output output format */
    protected $output = array();
    /** @var array $config */
    protected $config;

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
        $this->config = \Checker\Helper::getConfig();
        return $this->output;
    }

    public function run()
    {
        $result = array();

        foreach (array($this->config['website.page.0'], $this->config['website.page.1']) as $page) {
            $ch = curl_init($this->config['website.url'] . $page);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $result[] = ($httpcode == 200) ? true : false;
        }

        $this->output['result'] = in_array(false, $result, true) ? 'False' : 'Ok';

        return $this->output;
    }
}
