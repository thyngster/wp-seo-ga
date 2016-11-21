# SEO Meets Google Analytics

[TOC]

What's this?
-----------------

Is a Wordpress Plugin to log search bots visits to Google Analytics. This plugin used the uap-core library for PHP in order to detect the bots based on their User Agent.

Important Notes
------------
This  plugin is currently in Alpha/Beta Status, this means I do not offer any warranty. As I'm not a developer I'm trying to do all I can so the code is the most fail-proof as possible. If you find some bug, or if you have any improvement, please drop me a line or open an issue.

Installation
------------
Firstly you need to upload a the wp-seo-ga folder to your wp-content/plugins folder.
After that just activate the plugin, and setup the Property ID where you want to send the data.

This integration does use custom dimensions in order to track a lot of extra bots/requests info. Please refer to Custom Dimensions sections in order to configure your Google Analytics Property.

> **Note:** The custom dimensions setup is not needed at all if you only want to track the pages visited by bots, but we definetly recomended setting up a new property with those custom dimensions in order to track the bot names, the response, codes, etc

Custom Dimensions
--------------------

| Custom Dimension        | Value                 | Scope  |
| ----------------------- | :-------------------- | -----: |
|#1                       |Bot Name               |User    |
|#2                       |Bot Device             |User    |
|#3                       |Bot Model              |User    |
|#4                       |Original User Agent    |Hit     |
|#5                       |Response Status        |Hit     |
|#6                       |Client ID              |User    |
|#7                       |User ID                |User    |
|#8                       |Total Memory Used      |Hit     |
|#9                       |Total Processing Time  |Hit     |
|#10                      |Total Number of Queries|Hit     |
|#11                      |Bot Name               |Hit     |
|#12                      |Bot Device             |Hit     |
|#13                      |Bot Model              |Hit     |
|#14                      |Request Method         |Hit     |
|#15                      |Server Protocol        |Hit     |
|#16                      |Reverse Hostname       |User    |


# To-Do

 - Add sanity checks before being able to turn on the plugin
	 - PHP Version
	 - gethostbyaddr availability
	 - Check Streams availability for sending hits
 - Improve the plugin admin ui
 - Add 500 errors tracking, based on register_shutdown_function (Needs PHP> 5.2)
 - Add Predefined Report templates to the documentation

# ChangeLog

v0.2

 - Full code refactoring and cleaning up
 - Added Request Method Dimension
 - Added Server Protocol Dimension
 - Added the requesting IP address reverse domain root name

v0.1


 - First Public Release

# Support the plugin


This is a free to use plugin for Wordpress, and so it will continue to be. Any coding help, suggestions, bug finding, will be appreciated.

I'm coding the plugin on my free time, if you found the plugin useful, you may want to donate some money for the project. Please use the following button:

David Vallejo

[@thyng](https://www.thyngster.com/thyng)
