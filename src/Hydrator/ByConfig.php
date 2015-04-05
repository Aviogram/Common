<?php
namespace Aviogram\Common\Hydrator;

use Aviogram\Common\ArrayUtils;

class ByConfig
{
    /**
     * @var array
     */
    private static $exceptions = array(
        1 => 'Cannot add include `%s`, because data provided is not an array but an `%s`.',
    );

    /**
     * Hydrate array data to an object based on the configuration given
     *
     * @param array $data
     * @param ByConfigBuilder $config
     *
     * @return object | \ArrayIterator on a collection
     */
    public static function hydrate(array $data, ByConfigBuilder $config)
    {
        // When entity is not available. Only use the collection to set the data
        if ($config->getEntity() === false) {
            return $config->getCollectionEntity($data);
        }

        // When there is no collection specified. Make sure there will be an array to iterate through
        if ($config->isCollection() === false) {
            $data = array($data);
        }

        $rows = array();

        // Loop through the data
        foreach ($data as $index => $row) {
            $rows[$index] = $entity = $config->getEntity();

            foreach ($row as $field => $value) {
                // Skip field if not defined in the configuration
                if ($config->hasField($field) === false) {
                    $catchAll = $config->getCatchAllSetter();

                    if ($catchAll !== null) {
                        call_user_func(array($entity, $catchAll), $field, $value);
                    }

                    continue;
                }

                $callable = array($entity, $config->getSetter($field));

                if ($config->isInclude($field) === true) {
                    if (is_array($value) === false) {
                        throw new Exception\HydrateFailed(sprintf(static::$exceptions[1], $field, gettype($value)));
                    }

                    $value = static::hydrate($value, $config->getInclude($field));
                } else if ($config->getFormatter($field) !== null) {
                    $value = call_user_func($config->getFormatter($field), $value);
                }

                // Add the value
                call_user_func($callable, $value);
            }
        }

        return ($config->isCollection() === true) ? $config->getCollectionEntity($rows) : $entity;
    }

    /**
     * Get a config builder
     *
     * @param  string $entityClassName
     * @param  bool   $isCollection
     * @param  string $collectionClassName  (Should extend ArrayIterator class)
     *
     * @return ByConfigBuilder
     */
    public static function getConfigBuilder($entityClassName, $isCollection = false, $collectionClassName = 'ArrayIterator')
    {
        return new ByConfigBuilder($entityClassName, $isCollection, $collectionClassName);
    }

    /**
     * Builds an config based on the array given
     * array(
     *  'entity'            => 'full_class_name',
     *  'is_collection'     => true | false,
     *  'collection_entity' => 'full_class_name',
     *  'fields' => array(
     *      'fieldOne' => array(
     *          'getter'  => 'getFieldOne',
     *          'setter'  => 'setFieldOne',
     *      ),
     *      'fieldTwo' => array(
     *          'getter'  => 'getFieldTwo',
     *          'setter'  => 'setFieldTwo',
     *          'include' => array(
     *              'entity' => 'full_class_name',
     *              'fields' => array(
     *                  'fieldThree' => array(
     *                      'getter' => 'getFieldThree',
     *                      'setter' => 'setFieldThree',
     *                  ),
     *              ),
     *          ),
     *      ),
     *  ),
     * );
     *
     * @param array $config
     *
     * @return ByConfigBuilder
     *
     * @throws Exception\ConfigFailed When the field definition is not an array
     * @throws Exception\ConfigFailed When the include definition is not an array
     * @throws Exception\ConfigFailed When the entity class does not exists
     * @throws Exception\ConfigFailed When the entity class does not contain the getMethod
     * @throws Exception\ConfigFailed When the entity class does not contain the setMethod
     */
    public static function createConfigByArray(array $config)
    {
        $entity           = ArrayUtils::targetGet('entity', $config);
        $isCollection     = ArrayUtils::targetGet('entity', $config, false);
        $collectionEntity = ArrayUtils::targetGet('collection_entity', $config);
        $fields           = ArrayUtils::targetGet('fields', $config, array());

        $config = static::getConfigBuilder($entity, $isCollection, $collectionEntity);
        foreach ($fields as $fieldName => $field) {
            if (is_array($field) === false) {
                throw new Exception\ConfigFailed('ArrayConfig is invalid. Field definition should be an array.', 1);
            }

            $getter  = ArrayUtils::targetGet('getter', $field);
            $setter  = ArrayUtils::targetGet('setter', $field);
            $include = ArrayUtils::targetGet('include', $field);

            if ($include !== null) {
                if (is_array($include) === false) {
                    throw new Exception\ConfigFailed('ArrayConfig is invalid. Include definition should be an array.', 1);
                }

                $include = static::createConfigByArray($include);
            }

            $config->addField($fieldName, $getter, $setter, $include);
        }

        return $config;
    }
}
