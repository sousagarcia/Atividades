<?php
namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Control\TAction;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TField;
use Adianti\Widget\Form\TCheckButton;

use Exception;

/**
 * A group of CheckButton's
 *
 * @version    2.0
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TCheckGroup extends TField implements AdiantiWidgetInterface
{
    private $layout = 'vertical';
    private $changeAction;
    private $items;
    private $breakItems;
    private $buttons;
    private $labels;
    protected $formName;

    /**
     * Class Constructor
     * @param  $name name of the field
     */
    public function __construct($name)
    {
        parent::__construct($name);
        parent::setSize(NULL);
    }
    
    /**
     * Define the direction of the options
     * @param $direction String (vertical, horizontal)
     */
    public function setLayout($dir)
    {
        $this->layout = $dir;
    }
    
    /**
     * Get the direction (vertical or horizontal)
     */
    public function getLayout()
    {
        return $this->layout;
    }
    
    /**
     * Define after how much items, it will break
     */
    public function setBreakItems($breakItems)
    {
        $this->breakItems = $breakItems;
    }
    
    /**
     * Add items to the check group
     * @param $items An indexed array containing the options
     */
    public function addItems($items)
    {
        if (is_array($items))
        {
            $this->items = $items;
            $this->buttons = array();
            $this->labels  = array();

            foreach ($items as $key => $value)
            {
                $button = new TCheckButton("{$this->name}[]");
                $button->setProperty('checkgroup', $this->name);
                $button->setIndexValue($key);
                
                $obj = new TLabel($value);
                $this->buttons[$key] = $button;
                $this->labels[$key] = $obj;
            }
        }
    }
    
    /**
     * Return the option buttons
     */
    public function getButtons()
    {
        return $this->buttons;
    }

    /**
     * Return the option labels
     */
    public function getLabels()
    {
        return $this->labels;
    }
    
    /**
     * Return the post data
     */
    public function getPostData()
    {
        if (isset($_POST[$this->name]))
        {
            return $_POST[$this->name];
        }
        else
        {
            return array();
        }
    }
    
    /**
     * Define the action to be executed when the user changes the combo
     * @param $action TAction object
     */
    public function setChangeAction(TAction $action)
    {
        if ($action->isStatic())
        {
            $this->changeAction = $action;
        }
        else
        {
            $string_action = $action->toString();
            throw new Exception(AdiantiCoreTranslator::translate('Action (^1) must be static to be used in ^2', $string_action, __METHOD__));
        }
    }
    
    /**
     * Enable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function enableField($form_name, $field)
    {
        TScript::create( " tcheckgroup_enable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Disable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function disableField($form_name, $field)
    {
        TScript::create( " tcheckgroup_disable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * clear the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function clearField($form_name, $field)
    {
        TScript::create( " tcheckgroup_clear_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        if ($this->items)
        {
            // iterate the checkgroup options
            $i = 0;
            foreach ($this->items as $index => $label)
            {
                $button = $this->buttons[$index];
                $button->setName($this->name.'[]');
                // verify if the checkbutton is checked
                if (@in_array($index, $this->value))
                {
                    $button->setValue($index); // value=indexvalue (checked)
                }
                
                // create the label for the button
                $obj = $this->labels[$index];
                $obj->{'class'} = 'tcheckgroup_label';
                $obj->setTip($this->tag->title);
                
                if ($this->getSize() AND isset($this->breakItems))
                {
                    $obj->setSize($this->getSize());
                }

                // check whether the widget is non-editable
                if (parent::getEditable())
                {
                    if (isset($this->changeAction))
                    {
                        if (!TForm::getFormByName($this->formName) instanceof TForm)
                        {
                            throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()') );
                        }
                        $string_action = $this->changeAction->serialize(FALSE);
                        $button->setProperty('onChange', "serialform=(\$('#{$this->formName}').serialize());
                                                          __adianti_ajax_lookup('$string_action&'+serialform, this)");
                    }
                }
                else
                {
                    $button->setEditable(FALSE);
                    $obj->setFontColor('gray');
                }
                
                $obj->add($button);
                $obj->show();
                $i ++;
                
                if ($this->layout == 'vertical' OR ($this->breakItems == $i))
                {
                    // shows a line break
                    $br = new TElement('br');
                    $br->show();
                    $i = 0;
                }
                echo "\n";
            }
        }
    }
}
