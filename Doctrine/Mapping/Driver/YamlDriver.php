<?php

namespace Knp\Bundle\RadBundle\Doctrine\Mapping\Driver;

use Symfony\Bridge\Doctrine\Mapping\Driver\YamlDriver as BaseYamlDriver;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\Mapping\Driver\Driver;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Common\Util\Inflector;

class YamlDriver implements Driver
{
    private $namespace;
    private $directory;
    private $mappingData;

    /**
     * Constructor
     *
     * @param  string $directory
     */
    public function __construct($namespace, $directory)
    {
        $this->namespace = $namespace;
        $this->directory = $directory;
    }

    /**
     * {@inheritdoc}
     */
    public function loadMetadataForClass($className, ClassMetadataInfo $metadata)
    {
        if (null === $this->mappingData) {
            $this->loadMappingData();
        }

        $shortName = $this->getShortName($className);

        if (!isset($this->mappingData[$shortName])) {
            throw new MappingException(sprintf(
                'No mapping found for class "%s".',
                $className
            ));
        }

        $mapping = array_merge(
            array(
                'type'              => 'entity',
                'table'             => $this->tableize($shortName),
                'readOnly'          => false,
                'repositoryClass'   => null,
            ),
            $this->mappingData[$shortName]
        );

        switch ($mapping['type'])
        {
            case 'entity':
                $metadata->setCustomRepositoryClass($mapping['repositoryClass']);
                if ($mapping['readOnly']) {
                    $metadata->markReadOnly();
                }
                break;
            case 'mappedSuperclass':
                $metadata->isMappedSuperclass = true;
                break;
            default:
                throw MappingException::classIsNotAValidEntityOrMappedSuperClass($className);
        }

        $metadata->setTableName($mapping['table']);

        // map fields
        foreach ($mapping['fields'] as $field => $fieldMapping) {
            $this->mapField($field, $fieldMapping, $metadata);
        }

        if (0 === count($metadata->getIdentifier())) {
            $metadata->mapField(array(
                'fieldName' => 'id',
                'type'      => 'integer',
                'id'        => true,
            ));
        }
    }

    private function mapField($name, $mapping, ClassMetadataInfo $metadata)
    {
        if (null === $mapping || is_scalar($mapping)) {
            $mapping = array('type' => $mapping);
        }

        $mapping = array_merge(
            array(
                'type'  => 'string',
            ),
            $mapping
        );

        list(
            $mapping['type'],
            $mapping['typeParenthese']
        ) = $this->parseParenthese($mapping['type']);

        switch ($mapping['type']) {
            case 'oneToOne':
                $this->mapOneToOneField($name, $mapping, $metadata);
                break;
            case 'oneToMany':
                $this->mapOneToManyField($name, $mapping, $metadata);
                break;
            case 'manyToOne':
                $this->mapManyToOneField($name, $mapping, $metadata);
                break;
            case 'manyToMany':
                $this->mapManyToManyField($name, $mapping, $metadata);
                break;
            default:
                $this->mapSimpleField($name, $mapping, $metadata);
        }
    }

    private function mapOneToOneField($name, array $mapping, ClassMetadataInfo $metadata)
    {
        $oneToOne = array(
            'fieldName'     => $name,
            'targetEntity'  => $mapping['typeParenthese'],
        );

        if (isset($mapping['fetch'])) {
            $oneToOne['fetch'] = constant(sprintf(
                'Doctrine\ORM\Mapping\ClassMetadata::FETCH_%s',
                $mapping['fetch']
            ));
        }

        $joinColumns = array();
        if (isset($mapping['joinColumn'])) {
            $joinColumns[] = $this->getJoinColumnMapping($mapping['joinColumn']);
        } elseif (isset($mapping['joinColumns'])) {
            foreach ($mapping['joinColumns'] as $name => $joinColumnMapping) {
                if (!isset($joinColumnMapping['name'])) {
                    $joinColumnMapping['name'] = $name;
                }
                $joinColumns[] = $this->getJoinColumnMapping($joinColumnMapping);
            }
        }
        $oneToOne['joinColumns'] = $joinColumns;


        if (isset($mapping['cascade'])) {
            $oneToOne['cascade'] = $mapping['cascade'];
        }

        if (isset($mapping['orphanRemoval'])) {
            $oneToOne['orphanRemoval'] = (Boolean) $mapping['orphanRemoval'];
        }

        $metadata->mapOneToOne($oneToOne);
    }

