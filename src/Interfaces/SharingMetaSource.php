<?php

namespace Syntro\SilverShare\Interfaces;

use SilverStripe\Assets\Image;

/**
 * Establishes the necessary functionality to provide sharing information
 *
 * @author Matthias Leutenegger <hello@syntro.ch>
 */
interface SharingMetaSource
{

    /**
     * sharedOGType - returns the type which is used for sharing on og
     *
     * @return string
     */
    public function sharedOGType();


    /**
     * sharedTwitterType - returns the twitter type used with this source
     *
     * @return string
     */
    public function sharedTwitterType();

    /**
     * sharedOGTitle - returns the title this source should display when shared
     *
     * @return string
     */
    public function sharedOGTitle();

     /**
      * sharedOGDescription - returns a description which is used when shared
      *
      * @return string
      */
    public function sharedOGDescription();

    /**
     * sharedImage - returns an Image that should be displayed when shared
     *
     * @return Image|null
     */
    public function sharedImage();

    /**
     * sharedURL - returns the canonical url
     *
     * @return string
     */
    public function sharedURL();
}
