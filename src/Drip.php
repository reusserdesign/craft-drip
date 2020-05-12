<?php
/**
 * Drip plugin for Craft CMS 3.x
 *
 * Drip connector for Craft 3.x
 *
 * @link      madebyextreme.com
 * @copyright Copyright (c) 2019 Extreme
 */

namespace extreme\drip;

use extreme\drip\services\DripService as DripService;
use extreme\drip\models\Settings;
use extreme\drip\variables\DripVariable;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\services\Elements;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\web\View;

use craft\events\RegisterUrlRulesEvent;
use craft\web\twig\variables\CraftVariable;
use craft\services\Users;
use craft\events\UserEvent;

use craft\commerce\elements\Order;
use craft\commerce\models\LineItem;
use craft\commerce\events\LineItemEvent;

use Solspace\Freeform\Services\FormsService;
use Solspace\Freeform\Events\Forms\AfterSubmitEvent;

use yii\base\Event;
use yii\web\User;

/**
 * Class Drip
 *
 * @author    Extreme
 * @package   Drip
 * @since     1.0.0
 *
 * @property  DripService $dripService
 */
class Drip extends Plugin
{
    const TRANSLATION_CATEGORY = 'drip';

    // Static Properties
    // =========================================================================

    /**
     * @var Drip
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     *  Holds the api connection
     */

    public $drip = null;


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $view = Craft::$app->getView();
        $request = Craft::$app->getRequest();

        if (!$request->isCpRequest) {
            $content = str_replace(["\r\n", "\r", "\n"], '', $this->settings['dripSnippet']);
            if (strlen($content)) {
                preg_match('/<script(.*?)>(.*?)<\/script>/', $content, $matches);
                $snippet = array_key_exists(2, $matches) ? $matches[2] : $content;
                $view->registerJs($snippet, View::POS_END);
            }
        }

        $this->initDripEvents();

        // Register our site routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['siteActionTrigger1'] = 'drip/drip';
            }
        );

        // Register our CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['drip/settings'] = 'drip/settings/load-settings';
                $event->rules['drip/settings/drip'] = 'drip/settings/load-settings';
                $event->rules['drip/settings/core'] = 'drip/settings/load-settings';
                $event->rules['drip/settings/freeform'] = 'drip/settings/load-settings';
                $event->rules['drip/settings/commerce'] = 'drip/settings/load-settings';
            }
        );

        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('drip', DripVariable::class);
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        Craft::info(
            Craft::t(
                'drip',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'drip/settings',
            [
                'settings' => $this->getSettings()
            ],
            View::TEMPLATE_MODE_CP
        );
    }

    /**
     * @return array|null
     */

    public function getCpNavItem()
    {
        $subNavs = [];
        $navItem = parent::getCpNavItem();

        $currentUser = Craft::$app->getUser()->getIdentity();

        $subNavs['dashboard'] = [
            'label' => 'Dashboard',
            'url' => 'drip/dashboard',
        ];

        $subNavs['settings'] = [
            'label' => 'Settings',
            'url' => 'drip/settings',
        ];


        $navItem = array_merge($navItem, [
            'subnav' => $subNavs,
        ]);

        return $navItem;
    }

    /**
     *
     */
    private function initDripEvents()
    {

        /**
         * Activate User Event
         * can use EVENT_AFTER_UNSUSPEND_USER for easy testing via cms
         * Full list of user events: craft/vendor/craftcms/cms/src/services/Users.php
         */

        Event::on(
            Users::class,
            Users::EVENT_AFTER_ACTIVATE_USER,
            function (UserEvent $event) {
                Drip::$plugin->dripService->addCoreDripEvent('create', $event->user);
            }
        );

        /**
         * User Account Login Event
         * Core Yii Class event requires yii\web\User
         */

        Event::on(
            User::class,
            User::EVENT_AFTER_LOGIN,
            function (Event $event) {
                Drip::$plugin->dripService->addCoreDripEvent('login', $event->identity);
            }
        );

        /**
         * User Account Logout Event
         * Core Yii Class event requires yii\web\User
         */

        Event::on(
            User::class,
            User::EVENT_AFTER_LOGOUT,
            function (Event $event) {
                Drip::$plugin->dripService->addCoreDripEvent('logout', $event->identity);
            }
        );

        /**
         * User Account Update Event
         *
         */

        Event::on(
            Elements::class,
            Elements::EVENT_AFTER_SAVE_ELEMENT,
            function (Event $event) {
                if ($event->element instanceof \craft\elements\User) {
                    Drip::$plugin->dripService->addCoreDripEvent('update', $event->element);
                }
            }
        );


        /**
         * Freeform form submission
         * http://docs.solspace.com/craft/freeform/v2/developer/events-and-hooks.html#submissions
         */

        if (Craft::$app->plugins->isPluginInstalled('freeform')) {
            Event::on(
                FormsService::class,
                FormsService::EVENT_AFTER_SUBMIT,
                function (AfterSubmitEvent $event) {
                    Drip::$plugin->dripService->addFormSubmission($event);
                }
            );
        }

        /**
         * Craft Commerce cart update
         * http://docs.solspace.com/craft/freeform/v2/developer/events-and-hooks.html#submissions
         */

        if (Craft::$app->plugins->isPluginInstalled('commerce')) {
            Event::on(
                Order::class,
                Order::EVENT_AFTER_ADD_LINE_ITEM,
                function(LineItemEvent $event) {
                    Drip::$plugin->dripService->addShopperCartActivityDripEvent($event);
                }
            );
        }

    }
}
