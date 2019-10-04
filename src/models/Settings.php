<?php
/**
 * Drip plugin for Craft CMS 3.x
 *
 * Drip connector for Craft 3.x
 *
 * @link      madebyextreme.com
 * @copyright Copyright (c) 2019 Extreme
 */

namespace extreme\drip\models;

use extreme\drip\Drip;

use Craft;
use craft\base\Model;

/**
 * @author    Extreme
 * @package   Drip
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $dripApiToken = '';

    /**
     * @var string
     */
    public $dripAccountId = '';

    /**
     * @var string
     */
    public $dripSnippet = '';

    /**
     * @var array
     */
    public $dripCoreFields = [
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'email' => 'Email Address',
        'phone_number' => 'Phone Number',
        'time_zone' => 'Time Zone',
        'country' => 'Country',
        'state' => 'State',
        'address1' => 'Address 1',
        'address2' => 'Address 2',
        'city' => 'City',
        'zip' => 'Zip'
    ];

    /**
     * @var array
     */
    public $dripCustomFields = [];

    /**
     * @var array
     */
    public $freeform = [];

    /**
     * @var array
     */
    public $core = [
        'create' => [
            'enabled' => false,
            'event' => ''
        ],
        'login' => [
            'enabled' => false,
            'event' => ''
        ],
        'logout' => [
            'enabled' => false,
            'event' => ''
        ],
        'update' => [
            'enabled' => false,
            'event' => ''
        ]
    ];

    /**
     * @var array
     */

    public $coreFieldsDefault = [
        'email' => 'Email',
        'firstName' => 'First Name',
        'lastName' => 'Last Name'
    ];

    /**
     * @var array
     */

    public $coreFieldsCustom = [];


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }

}
