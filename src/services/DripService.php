<?php
/**
 * Drip plugin for Craft CMS 3.x
 *
 * Drip connector for Craft 3.x
 *
 * @link      madebyextreme.com
 * @copyright Copyright (c) 2019 Extreme
 */

namespace extreme\drip\services;

use Craft;
use craft\base\Component;
use craft\elements\User;
use extreme\drip\Drip;
use extreme\drip\helpers\DripRequest;
use extreme\drip\helpers\Dataset;
use extreme\drip\helpers\DripException;

use craft\commerce\events\LineItemEvent;

use Solspace\Freeform\Events\Forms\AfterSubmitEvent;
use Solspace\Freeform\Events\Submissions\SubmitEvent;
use Solspace\Freeform\Library\Composer\Components\Fields\EmailField;
use yii\base\InvalidConfigException;

/**
 * @author    Extreme
 * @package   Drip
 * @since     1.0.0
 */
class DripService extends Component
{

    /**
     * @var Drip|null
     */

    public $drip = null;

    /**
     * DripService constructor.
     * initialises the Drip API connection
     */

    public function __construct()
    {
        parent::__construct();

        if (!$this->drip) {
            $plugin = Drip::$plugin;
            $this->drip = new DripRequest(
                $plugin->getSettings()->dripApiToken,
                $plugin->getSettings()->dripAccountId
            );
        }
    }

    // Public Methods
    // =========================================================================

    /**
     * Returns array of custom fields from Drip
     * Call with Drip::$plugin->dripService->getCustomFields()
     *
     * @return array
     * @throws DripException
     * @throws InvalidConfigException
     */

    public function getCustomFields()
    {
        $dripCustomFields = $this->drip->get('v2', 'custom_field_identifiers')->get();
        return $dripCustomFields;
    }

    /**
     * Returns array of custom events from Drip
     * Call with Drip::$plugin->dripService->getCustomEvents()
     *
     * @return array
     * @throws DripException
     * @throws InvalidConfigException
     */

    public function getCustomEvents()
    {
        $dripCustomEvents = $this->drip->get('v2', 'event_actions')->get();
        return $dripCustomEvents;
    }


    /**
     * Method used to record all core drip events
     * In case of update event subscriber data is also updated in addition to logging the event
     *
     * @param String $event
     * @param User $user
     * @return bool
     */

    public function addCoreDripEvent(String $event, User $user)
    {
        $result = [];
        $settings = Drip::$plugin->getSettings();

        if ($settings->core[$event]['enabled'] == 1) {
            $eventName = Craft::t('drip', 'event_core_' . $event);
            $eventData = new Dataset('events', [
                'action' => $eventName,
                'email' => $user->email,
                'occurred_at' => date('c'),
                'properties' => [
                    'source' => 'Drip Plugin'
                ]
            ]);

            try {
                $result = $this->drip->post('v2', 'events', $eventData)->get();
            } catch (DripException $e) {
                Craft::error($e->getMessage(), 'drip');
            }

            if (array_key_exists('errors', $result)) {
                foreach ($result['errors'] as $error) {
                    Craft::error($error->message, 'drip');
                }
            }

            if ($event === 'update') {
                $this->updateDripSubscriber($user);
            }
        }

        return true;
    }

    /**
     * Method used to record all core drip events
     * In case of update event subscriber data is also updated in addition to logging the event
     *
     * @param LineItemEvent $event
     * @return bool
     */

