<?php 

class Pinciuc_Form_DisplayGroup extends Zend_Form_DisplayGroup
{

    /**
     * Element label
     * @var string
     */
    protected $_label;

    /**
     * Set element label
     *
     * @param  string $label
     * @return Zend_Form_Element
     */
    public function setLabel($label)
    {
        $this->_label = (string) $label;
        return $this;
    }

    /**
     * Retrieve element label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->_label;
    }

    /**
     * Is the element required?
     * (required by the "Label" decorator)
     *
     * @return bool
     */
    public function isRequired()
    {
    	foreach ($this->getElements() as $element) {
    		if ($element->isRequired()) {
    			return TRUE;
    		}
    	}
        return FALSE;
    }

    /**
     * Render display group
     * 
     * @return string
     */
    public function render(Zend_View_Interface $view = null)
    {
        if (null !== $view) {
            $this->setView($view);
        }
        $content = '';
        $firstElement = current($this->getElements());
        foreach ($this->getDecorators() as $decorator) {
        	// We want a Label decorator to identify with our first form element.
        	if ($firstElement && (get_class($decorator) == 'Zend_Form_Decorator_Label')) {
        		$decorator->setId($firstElement->getId());
        	}
            $decorator->setElement($this);
            $content = $decorator->render($content);
        }
        return $content;
    }

    
    /**
     * Retrieve error messages
     *
     * @return array
     */
    public function getMessages()
    {
    	$messages = array();
    	foreach ($this->getElements() as $element) {
    		foreach ($element->getMessages() as $key => $val) {
    			$messages[$element->getName()."-$key"] = $val;
    		}
    	}
    	return $messages;
    }
}
