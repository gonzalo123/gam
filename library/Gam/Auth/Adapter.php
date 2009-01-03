<?php
class Gam_Auth_Adapter implements Zend_Auth_Adapter_Interface
{
    private $_user = null;
    private $_pass = null;
    private $_authenticateResultInfo = array();
            
    /**
     * Sets username and password for authentication
     *
     * @return void
     */
    public function __construct($username, $password)
    {
        $this->_user = $username;
        $this->_pass = $password;
    }

    /**
     * Performs an authentication attempt
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot
     *                                     be performed
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        $this->_authenticateResultInfo['code'] = null;
        $this->_authenticateResultInfo['identity'] = null;
        $this->_authenticateResultInfo['messages'] = array();
            
        //Zend_Auth_Result::SUCCESS
        //Zend_Auth_Result::FAILURE
        //Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND
        //Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS
        //Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID
        //Zend_Auth_Result::FAILURE_UNCATEGORIZED
        if ($this->_user == '' && $this->_pass == '') {
            $this->_authenticateResultInfo['code'] = Zend_Auth_Result::SUCCESS;
            $this->_authenticateResultInfo['identity'] = $this->_user;
        }
        return new Zend_Auth_Result(
            $this->_authenticateResultInfo['code'],
            $this->_authenticateResultInfo['identity'],
            $this->_authenticateResultInfo['messages']
            );
    }
}