    public function addShopperCartActivityDripEvent(LineItemEvent $event)
    {


        $user = $event->lineItem->getOrder()->getUser();
        if($user) {

            $order = $event->lineItem->getOrder();
            $items = $order->getLineItems();

            $lineItems = [];
            foreach($items as $li) {
                $properties = [
                    'product_id' => $li->purchasable->product->id,
                    'product_variant_id' => $li->purchasableId,
                    'sku' => $li->sku,
                    'name' => $li->purchasable->title,
                    'price' => $li->price,
                    'quantity' => (int) $li->qty,
                    'discounts' => abs($li->getDiscount()),
                    'total' => $li->total,
                    'product_url' => $li->purchasable->product->url,
                    'image_url' => $li->purchasable->product->productImages ? $li->purchasable->product->productImages->one()->url : '',
                ];
                $lineItems[] = $properties;
            }

            // $lineItems = [];

            $result = [];
            $settings = Drip::$plugin->getSettings();

            if ($settings->commerce['cart']['enabled'] == 1) {
                $eventName = Craft::t('drip', 'event_commerce_cart');
                $eventData = [
                    'provider' => 'craft_commerce',
                    'email' => $user->email,
                    'action' => 'updated',
                    'cart_id' => $order->shortNumber,
                    'occurred_at' => date('c'),
                    'grand_total' => $order->total,
                    'total_discounts' => abs($order->getTotalDiscount()),
                    'cart_url' => $order->returnUrl,
                    'items' => $lineItems,
                    'properties' => [
                        'source' => 'Drip Plugin'
                    ]
                ];

                try {
                    $result = $this->drip->post('v3', 'shopper_activity/cart', $eventData)->get();
                } catch (DripException $e) {
                    Craft::error($e->getMessage(), 'drip');
                }

                if (array_key_exists('errors', $result)) {
                    foreach ($result['errors'] as $error) {
                        Craft::error($error->message, 'drip');
                    }
                }
            }
        }

        return true;
    }


    /**
     * Record Freeform form submission event
     * if subscriber form option enabled (and GDPR permission is granted) also create or update subscriber
     *
     * @param AfterSubmitEvent $event SubmitEvent
     * @return bool
     */

    public function addFormSubmission(AfterSubmitEvent $event)
    {
        $result = [];
        $settings = Drip::$plugin->getSettings();

        $form = $event->getForm();

        $formHandle = $form->getHandle();

        $formFields = $form->getLayout()->getFields();

        // first log the event if we have a user

        $user = Craft::$app->getUser()->getIdentity();

        $formIsEnabled = $this->settingIsEnabled($settings, $formHandle, 'enabled');

        if ($user && $formIsEnabled) {
            $eventName = Craft::t('drip', 'event_freeform_submission', ['formName' => $form->getName()]);
            $eventData = new Dataset('events', [
                'action' => $eventName,
                'email' => $user->email,
                'occurred_at' => date('c'),
                'properties' => [
                    'source' => 'Drip Plugin',
                    'form' => $formHandle
                ]
            ]);

            try {
                $result = $this->drip->post('v2', 'events', $eventData)->get();
            } catch (DripException $e) {
                Craft::error($e->getMessage(), 'drip');
            }

            if (array_key_exists('errors', $result)) {
                foreach ($result['errors'] as $error) {
                    Craft::error($error['message'], 'drip');
                }
            }
        }

        $subscriberEnabled = $this->settingIsEnabled($settings, $formHandle, 'subscriber');

        if ($subscriberEnabled) {
            $gdprConsentFieldHandle = array_key_exists('permission', $settings['freeform'][$formHandle]) ? $settings['freeform'][$formHandle]['permission'] : null;
            $gdprConsentRequired = $gdprConsentProvided = false;
            $gdprConsentText = '';

            foreach ($formFields as $field) {
                if ($gdprConsentFieldHandle && $gdprConsentFieldHandle == $field->getHandle()) {
                    $gdprConsentRequired = true;
                    if ($field->getValue() != '') {
                        $gdprConsentProvided = true;
                        $gdprConsentText = $field->getLabel();
                    }
                }
            }

            if ($gdprConsentRequired && $gdprConsentProvided) {
                $this->updateDripSubscriberFreeform($formHandle, $formFields, 'granted', $gdprConsentText);
            } elseif (!$gdprConsentRequired) {
                $this->updateDripSubscriberFreeform($formHandle, $formFields);
            }
        }

        return true;
    }


    /**
     * Updates a drip subscriber using the field mapping defined in the core plugin settings
     *
     * @param $user
     */

