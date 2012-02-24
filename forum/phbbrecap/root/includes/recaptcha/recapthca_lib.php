<?php
/**
*
* @package phpBB3 reCAPTCHA
* @version $Id: recapthca_lib.php 8 2009-02-11 06:39:14Z xhotshotx $
* @copyright (c) 2009 Michael Williams (mtotheikle) http://startrekguide.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
 * reCAPTCHA Library by Michael Williams (mtotheikle). Based off the default reCAPTCHA php library that can be found here http://recaptcha.net/plugins/php/.
 */
class recapthca_lib
{
	// reCAPTCHA server URL's
	const RECAPTCHA_API_SERVER 			= 'http://api.recaptcha.net';
	const RECAPTCHA_API_SECURE_SERVER 	= 'https://api-secure.recaptcha.net';
	const RECAPTCHA_VERIFY_SERVER		= 'api-verify.recaptcha.net';
	
	// API Keys.
	public $public_key 	= '';
	public $private_key = '';
	
	// Response from reCAPTCHA server.
	public $response = '';
	
	// Error code if any from reCAPTCHA server.
	public $error = '';
	
	// True if user guessed image correctly
	public $valid = false;
	
	// Lets methods know if they should run. Will be set true by construct if mod has keys and can connect to the reCAPTCHA server.
	public $enabled = false;
	
	/**
	 * Set enable_confirm config to false and checks for private and public key.
	 *
	 * @return unknown
	 */
	public function __construct()
	{
		global $config, $user;

		// Check to make sure we have what we need.
		if (!$this->private_key || !$this->public_key)
		{
			// This should never happen, but if it does, we will force phpBB to show default CAPTCHA.
			$config['enable_confirm'] = 1;
			return false;
		}
		
		// Check connection to reCAPATCA server, if we can connect we will force phpBB to show default CAPTCHA.
        $errno 	= '';
        $errstr = '';
		$fs = fsockopen(self::RECAPTCHA_VERIFY_SERVER, 80, $errno, $errstr, 30);

        if (!$fs)
        {
        	$config['enable_confirm'] = 1;
			return false;
        }
        fclose($fs);
        
		// Set enable_confirm to false so phpBB script does not show confirmation image.
		$config['enable_confirm'] = 0;
		
		// Add language file.
		$user->add_lang('mods/recaptcha');
		
		// Set enabled to true so we see our wonderful image.
		$this->enabled = true;
	}
	
	/**
	 * Checks users response to capthca image.
	 *
	 * @param string $remote_ip
	 * @param string $challenge Challenge code information
	 * @param string $response Users reponse
	 * @param array $error
	 */
	public function check_answer($user_ip, $challenge, $response, &$error)
	{
		global $user;
		
		// Check for empty fields.
		if (!$response)
		{
			// Set error for reCAPTCHA system and phpBB system.
			$this->error = 'incorrect-captcha-sol';
			$error[] = $user->lang['NO_RECAP_CONFIRM_CODE'];
			return;
		}
		$data = array(
			'privatekey'	=> $this->private_key,
			'remoteip'		=> $user_ip,
			'challenge'		=> $challenge,
			'response'		=> $response
		);

		// Post data to reCAPTCHA server.
		$this->http_post(self::RECAPTCHA_VERIFY_SERVER, '/verify', $data);
		
		$answers = explode("\n", $this->response[1]);

		if (trim($answers[0]) == 'true')
		{
			$this->valid = true;
			return;
		}
		else
		{
			$this->error = $answers[1];
			$error[] = $user->lang['INVALID_RECAP_CONFIRM_CODE'];
			return;
		}
	}
	
	/**
	 * Gets HTLM required for CAPTCHA image from reCAPTCHA servers.
	 *
	 * @param bool $use_ssl
	 */
	public function get_html($use_ssl = false)
	{
		global $template;
		
		if ($use_ssl)
		{
			$server = self::RECAPTCHA_API_SECURE_SERVER;
		}
		else
		{
			$server = self::RECAPTCHA_API_SERVER;
		}
		
		$error = '';
		if ($this->error)
		{
			$error = '&amp;error=' . $this->error;
		}

		$server .= '/challenge?k=' . $this->public_key . $error;
		
		$template->assign_vars(array(
			'RECAPTCHA_SRC' 	=> $server,
			'RECAPTCHA_KEY'		=> $this->public_key,
			'RECAPTCHA_ERRROR'	=> $error,
			'S_RECAPTCHA_CODE'	=> true,			
		));
	}
	
	/**
	 * Encodes given data into url params.
	 *
	 * @param array $data
	 * @return Fromated url string
	 */
	private function encode_url($data)
	{
		$req = '';
		foreach ($data as $key => $value)
		{
			$req .= $key . '=' . urlencode(stripslashes($value)) . '&';
		}
		
		// Cut the last '&'
		$req = substr($req, 0, strlen($req) -1);

		return $req;
	}
	
	/**
	 * Posts data to reCAPTCHA server.
	 *
	 * @param string $host
	 * @param string $path
	 * @param string $data
	 * @param int $port
	 * @return true on error false on success
	 */
	private function http_post($host, $path, $data, $port = 80)
	{
		// Encode data for url.
		$request = $this->encode_url($data);

		// Setup http request.
		$http_request  = "POST $path HTTP/1.0\r\n";
        $http_request .= "Host: $host\r\n";
        $http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
        $http_request .= "Content-Length: " . strlen($request) . "\r\n";
        $http_request .= "User-Agent: reCAPTCHA/PHP\r\n";
        $http_request .= "\r\n";
        $http_request .= $request;
        
        $errno 	= '';
        $errstr = '';
        $fs = fsockopen($host, $port, $errno, $errstr, 30);
        
        if (!$fs)
        {
        	return true;
        }
        
        fwrite($fs, $http_request);
        
        while(!feof($fs))
        {
        	$this->response .= fgets($fs, 1024);
        }
        fclose($fs);
        
        $this->response = explode("\r\n\r\n", $this->response, 2);
        
        return false;
	}
}