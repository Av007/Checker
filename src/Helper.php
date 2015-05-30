<?php

namespace Checker;

use \Zend\Config\Writer\Ini as IniWriter;
use \Zend\Config\Reader\Ini as IniReader;

class Helper
{
    const CONFIG_FILE = '/config.ini';

    /**
     * Gets Config
     *
     * @return array
     */
    public static function getConfig()
    {
        return parse_ini_file(self::getConfigPath());
    }

    /**
     * Extract file from phar
     *
     * @throws \Exception
     * @return int
     */
    public static function extractConfig($name = null)
    {
        if ($name) {
            $writer  = new IniWriter();
            $reader  = new IniReader();
            $content = $reader->fromFile(self::getConfigPath());
            $content = (isset($content[$name]) && $content[$name]) ? $content[$name] : null;

            return $writer->toString($content);

        } else {
            $content = file_get_contents(self::getConfigPath());
        }

        return $content;
    }

    /**
     * Extract file from phar
     *
     * @param string $name
     * @param string $value
     * @throws \Exception
     * @return int
     */
    public static function updateConfig($name, $value)
    {
        $configPath = self::getConfigPath();
        $reader  = new IniReader();
        $content = $reader->fromFile($configPath);
        $separator = explode('.', $name);

        $content[$separator[0]][$separator[0]][$separator[1]] = $value;

        $writer  = new IniWriter();

        return @file_put_contents($configPath, $writer->toString($content));
    }

    public static function showItemConfig()
    {
        $sections = parse_ini_file(self::getConfigPath(), true);
        return array_keys($sections);
    }

    /**
     * Gets console logo
     *
     * @return string
     */
    public static function getLogo()
    {
        return '  ____ _               _
 / ___| |__   ___  ___| | _____ _ __
| |   | \'_ \ / _ \/ __| |/ / _ \ \'__|
| |___| | | |  __/ (__|   <  __/ |
 \____|_| |_|\___|\___|_|\_\___|_|

';
    }

    /**
     * Gets config file path
     *
     * @return string
     */
    protected static function getConfigPath()
    {
        $configPath = dirname(\Phar::running(false)) . self::CONFIG_FILE;
        if (!file_exists($configPath)) {
            $configPath = __DIR__ . self::CONFIG_FILE;
        }

        return $configPath;
    }
}
