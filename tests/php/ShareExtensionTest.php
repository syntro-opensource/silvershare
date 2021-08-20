<?php

namespace Syntro\Silvershare\Tests;

use SilverStripe\Dev\SapphireTest;
use Syntro\SilverShare\Dev\SharedObject;

/**
 * Test the Share extension
 *
 * @author Matthias Leutenegger <hello@syntro.ch>
 */
class ShareExtensionTest extends SapphireTest
{

    /**
     * Defines the fixture file to use for this test class
     * @var string
     */
    protected static $fixture_file = './defaultfixture.yml';

    /**
     * testCMSFields
     *
     * @return void
     */
    public function testCMSFields()
    {
        $object = SharedObject::create();
        $fields = $object->getCMSFields()->dataFieldNames();
        $this->assertContains('OGImage', $fields);
        $this->assertContains('OGTitle', $fields);
        $this->assertContains('OGDescription', $fields);
        $this->assertNotContains('OGType', $fields);
        $this->assertNotContains('TwitterType', $fields);

        $object->config()->set('sharing_available_og_types', ['website']);
        $object->config()->set('sharing_available_twitter_types', ['summary']);
        $fields = $object->getCMSFields()->dataFieldNames();
        $this->assertContains('OGType', $fields);
        $this->assertContains('TwitterType', $fields);

        $object->config()->set('sharing_allow_user_overwrite', false);
        $fields = $object->getCMSFields()->dataFieldNames();
        $this->assertNotContains('OGImage', $fields);
        $this->assertNotContains('OGTitle', $fields);
        $this->assertNotContains('OGDescription', $fields);
        $this->assertNotContains('OGType', $fields);
        $this->assertNotContains('TwitterType', $fields);
    }
}