    public function updateDripSubscriber($user)
    {
        $fields = [];
        $settings = Drip::$plugin->getSettings();

        foreach ($settings->coreFieldsDefault as $craftField => $dripField) {
            if ($dripField === 'unmapped') {
                continue;
            }
            $fields[$dripField] = $user[$craftField];
        }

        foreach ($settings->coreFieldsCustom as $fieldGroup) {
            foreach ($fieldGroup as $craftField => $dripField) {
                if ($dripField === 'unmapped') {
                    continue;
                }
                $fields['custom_fields'][$dripField] = $user[$craftField];
            }
        }

        $userData = new Dataset('subscribers', $fields);

        try {
            $update = $this->drip->post('v2', 'subscribers', $userData);
            if ($update->status !== 200) {
                Craft::error($update->message, 'drip');
            }
        } catch (DripException $e) {
            Craft::error($e->getMessage(), 'drip');
        }
    }

    /**
     * Updates a drip subscriber using the field mapping defined in the Freeform settings
     *
     * @param $formHandle string
     * @param $formFields array
     * @param $gdprConsent
     * @param $gdprText
     * @return mixed
     */

    public function updateDripSubscriberFreeform(string $formHandle, array $formFields, $gdprConsent = null, $gdprText = null)
    {
        $fields = [];
        $settings = Drip::$plugin->getSettings();

        $formValues = [];
        $mappedFields = $settings['freeform'][$formHandle];

        // create array of formValues in format handle=>value

        foreach ($formFields as $formField) {
            $value = $formField->getValue();

            // freeform email field type value is array
            if ($formField instanceof \Solspace\Freeform\Fields\EmailField) {
                $value = is_array($value) ? $value[0] : $value;
            }

            // only add fields that have handles (excludes submit etc)
            $handle = $formField->getHandle();
            if ($handle) {
                $formValues[$handle] = $value;
            }
        }

        // iterate the mapped form fields to generate the $field array

        foreach ($mappedFields as $freeformField => $dripField) {
            if ($dripField === 'unmapped' || !array_key_exists($freeformField, $formValues)) {
                continue;
            }

            // test if this is a core field - compare to settings->dripCoreFields

            $isCoreField = array_key_exists($dripField, $settings->dripCoreFields);

            if ($isCoreField) {
                $fields[$dripField] = $formValues[$freeformField];
            } else {
                $fields['custom_fields'][$dripField] = $formValues[$freeformField];
            }
        }

        // assign gdpr consent values if set

        if ($gdprConsent) {
            $fields['eu_consent'] = $gdprConsent;
        }

        if ($gdprText) {
            $fields['eu_consent_message'] = $gdprText;
        }

        // prepare subscriber data for Drip
        $userData = new Dataset('subscribers', $fields);

        try {
            $update = $this->drip->post('v2', 'subscribers', $userData);
            if ($update->status !== 200) {
                Craft::error($update->message, 'drip');
            }
            return $update;
        } catch (DripException $e) {
            Craft::error($e->getMessage(), 'drip');
            return false;
        }
    }

    /**
     * Create lowercase snake version of string
     * convert to alphanumeric, lowercase and convert whitespace/hyphens to underscore
     * This format is required by Drip for provider name
     *
     * @param $string
     * @return string|string[]|null
     */
    protected function makeSnake($string)
    {
        $string = strtolower($string);
        $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
        $string = preg_replace("/[\s-_]+/", " ", $string);
        $string = preg_replace("/[\s-]/", "_", $string);
        return $string;
    }

    /**
     * @param $settings
     * @param $formHandle
     * @return bool
     */
    protected function formIsEnabled($settings, $formHandle)
    {
        foreach ($settings['freeform'] as $key => $form) {
            if ($key == $formHandle) {
                return array_key_exists('enabled', $settings['freeform'][$key]) && $settings['freeform'][$key]['enabled'] == 1;
            }
        }
        return false;
    }

    /**
     * @param $settings
     * @param $formHandle
     * @param $setting
     * @return bool
     */
    protected function settingIsEnabled($settings, $formHandle, $setting)
    {
        foreach ($settings['freeform'] as $key => $form) {
            if ($key == $formHandle) {
                return array_key_exists($setting, $settings['freeform'][$key]) && $settings['freeform'][$key][$setting] == 1;
            }
        }
        return false;
    }
}
