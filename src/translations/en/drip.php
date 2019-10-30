<?php
/**
 * Drip plugin for Craft CMS 3.x
 *
 * Drip connector for Craft 3.x
 *
 * @link      madebyextreme.com
 * @copyright Copyright (c) 2019 Extreme
 */

/**
 * @author    Extreme
 * @package   Drip
 * @since     1.0.0
 */

return [
    'Settings saved' => 'Settings saved',
    'Drip eCRM Connector plugin loaded' => 'Drip eCRM Connector plugin loaded',
    'Drip Settings' => 'Drip Settings',
    'Drip API' => 'Drip API',
    '{plugin} Settings' => '{plugin} Settings',
    '{plugin} Events' => '{plugin} Events',
    'Core Events' => 'Core Events',
    'Commerce Events' => 'Commerce Events',
    'Freeform Events' => 'Freeform Events',
    'Enable {name}' => 'Enable {name}',
    'core_events_intro' => 'Enable the required options below to record events in Drip when core Craft actions are performed.',
    'freeform_introduction' => 'Configure below to record events in Drip when your forms are submitted.',
    'freeform_enable_label' => 'Enable {name}',
    'freeform_enable_instruction' => 'Enable to track submission events for the {name} form.',
    'freeform_event_tags_label' => 'Event Tags',
    'freeform_event_tags_instruction' => 'You can pass tags through to your Drip account when this event is fired. Please comma separate tags.',
    'freeform_drip_create_heading' => 'Create Drip Subscriber?',
    'freeform_drip_create_instruction' => 'If the information in this form should be used to create or update a drip subscriber enable this option.',
    'freeform_drip_permission_heading' => 'GDPR Consent Field',
    'freeform_drip_permission_instruction' => 'If your form contains a field that should be selected to confirm GDPR consent, select the field below.',
    'freeform_drip_email_heading' => 'Subscriber Email Field',
    'freeform_drip_email_instruction' => 'Select the field in your form that will contain the subscribers email address.',
    'freeform_drip_mapping_heading' => 'Subscriber Field Mapping',
    'freeform_drip_mapping_instruction' => 'Map the required Freeform fields to a corresponding field in Drip',
    'freeform_field_heading' => 'Freeform Field',
    'freeform_no_forms' => 'No forms available. Create your forms before configuring for Drip.',
    'drip_field_refresh_intro' => 'If you have updated your fields in Drip, click below to refresh the selectable field options.',
    'drip_field_refresh' => 'Refresh Drip Fields',
    'drip_api_test_intro' => '',
    'drip_field_heading' => 'Drip Field',
    'drip_account_id_label' => 'Drip Account ID',
    'drip_account_id_instruction' => 'You can find this in you Drip account in the General Info section',
    'drip_account_id_tip' => '<a href="https://www.drip.com/learn/docs/manual/settings/account" class="go" rel="noopener" target="_blank">Help with account id</a>',
    'drip_api_token_label' => 'Drip API Token',
    'drip_api_token_instruction' => 'You can find this on your User Settings page of your account. You may need to ask Drip to enable API access for your account.',
    'drip_api_token_tip' => '<a href="https://www.drip.com/learn/docs/manual/user-settings/settings" class="go" rel="noopener" target="_blank">Help with API tokens</a>',
    'drip_snippet_label' => 'Drip JavaScript Snippet',
    'drip_snippet_instruction' => 'Paste your javascript snippet here and it will be added to all pages.',
    'drip_snippet_tip' => '<a href="https://www.drip.com/learn/docs/manual/settings/account" class="go" rel="noopener" target="_blank">Help with javascript snippet</a>',
    'drip_event_name' => 'Event Name',
    'drip_event_name_default' => 'This will default to {default}',
    'event_freeform_submission' => '{formName} Freeform Submission',
    'event_core_create' => 'Account Activated',
    'event_core_login' => 'User Account Login',
    'event_core_logout' => 'User Account Logout',
    'event_core_update' => 'User Account Update',
    'Enable to track events for the {name}' => 'Enable to track events on the {name}',
    'drip_save_to_test' => 'Save your API token and account id then click below to test the connection to Drip.',
    'drip_test_api' => 'Test API Connection'
];
