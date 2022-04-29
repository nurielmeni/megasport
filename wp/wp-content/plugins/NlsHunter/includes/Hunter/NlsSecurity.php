<?php
require_once 'NlsConfig.php';
require_once 'NlsSoapClient.php';
require_once 'NlsHelper.php';
require_once 'NlsService.php';

/**
 * Handles the security with Niloosoft Services 
 */
class NlsSecurity
{
    private $nlsConfig;

    public function __construct($nlsConfig)
    {
        $this->nlsConfig = $nlsConfig;
    }
    /**
     * Authenticate the user against the service and gets an Auth object
     * @return auth object with user data and expiration time
     * @throws \app\modules\niloos\models\Exception
     */
    public function authenticate($username, $password)
    {
        if (substr($this->nlsConfig->getNlsSecurityService(), -strlen('?wsdl')) !== '?wsdl')
            return false;

        /** Define SOAP headers for token authentication **/
        $this->soap_headers = [
            new SoapHeader('_', 'NiloosoftCred0', $this->nlsConfig->getNlsConsumer()),
            new SoapHeader('_', 'NiloosoftCred1', $this->nlsConfig->getNlsDomain() . '\\' . $username),
            new SoapHeader('_', 'NiloosoftCred2', $password)
        ];

        try {
            $this->client = new NlsSoapClient($this->nlsConfig->getNlsSecurityService(), array(
                'trace' => 1,
                'exceptions' => 0,
                'cache_wsdl' => WSDL_CACHE_BOTH,
                'location' => explode('?', $this->nlsConfig->getNlsSecurityService())[0],
            ));
        } catch (\Exception $e) {
            $this->auth = false;
            throw new Exception("Could not create SOAP Client, Niloos security\n" . $e->getMessage());
        }

        $this->client->__setSoapHeaders($this->soap_headers);

        $transactionCode = NlsHelper::newGuid();
        try {
            $param[] = new SoapVar($this->nlsConfig->getNlsDomain() . '\\' . $username, XSD_STRING, null, null, 'userName', null);
            $param[] = new SoapVar($password, XSD_STRING, null, null, 'password', null);
            $param[] = new SoapVar($transactionCode, XSD_STRING, null, null, 'transactionCode', null);
            $param[] = new SoapVar($this->nlsConfig->getNlsConsumer(), XSD_STRING, null, null, 'applicationSecret', null);
            $options = new SoapVar($param, SOAP_ENC_OBJECT, null, null);

            $this->auth  = json_encode($this->client->__soapCall("Authenticate2", array($options)));
            update_option(NlsService::AUTH_KEY, $this->auth);
            return strpos($this->auth, 'faultstring') !== false ? false : $this->auth;
        } catch (\Exception $e) {
            $this->auth = false;
            $e->transactionCode = $transactionCode;
            throw new \Exception("Could not authenticate with Niloos security\n" . $e);
        }
        return false;
    }

    public function authenticateByConsumerKeyAndSecretKey($userName)
    {
        $transactionCode = NlsHelper::newGuid();
        try {
            $param = array(
                'userName' => $userName,
                "ConsumerKey" => null,
                "SecretKey" => null,
                "ipAddress" => "127.0.0.1",
                'transactionCode' => $transactionCode
            );
            $res = $this->client->AuthenticateByConsumerKeyAndSecretKey($param);
            return $res;
        } catch (Exception $e) {
            $this->auth = false;
            $e->transactionCode = $transactionCode;
            throw new \Exception("Could not authenticate with Niloos security\n" . $e);
        }
    }

    public function nls_auth_error_admin_notice()
    {
        echo '<div class="notice notice-error is-dismissible">
                    <p>Auth error, could not connect to Niloos Services</p>
                </div>';
    }

    /**
     * Checks if the app is authenticated and the auth is valid (not expired)
     * if not valid Authenticates
     * 
     * @return boolian true if authenticated
     */
    public function isAuth()
    {
        $this->auth = json_decode(get_option(NlsService::AUTH_KEY));
        try {
            if (!$this->auth || property_exists($this->auth, 'faultcode') || $this->auth->Authenticate2Result != "Success") {
                add_action('admin_notices', [$this, 'nls_auth_error_admin_notice']);
                $this->auth = false;
                return false;
            }
            $expTime = \DateTime::createFromFormat("d/m/Y H:i:s", explode("^^^^", $this->auth->UsernameToken)[1]);
            if ($expTime->getTimestamp() < time()) {
                add_action('admin_notices', [$this, 'nls_auth_error_admin_notice']);
                $this->auth = false;
                return false;
            }
        } catch (Exception $e) {
            $this->auth = false;
            $e->reason = "Authntication Failed";
            throw $e;
        }
        return $this->auth;
    }
}
