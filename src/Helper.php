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
            $content = (isset($content[$name]) and $content[$name]) ? $content[$name] : null;

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
        return ' ____                   _         _
/ ___| _ __   ___  __ _| | ____ _| |__   ___   ___  ___
\___ \| \'_ \ / _ \/ _` | |/ / _` | \'_ \ / _ \ / _ \/ __|
 ___) | |_) |  __/ (_| |   < (_| | |_) | (_) | (_) \__ \
|____/| .__/ \___|\__,_|_|\_\__,_|_.__/ \___/ \___/|___/
      |_|
';
    }

    /**
     * Creates file
     *
     * @param string $filename
     * @param string $content
     * @throws \Exception
     */
    protected static function fileWrite($filename, &$content)
    {
        if (!is_writable($filename)) {
            if (!chmod($filename, 0666)) {
                throw new \Exception('Cannot change the mode of file ' . $filename);
            }
        }
        if (!$fp = @fopen($filename, 'w')) {
            throw new \Exception('Cannot open file ' . $filename);
        }
        if (fwrite($fp, $content) === false) {
            throw new \Exception('Cannot write to file ' . $filename);
            exit;
        }
        if (!fclose($fp)) {
            throw new \Exception('Cannot close file ' . $filename);
        }
    }
}
