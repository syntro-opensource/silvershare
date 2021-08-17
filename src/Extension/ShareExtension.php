<?php
namespace Syntro\SilverShare\Extension;

use SilverStripe\View\SSViewer;
use SilverStripe\Forms\HeaderField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\ManyManyList;
use SilverStripe\ORM\ManyManyThroughList;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Core\ClassInfo;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\CMS\Model\RedirectorPage;
use SilverStripe\CMS\Model\VirtualPage;
use SilverStripe\ErrorPage\ErrorPage;
use Syntro\SilverShare\Interfaces\SharingMetaSource;
use Page;

/**
 * The MetadataExtension applies the necessary functionality
 * to the Page object to handle automatic metadata generation
 *
 * @author Matthias Leutenegger <hello@syntro.ch>
 */
class ShareExtension extends DataExtension implements SharingMetaSource
{
    // /**
    //  * @config
    //  * @var array
    //  */
    // private static $sharing_available_og_types = [
    //     'website',
    //     'article'
    // ];
    //
    // /**
    //  * @config
    //  * @var array
    //  */
    // private static $sharing_available_twitter_types = [
    //     'summary',
    //     'summary_large_image',
    //     'app',
    //     'player'
    // ];

    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'OGType' => 'Varchar(20)',
        'OGTitle' => 'Varchar',
        'OGDescription' => 'Text',
        'TwitterType' => 'Varchar(20)',
    ];

    /**
     * Has_one relationship
     * @var array
     */
    private static $has_one = [
        'OGImage' => Image::class
    ];

    /**
     * Relationship version ownership
     * @var array
     */
    private static $owns = [
        'OGImage'
    ];

    /**
     * Add default values to database
     * @var array
     */
    private static $defaults = [
        'OGType' => 'website',
        'TwitterType' => 'summary'
    ];

    /**
     * fields to be translated by fluent
     * @var array
     */
    private static $field_include = [
        'OGTitle',
        'OGDescription',
    ];

    /**
     * fields to be ignored by fluent
     * @var array
     */
    private static $field_exclude = [
        'OGType',
        'TwitterType'
    ];

    /**
     * Update Fields
     *
     * @param  FieldList $fields the original fields
     * @return FieldList
     */
    public function updateCMSFields(FieldList $fields)
    {

        $owner = $this->owner;
        // stop when we are dealing with a redirector or virtual page
        if (
            $owner instanceof RedirectorPage ||
            $owner instanceof VirtualPage ||
            $owner instanceof ErrorPage
        ) {
            return $fields;
        }

        if ($owner->config()->sharing_allow_user_overwrite) {

            $OGTypes = [];
            $availableOGTypes = $owner->config()->sharing_available_og_types ?? [];
            foreach ($availableOGTypes as $value) {
                $OGTypes[$value] = _t(__CLASS__ . '.' . $value, $value);
            }
            $TwitterTypes = [];
            $availableTwitterTypes = $owner->config()->sharing_available_twitter_types ?? [];
            foreach ($availableTwitterTypes as $value) {
                $TwitterTypes[$value] = _t(__CLASS__ . '.' . $value, $value);
            }
            $fields->findOrMakeTab(
                "Root.SocialSharing",
                $owner->fieldLabel('Root.SocialSharing')
            );
            $ogInfoText = _t(__CLASS__ . '.INFOTEXT', '{info}');
            $fields->addFieldsToTab(
                'Root.SocialSharing',
                [
                    $ogInfotext = LiteralField::create('info', "<div class=\"alert alert-light\">{$ogInfoText}</div>"),
                    $ogImage = UploadField::create('OGImage', _t(__CLASS__ . '.OGImageTitle', 'OpenGraph Image')),
                    $ogTitle = TextField::create('OGTitle', _t(__CLASS__ . '.OGTitleTitle', 'OpenGraph Title')),
                    $ogDescription = TextareaField::create('OGDescription', _t(__CLASS__ . '.OGDescriptionTitle', 'OpenGraph Description')),
                    $toggleTypesField = ToggleCompositeField::create(
                        'Types',
                        _t(
                            __CLASS__ . '.RENDERTYPES',
                            'Render Types'
                        ),
                        [
                            $ogType = DropdownField::create('OGType', _t(__CLASS__ . '.OGTypeTitle', 'OpenGraph Type'), $OGTypes),
                            $twitterType = DropdownField::create('TwitterType', _t(__CLASS__ . '.TwitterTypeTitle', 'Twitter Type'), $TwitterTypes)
                        ]
                    )
                ]
            );

            $ogTitle
                ->setRightTitle(_t(__CLASS__ . '.OGTitleRight', 'The title which is shown when you share this page.'))
                ->setAttribute('placeholder', $this->getFallbackTitle());
            $ogDescription
                ->setAttribute('placeholder', $this->getFallbackDescription())
                ->setRightTitle(_t(__CLASS__ . '.OGDescriptionRight', 'The summary which is shown when you share this page.'));
            $ogType
                ->setRightTitle(_t(__CLASS__ . '.OGTypeRight', 'The type which is used to display this page when shared. Most of the time, you want this to be "website"'));
            $twitterType
                ->setRightTitle(_t(__CLASS__ . '.TwitterTypeRight', 'The type which is used to display this page when shared on Twitter. Most of the time, you want this to be "summary"'));

            // add some dialog to indicate image
            $ogImage->setRightTitle(_t(__CLASS__ . '.OGImageRight', 'The image which is shown when you share this page.'));
            if (!$owner->OGImageID && ! $this->getFallbackImage()) {
                $alertColor = SiteConfig::current_site_config()->OGDefaultImageID
                    ? 'info'
                    : 'danger';
                $alertMessage = SiteConfig::current_site_config()->OGDefaultImageID
                    ? _t(__CLASS__ . '.DEFAULTIMAGE', 'The default image set in the siteconfig will be used.')
                    : _t(__CLASS__ . '.NODEAFAULTIMAGE', 'No Image is set. This means, a crawler might select one at random.');
                $defaultImage = SiteConfig::current_site_config()->OGDefaultImage;
                if ($defaultImage && $defaultImage->isInDB()) {
                    $ogImage->setDescription("<div class=\"alert alert-{$alertColor} mb-0 d-flex align-items-center p-0\"><img class=\"rounded-left\" src=\"{$defaultImage->Thumbnail(60,60)->getURL()}\" /><div class=\"p-2\">{$alertMessage}</div></div>");
                } else {
                    $ogImage->setDescription("<div class=\"alert alert-{$alertColor} mb-0\">{$alertMessage}</div>");
                }

            }


            // $OgImageRightTitle = _t(__CLASS__ . '.OGImageRight', 'The image which is shown when you share this page.');
            // if (!$owner->OGImageID && !SiteConfig::current_site_config()->OGDefaultImageID) {
            //     $ogImage->setRightTitle(
            //         $OgImageRightTitle . ' ' . _t(__CLASS__ . '.NODEAFAULTIMAGE', 'No default Image is set. This means, a crawler might select one at random.')
            //     );
            // } elseif (!$owner->OGImageID && SiteConfig::current_site_config()->OGDefaultImageID) {
            //     $ogImage->setRightTitle(
            //         $OgImageRightTitle . ' ' . _t(__CLASS__ . '.DEFAULTIMAGE', 'The default image set in the siteconfig will be used.')
            //     );
            // }
        } else {
            $fields->removeByName([
                'OGType',
                'OGTitle',
                'OGDescription',
                'TwitterType',
                'OGImage',
            ]);
        }

        return $fields;
    }

    /**
     * updateFieldLabels - adds Fieldlabels
     *
     * @param  array $labels the original labels
     * @return array
     */
    public function updateFieldLabels(&$labels)
    {
        $labels['Root.SocialSharing'] =  _t(__CLASS__ . '.Sharing', 'Sharing');
        return $labels;
    }

    /**
     * sharedOGType - returns the type which is used for sharing on og
     *
     * @return string
     */
    public function sharedOGType()
    {
        return $this->getOwner()->OGType;
    }

    /**
     * sharedTwitterType - returns the twitter type used with this source
     *
     * @return string
     */
    public function sharedTwitterType()
    {
        return $this->getOwner()->TwitterType;
    }

    /**
     * sharedTitle - returns the title this source should display when shared
     *
     * @return string
     */
    public function sharedOGTitle()
    {
        return $this->getOwner()->OGTitle ?? $this->getFallbackTitle();
    }

    /**
     * sharedOGDescription - returns a description which is used when shared
     *
     * @return string
     */
    public function sharedOGDescription()
    {
        return $this->getOwner()->OGDescription ?? $this->getFallbackDescription();
    }

    /**
     * sharedImage - returns an Image that should be displayed when shared
     *
     * @return Image|null
     */
    public function sharedImage()
    {
        return $this->getOwner()->OGImage ?? $this->getFallbackImage();
    }

    /**
     * sharedURL - returns the canonical url
     *
     * @return string
     */
    public function sharedURL()
    {
        return $this->getOwner()->AbsoluteLink();
    }

    /**
     * getFallbackTitle - This returns a fallback title, based on the
     * sharing_fallback_title config of the applied object
     *
     * @return string|null
     */
    public function getFallbackTitle()
    {
        $owner = $this->getOwner();
        $fallbackField = $owner->config()->sharing_fallback_title;
        if ($fallbackField) {
            return (string) $owner->obj($fallbackField);
        }
        return null;
    }

    /**
     * getFallbackDescription - This returns a fallback description, based on the
     * sharing_fallback_description config of the applied object
     *
     * @return string|null
     */
    public function getFallbackDescription()
    {
        $owner = $this->getOwner();
        $fallbackField = $owner->config()->sharing_fallback_description;
        if ($fallbackField) {
            return (string) $owner->obj($fallbackField);
        }
        return null;
    }

    /**
     * getFallbackImage - This returns a fallback image, based on the
     * sharing_fallback_image config of the applied object
     *
     * @return string|null
     */
    public function getFallbackImage()
    {
        $owner = $this->getOwner();
        $fallbackField = $owner->config()->sharing_fallback_image;
        if ($fallbackField) {
            $fallback = $owner->obj($fallbackField);
            if ($fallback instanceof Image) {
                return  $fallback;
            }
            if (
                ($fallback instanceof ManyManyList ||
                $fallback instanceof ManyManyThroughList) &&
                $fallback->count() > 0
            ) {
                return $fallback->first();
            }
        }
        return null;
    }
}
