<?php
/**
 * Drip plugin for Craft CMS 3.x
 *
 * Drip connector for Craft 3.x
 *
 * @link      madebyextreme.com
 * @copyright Copyright (c) 2019 Extreme
 */

namespace extreme\drip\variables;

use craft\commerce\Plugin;
use extreme\drip\Drip;

use Craft;
use extreme\drip\helpers\DripException;
use Solspace\Freeform\Library\Composer\Components\Form;

/**
 * @author    Extreme
 * @package   Drip
 * @since     1.0.0
 */
class DripVariable
{

  /**
   * @return Settings
   */

    public function getSettings(): Settings
    {
        return Drip::getInstance()->settings->getSettingsModel();
    }

    /**
     * @return string
     */

    public function name(): string
    {
        return Drip::getInstance()->name;
    }

    /**
     * @return string
     */

    public function handle(): string
    {
        return Drip::getInstance()->handle;
    }

    /**
     * @return string
     */

    public function dripAccountId(): string
    {
        return Drip::getInstance()->getSettings()->getDripAccountId();
    }


    /**
     * Returns an array of core and custom Drip fields
     *
     * @return array
     */

    public function dripFields(): array
    {
        $fields = [
        'unmapped' => 'Not mapped'
        ];

        $fields = array_merge($fields, Drip::getInstance()->getSettings()->dripCoreFields);

        $customFields = Drip::getInstance()->getSettings()->dripCustomFields;

        foreach ($customFields as $field) {
            $fields[$field] = $field;
        }

        return $fields;
    }

    /**
     * Returns an array of freeform form fields for the form provided
     *
     * @param $formData
     * @return array
     */

    public function formFields(Form $formData): array
    {
        $fields = ['unmapped' => 'Unmapped'];

        foreach ($formData->getLayout()->getFields() as $field) {
            if (!$field->getHandle()) {
                continue;
            }
            $fields[$field->getHandle()] = $field->getLabel();
        }

        return $fields;
    }

    /**
     * Returns array of custom events from Drip
     *
     * @return array
     * @throws DripException
     * @throws \yii\base\InvalidConfigException
     */

    public function dripEvents(): array
    {
        $events = [
        'unmapped' => 'Not mapped'
        ];

        $dripEvents = Drip::$plugin->dripService->getCustomEvents();

        $events = array_merge($events, $dripEvents['event_actions']);

        return $events;
    }

    /**
     * Returns array of Commerce order statuses
     *
     * @return array
     */

    public function commerceOrderStatuses(): array
    {
        $orderStatuses = Plugin::getInstance()->orderStatuses->getAllOrderStatuses();

        $result = [];

        foreach ($orderStatuses as $status) {
            $result[$status->id] = $status->name;
        }
        return $result;
    }

    /**
     * Returns the array of Drip order statuses defined in settings
     * These are fixed values in the Drip API and should not change
     * but could be overridden in plugin config
     *
     * @return array
     */

    public function dripOrderEvents(): array
    {
        $dripOrderEvents = Drip::getInstance()->getSettings()->dripOrderEvents;

        return array_merge(['unmapped'], $dripOrderEvents);
    }
}
