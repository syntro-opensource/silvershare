<?php

namespace Syntro\Silvershare\Tests;

use SilverStripe\Dev\SapphireTest;
use Syntro\SilverShare\Dev\SharedObject;
use SilverStripe\ORM\DataObject;
use SilverStripe\SiteConfig\SiteConfig;

/**
 * Test the Share SiteConfig extension
 *
 * @author Matthias Leutenegger <hello@syntro.ch>
 */
class ShareSiteConfigExtensionTest extends SapphireTest
{

    /**
     * Defines the fixture file to use for this test class
     * @var string
     */
    protected static $fixture_file = './defaultfixture.yml';

    /**
     * setUp - add a siteconfig if necessary
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = DataObject::get_one(SiteConfig::class);
        if (!$config) {
            SiteConfig::make_site_config();
        }
    }

    /**
     * testCMSFields
     *
     * @return void
     */
    public function testCMSFields()
    {
        $object = SiteConfig::current_site_config();
        $fields = $object->getCMSFields()->dataFieldNames();
        $this->assertContains('OGDefaultImage', $fields);
        $this->assertContains('TwitterSite', $fields);
    }
}
