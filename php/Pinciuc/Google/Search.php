<?php

class Pinciuc_Google_Search
{

    /**
     * Number of attempts to make, in case of network errors
     * @var integer
     */
    public $attempts = 3;

    /**
     * Google Search key
     * @var string
     */
    private $_key;

    /**
     * SOAP client object
     * @var SoapClient
     */
    private $_soapClient;

    /**
     * SOAP client options
     * @var string
     */
    private $_soapOptions; //= 'urn:GoogleSearch'

    /**
     * Limit search to specific domain name
     * @var string
     */
    private $_site;


    /**
     * Constructor
     *
     * @param string $key
     * @return void
     */
    public function __construct($key, $site) {
        $this->_key     = $key;
        $this->_site    = $site;
        $this->_soapClient = new SoapClient('http://api.google.com/GoogleSearch.wsdl');
    }

    /**
     * Calls the Google API and retrieves the search results in $ret
     * Retry is useful because sometimes the connection will
     * fail for some reason but will succeed when retried.
     *
     * @param string $q
     * @param string $type
     * @param integer $start
     * @param integer $display
     * @param array &$ret
     * @param integer $attempt
     * @return boolean
     */
    private function _search($q, $type, $start, $display, &$ret, $attempt = 1)
    {
        // Note that we pass in an array of parameters into the Google search.
        // The parameters array has to be passed by reference.
        // The parameters are well documented in the developer's kit on the
        // Google site http://www.google.com/apis

        // limit searches to this server
        //$sitequery = "$q site:{$_SERVER['SERVER_NAME']} $restrict";
        $sitequery = "$q site:{$this->_site} $restrict";

        $params = array(
                    'key' => $this->_key,
                    'q' => $sitequery,
                    'start' => $start,
                    'maxResults' => $display,
                    'filter' => false,
                    'restrict' => '',
                    'safeSearch' => false,
                    'lr' => '',
                    'ie' => '',
                    'oe' => '',
            );

        // Here's where we actually call Google using SOAP.
        // doGoogleSearch is the name of the remote procedure call.

        $ret = $this->_soapClient->__soapCall('doGoogleSearch', $params, $this->_soapOptions);
        //$ret = $soapclient->doGoogleSearch($params, $soapoptions);
        //$ret = $soapclient->doGoogleSearch($params);

        if (is_soap_fault($ret)) {
            if ($attempt < $this->attempts)
                return $this->_search($q, $type, $start, $display, &$ret, $attempt++);
            else {
                echo 'An error occurred!<br/>';
                echo "Error: $err<br/>";
                return false;
            }
        }

        return true;
    }


    /**
     * Does Google search
     *
     * @param string $q
     * @param string $type
     * @param integer $start
     * @param integer $display
     * @param array $ret
     * @return mixed
     */
    public function search( $q, $type, $start, $display, &$ret )
    {
        return $this->_search( $q, $type, $start, $display, $ret );
    }


    ////////////////////////////////////////////////////////////
    // Calls the Google API and retrieves the suggested spelling correction
    //
    /*
    function do_spell( $q, $key, &$spell, $attempt = 1 )
    {
        global $soapclient;
        global $soapoptions;

        $params = array(
                    'key' => $key,
                    'phrase' => $q,
            );

        $spell = $soapclient->__soapCall('doSpellingSuggestion', $params, $soapoptions);
        //$spell = $soapclient->call('doSpellingSuggestion', $params, $soapoptions);

        if (is_soap_fault($ret)) {
            if ($attempt < $attempts)
                return do_spell($q, $key, &$spell, $attempt++);
            else {
            print("<br/>An error occurred!<br/>");
            print(" Error: $err<br/>\n");
            return false;
        }
        }

        return true;
    }
    */
}
