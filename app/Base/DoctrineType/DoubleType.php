<?php
namespace App\Base\DoctrineType;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Acelaya\Enum\Action;

class DoubleType extends Type
{        
    /**
     * Gets the name of this type.
     *
     * @return string
     */
    public function getName()
    {
        return 'double';
    }
    
    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array $fieldDeclaration The field declaration.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The currently used database platform.
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'DOUBLE';
    }
    
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return (float) $value;
    }
    
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return (float) $value;
    }
}