<?php
require_once(dirname(__FILE__) . '/../select2/Select2.class.php');

/**
 * This widget is designed to generate more user friendly autocomplete widgets.
 *
 * @package     symfony
 * @subpackage  widget
 * @link        https://github.com/19Gerhard85/sfSelect2WidgetsPlugin
 * @author      Ing. Gerhard Schranz <g.schranz@bgcc.at>
 * @version     0.1 2013-03-11
 */
class sfWidgetFormSelect2PropelChoiceSortable extends sfWidgetFormInput
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
        $this->addOption('culture', sfContext::getInstance()->getUser()->getCulture());
        $this->addOption('width', sfConfig::get('sf_sfSelect2Widgets_width'));
        $this->addRequiredOption('model');
        $this->addOption('add_empty', false);
        $this->addOption('method', '__toString');
        $this->addOption('key_method', 'getPrimaryKey');
        $this->addOption('order_by', null);
        $this->addOption('query_methods', array());
        $this->addOption('criteria', null);
        $this->addOption('connection', null);
        $this->addOption('multiple', false);
        // not used anymore
        $this->addOption('peer_method', 'doSelect');

        parent::configure($options, $attributes);
        $this->setOption('type', 'hidden');
    }

    public function getChoices()
    {
        $choices = array();
        if (false !== $this->getOption('add_empty'))
        {
            $choices[''] = true === $this->getOption('add_empty') ? '' : $this->getOption('add_empty');
        }

        $criteria = PropelQuery::from($this->getOption('model'));
        if ($this->getOption('criteria'))
        {
            $criteria->mergeWith($this->getOption('criteria'));
        }
        foreach ((array)$this->getOption('query_methods') as $methodName => $methodParams)
        {
            if(is_array($methodParams))
            {
                $criteria = call_user_func_array(array($criteria, $methodName), $methodParams);
            }
            else
            {
                $criteria = $criteria->$methodParams();
            }
        }
        if ($order = $this->getOption('order_by'))
        {
            $criteria->orderBy($order[0], $order[1]);
        }
        $objects = $criteria->find($this->getOption('connection'));

        $methodKey = $this->getOption('key_method');
        if (!method_exists($this->getOption('model'), $methodKey))
        {
            throw new RuntimeException(sprintf('Class "%s" must implement a "%s" method to be rendered in a "%s" widget', $this->getOption('model'), $methodKey, __CLASS__));
        }

        $methodValue = $this->getOption('method');
        if (!method_exists($this->getOption('model'), $methodValue))
        {
            throw new RuntimeException(sprintf('Class "%s" must implement a "%s" method to be rendered in a "%s" widget', $this->getOption('model'), $methodValue, __CLASS__));
        }

        foreach ($objects as $object)
        {
            $choices[$object->$methodKey()] = $object->$methodValue();
        }

        if (count($choices) > 0 && isset($choices['']) && $choices[''] == '') {
            $choices[''] = '&nbsp;';
        }

        return $choices;
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

        $choices = $this->getChoices();
        $choices_render = array();

        foreach($choices as $key => $choice)
        {
            $choices_render[] = array(
                'id' => $key,
                'text' => $choice
            );
        }

        $choices_render = json_encode($choices_render);
        $value_render = json_encode($value);

        $return = parent::render($name, null, $attributes, $errors);

        $return .= sprintf(<<<EOF
<script type="text/javascript">
function formatResult(item)
{
    return item.text;
}

jQuery("#%s").select2(
{
    tags: $choices_render,
    width:              '%s',
    allowClear:         %s
});

jQuery("#$id").select2("val", $value_render);

jQuery("#$id").select2("container").find("ul.select2-choices").sortable({
    containment: 'parent',
    start: function() { $("#$id").select2("onSortStart"); },
    update: function() { $("#$id").select2("onSortEnd"); }
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
        return Select2::addStylesheets();
    }

    /**
     * Gets the JavaScript paths associated with the widget.
     *
     * @return array An array of JavaScript paths
     */
    public function getJavascripts()
    {
        return Select2::addJavascripts($this->getOption('culture'));
    }
}
