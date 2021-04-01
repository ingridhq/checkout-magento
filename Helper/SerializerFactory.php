<?php
declare(strict_types=1);

namespace Ingrid\Checkout\Helper;

use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class SerializerFactory {

    /**
     * @return Serializer
     */
    public static function create() {
        /** @noinspection PhpDeprecationInspection */
        AnnotationRegistry::registerLoader('class_exists');
        return SerializerBuilder::create()->build();
    }
}
