<?php

/**
 * Drip eCRM Connector plugin for Craft CMS 3.x
 *
 * Drip eCRM Connector
 *
 * @link      madebyextreme.com
 * @copyright Copyright (c) 2019 Extreme
 */

namespace extreme\drip\controllers;

use extreme\drip\Drip;

use Craft;
use craft\web\Controller;
use extreme\drip\assetbundles\settingscpsection\SettingsCPSectionAsset;
use extreme\drip\helpers\drip\DripException;
use yii\base\InvalidConfigException;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * Settings Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Extreme
 * @package   Drip
 * @since     1.0.0
 */
class SettingsController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected array|int|bool $allowAnonymous = ['index'];

    // Public Methods
    // =========================================================================


    /**
     * This action handles all settings requests and renders the required template
     *
     * @return Response
     * @throws InvalidConfigException
     */
    public function actionLoadSettings(): Response
    {
        $this->view->registerAssetBundle(SettingsCPSectionAsset::class);

        $template = Craft::$app->request->getSegment(3);

        $settings = Drip::getInstance()->getSettings();

        $templatePath = 'drip/settings/_' . ($template ?: 'drip');

        return $this->renderTemplate($templatePath, ['settings' => $settings]);
    }


    /**
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionSaveSettings()
    {
        $this->requirePostRequest();

        $postData = Craft::$app->request->post('settings', []);

        $plugin = Drip::getInstance();

        $plugin->setSettings($postData);

        if (Craft::$app->plugins->savePluginSettings($plugin, $postData)) {
            Craft::$app->session->setNotice(Craft::t('drip', 'Settings Saved'));
        } else {
            $errors = $plugin->getSettings()->getErrors();
            Craft::$app->session->setError(json_encode($errors));
        }
        return $this->redirectToPostedUrl();
    }

    /**
     * Connect to Drip, retrieve list of custom fields and save to plugin settings
     *
     * @return Response
     * @throws InvalidConfigException
     * @throws \extreme\drip\helpers\DripException
     */
    public function actionGetFields()
    {
        $fields = Drip::$plugin->dripService->getCustomFields();

        if (array_key_exists('custom_field_identifiers', $fields)) {
            $data = [
                'dripCustomFields' => $fields['custom_field_identifiers'],
                'dripCoreFields' => [
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
                ]
            ];

            Drip::getInstance()->setSettings($data);

            Craft::$app->plugins->savePluginSettings(Drip::getInstance(), $data);
        }

        return $this->asJson(['fields' => $fields]);
    }
}
