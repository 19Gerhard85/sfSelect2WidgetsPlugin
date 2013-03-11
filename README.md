sfSelect2WidgetsPlugin
======================

Description
-----------
The `sfSelect2WidgetsPlugin` is a symfony 1.2/1.3/1.4 plugin that provides several form widgets with `Select2` functionality.
Following widgets are included:
  * I18n Choice Country
  * I18n Choice Currency
  * I18n Choice Language
  * Autocomplete (for [Propel ORM](https://github.com/propelorm/sfPropelORMPlugin))
  * Choice
  * Propel Choice (for [Propel ORM](https://github.com/propelorm/sfPropelORMPlugin))

Installation
------------
  * Install the plugin and init submodule

        $ cd plugins
        $ git clone git@github.com:19Gerhard85/sfSelect2WidgetsPlugin.git
        $ cd sfSelect2WidgetsPlugin
        $ git submodule update --init

  * Enable the plugin in your `/config/ProjectConfiguration.class.php`

        $this->enablePlugins('sfSelect2WidgetsPlugin');
  
  * Publish assets

        $ ./symfony plugin:publish-assets

  * Clear you cache

        $ ./symfony cc
        
Usage
-----

  
