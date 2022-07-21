<?php

namespace Syntro\SilverShare\Dev;

use SilverStripe\ORM\DataObject;
use SilverStripe\Dev\TestOnly;
use Syntro\SilverShare\Extension\ShareExtension;

/**
 * A test dataobject which allows the extension-test
 *
 * @author Matthias Leutenegger <hello@syntro.ch>
 */
class SharedObject extends DataObject implements TestOnly
{
    /**
     * @config
     */
    private static $sharing_allow_user_overwrite = true;

    /**
     * @config
     */
    private static $sharing_fallback_description = [
        'Description',
        'getDefaultDescription'
    ];

    /**
     * Database fields
     * @config
     * @var array
     */
    private static $db = [
        'Title' => 'Varchar',
        'Description' => 'Text'
    ];

    /**
     * Defines extension names and parameters to be applied
     *  to this object upon construction.
     * @config
     *  @var array
     */
    private static $extensions = [
        ShareExtension::class,
    ];

    /**
     * getDefaultDescription
     *
     * @return string
     */
    public function getDefaultDescription()
    {
        return 'someString';
    }
}
