=== Messenger Customer Chat ===
Contributors: facebook
Tags: Facebook, Messenger, Customer Care, Chat, Messaging
Requires at least: 3.9
Tested up to: 5.1
Stable tag: 1.2
Requires PHP: 5.2.4
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The official Messenger customer chat plugin from Facebook.

== Description ==
Messenger customer chat is the official free Messenger customer chat plugin for WordPress by Facebook. This plugin allows you to interact with your customers using Messenger by integrating it on your WordPress website. You can learn more about Messenger customer chat by clicking [here](https://developers.facebook.com/docs/messenger-platform/discovery/customer-chat-plugin).

== Installation ==

= Pre-requisites =
__Requires WordPress Version:__ 3.9 or higher
<br />

__Requires PHP Version:__ 5.2.4 or higher
<br />

__Facebook Presence__ <br />
In order to use the Messenger customer chat plugin, you will need to have a published Facebook Page. You can find a list of your Facebook Pages by following this [link](https://www.facebook.com/bookmarks/pages).  If you do not have a Facebook Page, you can create one for free [here](https://www.facebook.com/pages/creation/?ref_type=pages_you_admin).
Once you have your Facebook Page ready, you will be asked to login to Facebook on the computer or device you are using to install the plugin. You can then follow the directions below.

= Installing =
To install the Messenger customer chat plugin: <br />
1. Log in to your WordPress dashboard, navigate to the Plugins menu and click Add New. <br />
2. In the search field, type 'Messenger customer chat' and click 'Search Plugins'. Select the plugin authored by 'Facebook'. You can install it by simply clicking 'Install Now'.

= Updating =
Following the [Facebook platform and versioning](https://developers.facebook.com/docs/apps/versions), all versions will be supported for 2 years from launch. Updating should be safe and easy.

= Configure plugin for first use =
After navigating to the Messenger customer chat plugin settings page, click on 'Setup Customer Chat.' A new window will appear for setting up the plugin and you will be asked to select the Facebook Page you want to use. Next, you will go through the plugin setup with the following steps:
1. You will be asked to select the language and you can customize the greeting message. The default greeting is "Hi! How can we help you?"
2. The next screen allows you to select your response time and chat color. By setting the response time, you can set expectations with your customers on when they will receive a response
3. Next, click on 'Finish' to save these settings and click 'Done' to close this window. The Messenger customer chat plugin code should now be visible in your WordPress Dashboard
4. Finally, clicking 'Save Changes' will save the plugin onto your WordPress site. You can edit this code manually by clicking 'Edit Code' or rerun the setup by clicking 'Edit Customer Chat'

<br />
That's it! You're all set. Now all visitors to your WordPress website will see the Messenger customer chat plugin and can message you.

= Support =
If you get stuck, or have any questions, you can ask for help in the [Messenger Customer Chat plugin forum](https://wordpress.org/support/plugin/facebook-messenger-customer-chat).

== Screenshots ==
1. Messenger customer chat on your website
2. Messenger customer chat settings page on first installation
3. Select Facebook Page in setup
4. Set your desired language and customize the greeting text
5. Set your response time and customize your chat color
6. Messenger customer chat settings page after successful setup

== Frequently Asked Questions ==

= Where can I find more information on Messenger customer chat? =
You can find more information on the [Messenger customer chat page](https://developers.facebook.com/docs/messenger-platform/discovery/customer-chat-plugin).

= What do I need before setting up Messenger customer chat on my website? =
You will need to have a published Facebook Page and be logged into Facebook on your computer or device.

= How does the plugin work? =
The plugin is a snippet of JavaScript code that runs on your WordPress website. There will be a small Messenger chat bubble that loads with your website in the lower right corner. Your customers can click on it at anytime and message you. It works on both mobile and desktop. You can find more information in our Developer Docs on the [Messenger customer chat page](https://developers.facebook.com/docs/messenger-platform/discovery/customer-chat-plugin).

= Where can I see all my messages? =
You can see all messages and responses to your Facebook Page in your Page Inbox. Navigate to your Facebook Page on Facebook and click on 'Inbox' at the top.

= What permissions do I need on a Page to enable the Messenger customer chat feature? =
You need be an administrator of the Page.

= Why are some of my users seeing an additional confirmation window after clicking "Chat Now"? =
A "Continue As" popup is shown infrequently for security purposes. Most signed-in users will only have to click the plugin itself to opt in, without having to re-confirm. Additionally, as more users message you via the plugin, the "Continue As" popup will be shown less often.

= Can I see messaging analytics for my Facebook page? =
You can see all messages and responses to your Facebook Page in your Page Inbox. Additionally, you can view analytics for your Facebook Page in Page Analytics or via Facebook Analytics. You can learn more about Page Analytics [here](https://developers.facebook.com/docs/analytics/getting-started/pages). If you want more insights on your Facebook Page, visit Page Insights [here](https://www.facebook.com/business/a/page/page-insights).

= Where can I find support? =
If you get stuck, or have any questions, you can ask for help in the [Messenger Customer Chat plugin forum](https://wordpress.org/support/plugin/facebook-messenger-customer-chat). If you would like to file a bug, please use the Facebook Bug tool found [here](https://developers.facebook.com/support/bugs/).

= I am a developer. Can I help improve the plugin? =
Of course! This plugin is open sourced on the Facebook Incubator GitHub. You can find the code and contribution instructions in the [WordPress Messenger customer chat plugin repository](https://github.com/facebookincubator/wordpress-messenger-customer-chat-plugin).

== Changelog ==

= 1.2 - February 28, 2018 =
* Tested up to WordPress 5.1
* Fixed issues with Safari
* Fix: Edge case where multiple SDK loaded on the page resulting with plugin not loading

= 1.1 - October 31, 2018 =
* Updated plugin to use new Facebook JS SDK
* Fix: Error message in code snippet

= 1.0 - September 24, 2018 =
* Plugin released for all users

== Upgrade Notice ==

= 1.2 =
* Handle edge case which resulted in plugin being broken for some users.

= 1.1 =
* Upgraded plugin to use new Facebook JS SDK and minor bug fixes.
