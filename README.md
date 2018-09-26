# What is wordpress-messenger-customer-chat-plugin?

`wordpress-messenger-customer-chat-plugin` is Facebook Messenger's customer chat plugin for Wordpress.
This is the source code for the plugin which allows you to add a Messenger chat window directly onto
your website.

You can read more about Messenger customer chat [here](https://developers.facebook.com/docs/messenger-platform/discovery/customer-chat-plugin).

# Why did we create this?

Facebook wants to enable businesses to reach people wherever they are. This plugin
enables people to use Messenger and have one, persistent conversation across multiple
platforms. As recently published, [30% of websites on the web](https://venturebeat.com/2018/03/05/wordpress-now-powers-30-of-websites/)
are powered by Wordpress. The goal of the Wordpress plugin is to automatically
place code on a website, reducing friction, chance for error, and simplifying
the installation process. Once installed and setup, creators will be able to
engage their users in a more personal way. Users can get their questions answered
from more businesses without leaving their website, and continue the conversation
in Messenger without losing history or context.

Facebook already has a WordPress plugin for Instant Articles. With the success of
the Messenger customer chat plugin, the hope is to bring more Facebook features to
WordPress.

# Why not use an existing plugin?

* This plugin uses native Facebook UI to setup Messenger customer chat for your page
* Facebook is actively improving and supporting this

# How does it work?

The Facebook Messenger customer chat plugin triggers Facebook UI to setup customer
chat messaging for your Facebook Page. It requires you to be logged into Facebook
at time of setup. You will be asked to select the Facebook Page you'd like to setup
messaging for. You will then be taken through a guide to customize the plugin.
Once complete, some JavaScript will be injected into your WordPress website. When
visiting your website, users will see the Messenger chat bubble in the bottom
right corner which they can message you from.

For more information, please read the [readme.txt](https://github.com/facebookincubator/wordpress-messenger-customer-chat-plugin/blob/master/readme.txt).

# Requirements

This plugin requires at least WordPress version 3.9 and PHP version 5.2.4. There
are dependencies on WordPress libraries included in each WordPress download.

# Getting Started & Installation

Please clone this repo with the following commands:

```
$ mkdir -p ~/src/github.com/facebookincubator
$ cd $_
$ git clone https://github.com/facebookincubator/wordpress-messenger-customer-chat-plugin
```

After making any edits you want to make, zip up the directory so you can upload
it onto your WordPress website. The following command will zip the necessary files:

```
$ cd ~/src/github.com/facebookincubator
$ zip -r fb-messenger-customer-chat.zip wordpress-messenger-customer-chat-plugin/
```

You will need to download and install WordPress onto your computer for development
purposes. You can find instructions on how to do [here](https://codex.wordpress.org/Installing_WordPress).
Once installed and setup, go to the WordPress Dashboard. From the navigation pane,
select 'Plugins' and then 'Add New'. At the top, there will be a button that says
'Upload Plugin'. Click it, upload the zip file you just created, and then 'Install Now'.

Once installation is complete, you can follow directions listed
[readme.txt](https://github.com/facebookincubator/wordpress-messenger-customer-chat-plugin/blob/master/readme.txt)
to use the plugin.

# TODOs / future improvements

TODOs and improvements are tracked
[here](https://github.com/facebookincubator/wordpress-messenger-customer-chat-plugin/issues?q=is%3Aissue+is%3Aopen+label%3Aenhancement)

PRs are welcome!

# Who wrote it?

* Anish Bhayani ([@bhayani](https://github.com/bhayani)), Solutions Engineer
* Kent Wu ([@verswu](https://github.com/verswu)), Solutions Engineer
