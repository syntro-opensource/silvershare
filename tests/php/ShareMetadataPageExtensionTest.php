<?php

namespace Syntro\Silvershare\Tests;

use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\CMS\Model\SiteTree;

/**
 * Test the SiteTree extension which handles metadata creation
 *
 * @author Matthias Leutenegger <hello@syntro.ch>
 */
class ShareMetadataPageExtensionTest extends SapphireTest
{

    /**
     * testGetSharingSource
     *
     * @return void
     */
    public function testGetSharingSource()
    {
        $sitetree = SiteTree::create();
        $sitetreeNew = SiteTree::create();
        $source = $sitetree->getSharingSource();
        $this->assertEquals($sitetree->ID, $source->ID);
        $sitetree->setSharingSource($sitetreeNew);
        $source = $sitetree->getSharingSource();
        $this->assertEquals($sitetreeNew->ID, $source->ID);
    }

    public function testCreateMetaTag($value='')
    {
        $sitetree = SiteTree::create();
        $tag = $sitetree->createMetaTag('property', 'content', 'propKey');
        $this->assertEquals([
            'tag' => 'meta',
            'attributes' => [
                'propKey' => 'property',
                'content' => 'content'
            ],
            'content' => null
        ], $tag);
    }

    /**
     * testProvideNameTag
     *
     * @return void
     */
    public function testProvideNameTag()
    {
        $testTitle = 'Some Title';
        $config = SiteConfig::current_site_config();
        $config->Title = $testTitle;
        $config->write();
        $sitetree = SiteTree::create();
        $tag = $sitetree->provideNameTag();
        $this->assertEquals('meta', $tag['tag']);
        $this->assertEquals('og:name', $tag['attributes']['property']);
        $this->assertEquals($testTitle, $tag['attributes']['content']);
        $this->assertNull($tag['content']);
    }

    /**
     * testProvideTwitterTag
     *
     * @return void
     */
    public function testProvideTwitterTag()
    {
        $testTitle = '@twitter';
        $config = SiteConfig::current_site_config();
        $config->TwitterSite = $testTitle;
        $config->write();
        $sitetree = SiteTree::create();
        $tag = $sitetree->provideTwitterSiteTag();
        $this->assertEquals('meta', $tag['tag']);
        $this->assertEquals('twitter:site', $tag['attributes']['name']);
        $this->assertEquals($testTitle, $tag['attributes']['content']);
        $this->assertNull($tag['content']);
    }

    /**
     * testProvideOGTypeTag
     *
     * @return void
     */
    public function testProvideOGTypeTag()
    {
        $testType = 'article';
        $sitetree = SiteTree::create();
        // default fallback
        $tag = $sitetree->provideOGTypeTag();
        $this->assertEquals('meta', $tag['tag']);
        $this->assertEquals('og:type', $tag['attributes']['property']);
        $this->assertEquals('website', $tag['attributes']['content']);
        $this->assertNull($tag['content']);

        $sitetree->OGType = $testType;
        $tag = $sitetree->provideOGTypeTag();
        $this->assertEquals('meta', $tag['tag']);
        $this->assertEquals('og:type', $tag['attributes']['property']);
        $this->assertEquals($testType, $tag['attributes']['content']);
        $this->assertNull($tag['content']);
    }


    /**
     * testProvideTwitterTypeTag
     *
     * @return void
     */
    public function testProvideTwitterTypeTag()
    {
        $testType = 'summary_large_image';
        $sitetree = SiteTree::create();
        // default fallback
        $tag = $sitetree->provideTwitterTypeTag();
        $this->assertEquals('meta', $tag['tag']);
        $this->assertEquals('twitter:card', $tag['attributes']['name']);
        $this->assertEquals('summary', $tag['attributes']['content']);
        $this->assertNull($tag['content']);

        $sitetree->TwitterType = $testType;
        $tag = $sitetree->provideTwitterTypeTag();
        $this->assertEquals('meta', $tag['tag']);
        $this->assertEquals('twitter:card', $tag['attributes']['name']);
        $this->assertEquals($testType, $tag['attributes']['content']);
        $this->assertNull($tag['content']);
    }

    /**
     * testProvideOGTitleTag
     *
     * @return void
     */
    public function testProvideOGTitleTag()
    {
        $testTitle = 'article';
        $sitetree = SiteTree::create();

        $sitetree->Title = $testTitle;
        $tag = $sitetree->provideOGTitleTag();
        $this->assertEquals('meta', $tag['tag']);
        $this->assertEquals('og:title', $tag['attributes']['property']);
        $this->assertEquals($testTitle, $tag['attributes']['content']);
        $this->assertNull($tag['content']);
    }

    /**
     * testProvideOGDescriptionTag
     *
     * @return void
     */
    public function testProvideOGDescriptionTag()
    {
        $testTitle = 'Some description';
        $sitetree = SiteTree::create();

        $sitetree->MetaDescription = $testTitle;
        $tag = $sitetree->provideOGDescriptionTag();
        $this->assertEquals('meta', $tag['tag']);
        $this->assertEquals('og:description', $tag['attributes']['property']);
        $this->assertEquals($testTitle, $tag['attributes']['content']);
        $this->assertNull($tag['content']);
    }


    // TODO: Write a test for the image tag
    // TODO: Write a test for the URL Tag
}
