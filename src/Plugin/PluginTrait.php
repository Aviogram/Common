<?php
namespace Aviogram\Common\Plugin;

trait PluginTrait
{
    /**
     * Basic functionality works with this array. Uses a <alias> => <className> format
     *
     * @var array
     */
    protected $plugins;

    /**
     * Checks if the given plugin implements the correct parent classes/interfaces
     *
     * @param  string $class
     *
     * @return boolean
     */
    abstract protected function isCorrectPlugin($class);

    /**
     * Returns the class name for the given name
     *
     * @param  string $name
     *
     * @return string | boolean FALSE when the name could not be found
     */
    protected function getPluginClassByName($name)
    {
        if (array_key_exists($name, $this->plugins) === false) {
            return false;
        }

        return $this->plugins[$name];
    }

    /**
     * Method for fetching a singleton instance of a plugin
     *
     * @param  string   $name
     * @param  callable $constructClosure
     *
     * @return object
     */
    protected function getPlugin($name, callable $constructClosure = null)
    {
        static $cache = array();

        // Check if the plugin is already constructed
        if (array_key_exists($name, $cache) === true) {
            return $cache[$name];
        }

        // Fetch the full className
        $class = $this->getPluginClassByName($name);

        if ($class === false) {
            throw new Exception\PluginNotFound("Plugin with name `{$name}` does not exists");
        }

        // Check if the plugin is in the correct instance
        if ($this->isCorrectPlugin($class) === false) {
            throw new Exception\PluginNotCreated("Plugin with name `{$name}` does not exists");
        }

        // No closure given we just construct the class
        if ($constructClosure === null) {
            return $cache[$name] = new $class();
        }

        $object = $constructClosure($class);
        if (is_object($object) === false) {
            throw new Exception\PluginNotCreated(
                "Plugin with name `{$name}` could not be created, because callable did not return an object."
            );
        }

        return $cache[$name] = $object;
    }
}
