<?php
namespace Syntro\SilverShare\Extension;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\ToggleCompositeField;

/**
 * Adds some Meta fields to the siteconfig
 * @author Matthias Leutenegger <hello@syntro.ch>
 */
class ShareSiteConfigExtension extends DataExtension
{
    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'TwitterSite' => 'Varchar',
    ];

    /**
     * Has_one relationship
     * @var array
     */
    private static $has_one = [
        'OGDefaultImage' => Image::class,
    ];

    /**
     * Relationship version ownership
     * @var array
     */
    private static $owns = [
        'OGDefaultImage'
    ];

    /**
     * fields to be ignored by fluent
     * @var array
     */
    private static $field_exclude = [
        'TwitterSite'
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
        $fields->addFieldsToTab(
            'Root.Main',
            [
                $OGDefaultImageField = UploadField::create('OGDefaultImage', _t(__CLASS__ . '.OGImageTitle', 'Default preview image')),
                $SoMeCollapseField = ToggleCompositeField::create(
                    'SoMeData',
                    _t(__CLASS__ . '.SOCIALMEDIAMETA', 'Social Media Meta'),
                    [
                        $TwitterSiteField = TextField::create('TwitterSite', _t(__CLASS__ . '.TwitterSiteTitle', 'Twitter Site'))
                    ]
                )
            ]
        );
        $OGDefaultImageField
            ->setFolderName('Meta/Images')
            ->setRightTitle(_t(__CLASS__ . '.OGImageRight', 'This image will be displayed if no page specific image has been defined.'));
        $TwitterSiteField
            ->setAttribute('placeholder', _t(__CLASS__ . '.TwitterSitePlaceholder', '@yourcompany'))
            ->setRightTitle(_t(__CLASS__ . '.TwitterSiteRight', 'The handle of your Twitter page.'));
        return $fields;
    }
}
