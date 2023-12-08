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

    public string $dripApiToken = '';

    public string $dripAccountId = '';

    public string $dripSnippet = '';

    public array $dripCoreFields = [
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

    public array $dripCustomFields = [];

    public array $freeform = [];

    public array $commerce = [];

    public array $core = [
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

    public array $coreFieldsDefault = [
        'email' => 'Email',
        'firstName' => 'First Name',
        'lastName' => 'Last Name'
    ];

    public array $coreFieldsCustom = [];


    // Public Methods
    // =========================================================================

    public function rules(): array
    {
        return [];
    }

    public function getDripAccountId(): string
    {
        return Craft::parseEnv($this->dripAccountId);
    }
}
