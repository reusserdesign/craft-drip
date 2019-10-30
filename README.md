# Drip plugin for Craft CMS 3.x

Drip connector for Craft 3.2.x

![Screenshot](resources/img/plugin-logo.png)

## Requirements

This plugin requires Craft CMS 3.2.0 or later.

You will also need Drip account from http://www.getdrip.com.

## Installation

To install the plugin use the Craft Plugin Store or follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require extreme-creations/drip

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Drip.

## Drip Overview

The plugin can be used record events in Drip when a visitor performs certain actions.

Core Craft events that will be recorded in Drip:
* User account created
* User login
* User logout
* User account updates

The actions available will depend on the edition of Craft you are using. Certain features, such as the ability to record events relating to user account creation, will only be available if you are using Craft Pro edition.

If you are using Freeform plugin additional form submission events can also be tracked.
* Form Submission


## Configuring Drip

There are 3 fields that need to be populated with details from your Drip account.

* Account ID
* API token
* Javascript code snippet

Once you have added and saved your account details you can test the connection to the Drip APi.

The javascript snippet will be injected in to all pages on your website at the end of the body. 

## Using Drip

Once configured and connected to you Drip account events will be automatically recorded in Drip.

## Drip Roadmap

* Look out for future updates providing more features and support for Craft Commerce.

Brought to you by [Extreme](madebyextreme.com)
