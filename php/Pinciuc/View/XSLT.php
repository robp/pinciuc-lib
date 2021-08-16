<?php
/** Zend_View_Abstract */
require_once('Zend/View/Abstract.php');

class Pinciuc_View_XSLT extends Zend_View_Abstract
{
    /**
     * XSLT object
     * @var XSLTProcessor
     */
    protected $_xslt;
    protected $_dom;
    protected $_assignedVars = array();
    private $_filter = array();
    public $dom;
    public $xml;

    /**
     * Constructor
     *
     * @param string $tmplPath
     * @param array $extraParams
     * @return void
     */
    public function __construct($tmplPath = null, $extraParams = array())
    {
        $this->_xslt = new XSLTProcessor;

        $this->_xslt->registerPHPFunctions(array('urlencode', 'urldecode'));

        // create XML DOM document
        $this->_dom = new DOMDocument('1.0', 'utf-8');
        $this->dom = new DOMDocument('1.0', 'utf-8');

        parent::__construct();

        if (null !== $tmplPath) {
            $this->setScriptPath($tmplPath);
        }

        foreach ($extraParams as $key => $value) {
            $this->_xslt->setParameter('', $key, $value);
        }
    }

    /**
     * performs the translation in a scope with only public $this variables.
     *
     * @param string The view script to execute.
     */
    protected function _run()
    {
        echo $this->_xslt->transformToXML($this->_dom);
    }

    /**
     * Applies the filter callback to a buffer.
     *
     * @param string $buffer The buffer contents.
     * @return string The filtered buffer.
     */
    private function _filter($buffer)
    {
        // loop through each filter class
        foreach ($this->_filter as $name) {
            // load and apply the filter class
            $filter = $this->getFilter($name);
            $buffer = call_user_func(array($filter, 'filter'), $buffer);
        }

        // done!
        return $buffer;
    }

    /**
     * Return the template engine object
     *
     * @return XSLTProcessor
     */
    public function getEngine()
    {
        return $this->_xslt;
    }

    /**
     * Assign a variable to the template
     *
     * @param string $key The variable name.
     * @param mixed $val The variable value.
     * @return void
     */
    public function __set($key, $val)
    {
        $this->_xslt->setParameter('', $key, $val);
        $this->_assignedVars[$key] = 1;
    }

    /**
     * Retrieve an assigned variable
     *
     * @param string $key The variable name.
     * @return mixed The variable value.
     */
    public function __get($key)
    {
        return $this->_xslt->getParameter('', $key);
    }

    /**
     * Allows testing with empty() and isset() to work
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return (null !== $this->_xslt->getParameter('', $key));
    }

    /**
     * Allows unset() on object properties to work
     *
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        $this->_xslt->removeParameter('', $key);
        unset($this->_assignedVars[$key]);
    }

    /**
     * Assign variables to the template
     *
     * Allows setting a specific key to the specified value, OR passing an array
     * of key => value pairs to set en masse.
     *
     * @see __set()
     * @param string|array $spec The assignment strategy to use (key or array of key
     * => value pairs)
     * @param mixed $value (Optional) If assigning a named variable, use this
     * as the value.
     * @return void
     */
    public function assign($spec, $value = null)
    {
        if (is_array($spec)) {
            $this->_xslt->setParameter('', $spec);
            foreach ($spec as $key => $val)
                $this->_assignedVars[$key] = 1;
            return;
        }

        $this->_xslt->setParameter('', $spec, $value);
        $this->_assignedVars[$spec] = 1;
    }

    /**
     * Clear all assigned variables
     *
     * Clears all variables assigned to Zend_View either via {@link assign()} or
     * property overloading ({@link __get()}/{@link __set()}).
     *
     * @return void
     */
    public function clearVars()
    {
        foreach (array_keys($this->_assignedVars) as $key)
            $this->_xslt->removeParameter('', $key);

        $this->_assignedVars = array();
    }

    /**
     * Processes a template and returns the output.
     *
     * @param string $name The template to process.
     * @return string The output.
     */
    public function render($name)
    {
        // find the script file name using the parent protected method
        $this->_file = $this->_script($name);
        unset($name); // remove $name from local scope
        
        // load the XSL stylesheet
        $xsl = new DOMDocument;
        $xsl->load($this->_file);
        
        // disable an annoying error for output method="xhtml"
        @$this->_xslt->importStyleSheet($xsl);

        if (strlen($this->xml)) {
            // tidy our input XML
            $options = array(
                'indent' => true,
                'input-xml' => true,
                'output-xml' => true,
                'fix-backslash' => false,
                'wrap' => false,
                'quiet' => true
            );
            /*
            foreach ($options as $key => $val)
                tidy_setopt($key, $val);
             */

            $xml = $this->xml;

            //tidy_parse_string($xml);
            //tidy_clean_repair();

            //Zend_Debug::dump(tidy_get_output());
            //$this->_dom = DOMDocument::loadXML(tidy_get_output());

            $fragment = $this->_dom->createDocumentFragment();
            $fragment->appendXML($xml);

            $this->dom->appendChild($fragment);
        }

        $this->_dom = $this->dom;

        ob_start();
        $this->_run();
        return $this->_filter(ob_get_clean()); // filter output
        return ob_get_clean(); // filter output
    }
}
