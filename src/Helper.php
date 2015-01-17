<?php
namespace Checker;

use Herrera\Phar\Update\Exception\Exception;

class Helper
{
    const configFilePath = '/config.ini';

    /**
     * Gets Config
     *
     * @return array
     */
    public static function getConfig()
    {
        $configPath = dirname(\Phar::running(false)) . self::configFilePath;
        if (!file_exists($configPath)) {
            $configPath = __DIR__ . self::configFilePath;
        }

        return parse_ini_file($configPath);
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
            $writer  = new \Zend\Config\Writer\Ini();
            $reader  = new \Zend\Config\Reader\Ini();
            $content = $reader->fromFile(__DIR__ . self::configFilePath);
            $content = (isset($content[$name]) && $content[$name]) ? $content[$name] : null;

            return $writer->toString($content);

        } else {
            $content = file_get_contents(__DIR__ . self::configFilePath);
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
        $reader  = new \Zend\Config\Reader\Ini();
        $content = $reader->fromFile(__DIR__ . self::configFilePath);
        $separator = explode('.', $name);

        $content[$separator[0]][$separator[0]][$separator[1]] = $value;

        $writer  = new \Zend\Config\Writer\Ini();
        return @file_put_contents(__DIR__ . self::configFilePath, $writer->toString($content));
    }

    public static function showItemConfig()
    {
        $sections = parse_ini_file(__DIR__ . self::configFilePath, true);
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
}
