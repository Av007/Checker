<?php
namespace Checker;

class Factory
{
    public static function build($type)
    {
        $class = 'Checker\\Modules\\' . ucfirst($type);
        if (!class_exists($class)) {
            throw new \Exception('Missing format class.');
        }

        return new $class;
    }
}
