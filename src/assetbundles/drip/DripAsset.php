<?php
/**
 * Drip plugin for Craft CMS 3.x
 *
 * Drip connector for Craft 3.x
 *
 * @link      madebyextreme.com
 * @copyright Copyright (c) 2019 Extreme
 */

namespace extreme\drip\assetbundles\Drip;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Extreme
 * @package   Drip
 * @since     1.0.0
 */
class DripAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@extreme/drip/assetbundles/drip/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/Drip.js',
        ];

        $this->css = [
            'css/Drip.css',
        ];

        parent::init();
    }
}
