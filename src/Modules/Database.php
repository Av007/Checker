<?php

namespace Checker\Modules;

class Database
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

        try {
            $pdo = new \PDO('mysql:host=' . $this->config['db.host'] . ';dbname=' . $this->config['db.name'] . ';charset=utf8',
                $this->config['db.username'], $this->config['db.password']);

            $result[] = (bool)$pdo;

            $query = $pdo->query('SELECT * FROM subscription_plan');
            $dbResult = $query->fetchAll(\PDO::FETCH_ASSOC);
            $result[] = count($dbResult) > 0;
        } catch(\Exception $e) {
            $result[] = false;
        }

        $this->output['result'] = in_array(false, $result, true) ? 'False' : 'Ok';

        return $this->output;
    }
}
