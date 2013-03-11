<?php
/**
 * This widget is designed to generate more user friendly autocomplete widgets.
 *
 * @package     symfony
 * @subpackage  widget
 * @link        https://github.com/ivaynberg/select2
 * @author      Ing. Gerhard Schranz <g.schranz@bgcc.at>
 * @version     1.0 2012-08-09
 */
class BgWidgetFormI18nSelect2ChoiceCountry extends sfWidgetFormI18nChoiceCountry
{
    /**
     * Configures the current widget.
     *
     * Available options:
     *
     *  * url:            The URL to call to get the choices to use (required)
     *  * config:         A JavaScript array that configures the JQuery autocompleter widget
     *  * value_callback: A callback that converts the value before it is displayed
     *
     * @param array $options     An array of options
     * @param array $attributes  An array of default HTML attributes
     *
     * @see sfWidgetForm
     */
    protected function configure($options = array(), $attributes = array())
    {
        $this->addOption('width', 'resolve');

        parent::configure($options, $attributes);
    }

    /**
     * @param  string $name        The element name
     * @param  string $value       The date displayed in this widget
     * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
     * @param  array  $errors      An array of errors for the field
     *
     * @return string An HTML tag string
     *
     * @see sfWidgetForm
     */
    public function render($name, $value = null, $attributes = array(), $errors = array())
    {
        $id = $this->generateId($name);

        $return = parent::render($name, $value, $attributes, $errors);

        $return .= sprintf(<<<EOF
<script type="text/javascript">
function formatResult(item)
{
    return item.text;
}

jQuery("#%s").select2(
{
    width:              '%s',
    allowClear:         %s,
});
</script>
EOF
            ,
            $id,
            $this->getOption('width'),
            $this->getOption('add_empty') == true ? 'true' : 'false'
        );

        return $return;
    }

    /**
     * Gets the stylesheet paths associated with the widget.
     *
     * @return array An array of stylesheet paths
     */
    public function getStylesheets()
    {
        return array('/sfSelect2WidgetsPlugin/select2/select2.css' => 'all');
    }

    /**
     * Gets the JavaScript paths associated with the widget.
     *
     * @return array An array of JavaScript paths
     */
    public function getJavascripts()
    {
        return array('/sfSelect2WidgetsPlugin/select2/select2.js');
    }
}