    private function mapOneToManyField($name, array $mapping, ClassMetadataInfo $metadata)
    {
        $oneToMany = array(
            'fieldName'     => $name,
            'targetEntity'  => $mapping['typeParenthese'],
            'mappedBy'      => $mapping['mappedBy'],
        );

        if (isset($mapping['targetEntity'])) {
            $oneToMany['targetEntity'] = $mapping['targetEntity'];
        }

        if (isset($mapping['fetch'])) {
            $oneToMany['fetch'] = constant(sprintf(
                'Doctrine\ORM\Mapping\ClassMetadata::FETCH_%s',
                $mapping['fetch']
            ));
        }

        if (isset($mapping['cascade'])) {
            $oneToMany['cascade'] = $mapping['cascade'];
        }

        if (isset($mapping['orphanRemoval'])) {
            $oneToMany['orphanRemoval'] = (Boolean) $mapping['orphanRemoval'];
        }

        if (isset($mapping['orderBy'])) {
            $oneToMany['orderBy'] = $mapping['orderBy'];
        }

        if (isset($mapping['indexBy'])) {
            $oneToMany['indexBy'] = $mapping['indexBy'];
        }

        $metadata->mapOneToMany($oneToMany);
    }

    private function mapManyToOneField($name, array $mapping, ClassMetadataInfo $metadata)
    {
        $manyToOne = array(
            'fieldName'     => $name,
            'targetEntity'  => $mapping['typeParenthese'],
        );

        if (isset($mapping['targetEntity'])) {
            $manyToOne['targetEntity'] = $mapping['targetEntity'];
        }

        if (isset($mapping['fetch'])) {
            $manyToOne['fetch'] = constant(sprintf(
                'Doctrine\ORM\Mapping\ClassMetadata::FETCH_%s',
                $mapping['fetch']
            ));
        }

        if (isset($mapping['inversedBy'])) {
            $manyToOne['inversedBy'] = $mapping['inversedBy'];
        }

        $joinColumns = array();
        if (isset($mapping['joinColumn'])) {
            $joinColumns[] = $this->getJoinColumnMapping($mapping['joinColumn']);
        } elseif (isset($mapping['joinColumns'])) {
            foreach ($mapping['joinColumns'] as $name => $joinColumnMapping) {
                if (!isset($joinColumnMapping['name'])) {
                    $joinColumnMapping['name'] = $name;
                }
                $joinColumns[] = $this->getJoinColumnMapping($joinColumnMapping);
            }
        }
        $manyToOne['joinColumns'] = $joinColumns;

        if (isset($mapping['cascade'])) {
            $manyToOne['cascade'] = $mapping['cascade'];
        }

        if (isset($mapping['orphanRemoval'])) {
            $manyToOne['orphanRemoval'] = (Boolean) $mapping['orphanRemoval'];
        }

        $metadata->mapManyToOne($manyToOne);
    }

    private function mapManyToManyField($name, array $mapping, ClassMetadataInfo $metadata)
    {
        $manyToMany = array(
            'fieldName'     => $name,
            'targetEntity'  => $mapping['typeParenthese'],
        );

        if (isset($mapping['targetEntity'])) {
            $manyToOne['targetEntity'] = $mapping['targetEntity'];
        }

        if (isset($mapping['fetch'])) {
            $manyToOne['fetch'] = constant(sprintf(
                'Doctrine\ORM\Mapping\ClassMetadata::FETCH_%s',
                $mapping['fetch']
            ));
        }

        if (isset($mapping['mappedBy'])) {
            $manyToMany['mappedBy'] = $mapping['mappedBy'];
        } else if (isset($mapping['joinTable'])) {
            if (isset($mapping['inversedBy'])) {
                $manyToMany['inversedBy'] = $mapping['inversedBy'];
            }

            $joinTableMapping = $mapping['joinTable'];
            $joinTable = array(
                'name' => $joinTableMapping['name']
            );

            if (isset($joinTableMapping['schema'])) {
                $joinTable['schema'] = $joinTableMapping['schema'];
            }

            foreach ($joinTableMapping['joinColumns'] as $name => $joinColumnMapping) {
                if (!isset($joinColumnMapping['name'])) {
                    $joinColumnMapping['name'] = $name;
                }

                $joinTable['joinColumns'][] = $this->getJoinColumnMapping($joinColumnMapping);
            }

            foreach ($joinTableMapping['inverseJoinColumns'] as $name => $joinColumnMapping) {
                if (!isset($joinColumnMapping['name'])) {
                    $joinColumnMapping['name'] = $name;
                }

                $joinTable['inverseJoinColumns'][] = $this->getJoinColumnMapping($joinColumnMapping);
            }

            $mapping['joinTable'] = $joinTable;
        }

        if (isset($mapping['cascade'])) {
            $manyToMany['cascade'] = $mapping['cascade'];
        }

        if (isset($mapping['orphanRemoval'])) {
            $manyToMany['orphanRemoval'] = (Boolean) $mapping['orphanRemoval'];
        }

        if (isset($mapping['orderBy'])) {
            $manyToMany['orderBy'] = $mapping['orderBy'];
        }

        if (isset($mapping['indexBy'])) {
            $manyToMany['indexBy'] = $mapping['indexBy'];
        }

        $metadata->mapManyToMany($manyToMany);
    }

    private function mapSimpleField($name, array $mapping, ClassMetadataInfo $metadata)
    {
        $field = array(
            'fieldName'     => $name,
            'type'          => $mapping['type'],
        );

        if (isset($mapping['id'])) {
            $field['id'] = true;
            if (isset($mapping['generator']['strategy'])) {
                $metadata->setIdGeneratorType(constant(sprintf(
                    'Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_%s',
                    strtoupper($mapping['generator']['strategy'])
                )));
            }
        }

        if (empty($mapping['column'])) {
            $mapping['column'] = $this->tableize($name);
        }

        $field['columnName'] = $mapping['column'];

        if (isset($mapping['typeParenthese'])) {
            $field['length'] = $mapping['typeParenthese'];
        }

        if (isset($mapping['length'])) {
            $field['length'] = $mapping['length'];
        }

        if (isset($mapping['precision'])) {
            $field['precision'] = $mapping['precision'];
        }

        if (isset($mapping['scale'])) {
            $field['scale'] = $mapping['scale'];
        }

        if (isset($mapping['unique'])) {
            $field['unique'] = (Boolean) $mapping['unique'];
        }

        if (isset($mapping['options'])) {
            $field['options'] = $mapping['options'];
        }

        if (isset($mapping['nullable'])) {
            $field['nullable'] = (Boolean) $mapping['nullable'];
        }

        if (isset($mapping['version']) && $mapping['version']) {
            $metadata->setVersionMapping($field);
        }

        if (isset($mapping['columnDefinition'])) {
            $field['columnDefinition'] = $mapping['columnDefinition'];
        }

        $metadata->mapField($field);
    }

    private function getJoinColumnMapping(array $mapping)
    {
        $joinColumn = array(
            'name'                  => $mapping['name'],
            'referencedColumnName'  => $mapping['referencedColumnName']
        );

        if (isset($mapping['fieldName'])) {
            $joinColumn['fieldName'] = (string) $mapping['fieldName'];
        }

        if (isset($mapping['unique'])) {
            $joinColumn['unique'] = (Boolean) $mapping['unique'];
        }

        if (isset($mapping['nullable'])) {
            $joinColumn['nullable'] = (Boolean) $mapping['nullable'];
        }

        if (isset($mapping['onDelete'])) {
            $joinColumn['onDelete'] = $mapping['onDelete'];
        }

        if (isset($mapping['onUpdate'])) {
            $joinColumn['onUpdate'] = $mapping['onUpdate'];
        }

        if (isset($mapping['columnDefinition'])) {
            $joinColumn['columnDefinition'] = $mapping['columnDefinition'];
        }

        return $joinColumn;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllClassNames()
    {
        if (null === $this->mappingData) {
            $this->loadMappingData();
        }

        return array_map(
            array($this, 'getClassName'),
            array_keys($this->mappingData)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isTransient($className)
    {
        if (null === $this->mappingData) {
            $this->loadMappingData();
        }

        $shortName = $this->getShortName($className);

        return isset($this->mappingData[$shortName]);
    }

    protected function getClassName($shortName)
    {
        return sprintf('%s\%s', $this->namespace, $shortName);
    }

    protected function getShortName($className)
    {
        $prefix = sprintf('%s\\', $this->namespace);

        if (0 !== strpos($className, $prefix)) {
            throw new \InvalidArgumentException(sprintf(
                'The specified "%s" class is out of the "%s" namespace.',
                $className,
                $this->namespace
            ));
        }

        return substr($className, strlen($prefix));
    }

    private function loadMappingData()
    {
        $this->mappingData = array();

        $finder = Finder::create()->files()->name('*.yml')->in($this->directory);
        foreach ($finder as $file) {
            $this->mappingData = array_merge(
                Yaml::parse($file->getPathname()),
                $this->mappingData
            );
        }
    }

    private function tableize($className)
    {
        return Inflector::tableize(str_replace('\\', '', $className));
    }

    private function parseParenthese($string)
    {
        if (0 === strlen($string)) {
            return array(null, null);
        }

        $posOpen  = strpos($string, '(');
        $posClose = strpos($string, ')');
        $posDiff  = $posClose - $posOpen;

        if (false === $posOpen || false === $posClose || $posOpen > $posClose) {
            return array($string, null);
        }

        return array(
            0 === $posOpen ? null : substr($string, 0, $posOpen),
            1 === $posClose - $posOpen ? null : substr($string, $posOpen + 1, $posDiff - 1)
        );
    }
}
