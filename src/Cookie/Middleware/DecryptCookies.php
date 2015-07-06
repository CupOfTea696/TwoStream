<?php namespace CupOfTea\TwoStream\Cookie\Middleware;

use Closure;
use Exception;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\Encrypter as EncrypterContract;

class DecryptCookies implements Middleware
{
    
	/**
	 * The encrypter instance.
	 *
	 * @var \Illuminate\Contracts\Encryption\Encrypter
	 */
	protected $encrypter;
    
	/**
	 * Create a new CookieGuard instance.
	 *
	 * @param  \Illuminate\Contracts\Encryption\Encrypter  $encrypter
	 * @return void
	 */
	public function __construct(EncrypterContract $encrypter)
	{
		$this->encrypter = $encrypter;
	}
    
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		return $next($this->decrypt($request));
	}
    
	/**
	 * Decrypt the cookies on the request.
	 *
	 * @param  \Symfony\Component\HttpFoundation\Request  $request
	 * @return \Symfony\Component\HttpFoundation\Request
	 */
	protected function decrypt(Request $request)
	{
		foreach ($request->cookies as $key => $c)
		{
			try
			{
				$request->cookies->set($key, $this->decryptCookie($c));
			}
			catch (DecryptException $e)
			{
				$request->cookies->set($key, null);
			}
		}
        
		return $request;
	}
    
	/**
	 * Decrypt the given cookie and return the value.
	 *
	 * @param  string|array  $cookie
	 * @return string|array
	 */
	protected function decryptCookie($cookie)
	{
		if (is_array($cookie)) {
            return $this->decryptArray($cookie);
        } else {
            try {
                return $this->encrypter->decrypt(urldecode($cookie));
            } catch (Exception $e) {
                return $this->encrypter->decrypt($cookie);
            }
        }
	}
    
	/**
	 * Decrypt an array based cookie.
	 *
	 * @param  array  $cookie
	 * @return array
	 */
	protected function decryptArray(array $cookie)
	{
		$decrypted = array();
        
		foreach ($cookie as $key => $value)
		{
            try {
                $decrypted[$key] = $this->encrypter->decrypt(urldecode($value));
            } catch (Exception $e) {
                $decrypted[$key] = $this->encrypter->decrypt($value);
            }
		}
        
		return $decrypted;
	}
    
	/**
	 * Duplicate a cookie with a new value.
	 *
	 * @param  \Symfony\Component\HttpFoundation\Cookie  $c
	 * @param  mixed  $value
	 * @return \Symfony\Component\HttpFoundation\Cookie
	 */
	protected function duplicate(Cookie $c, $value)
	{
		return new Cookie(
			$c->getName(), $value, $c->getExpiresTime(), $c->getPath(),
			$c->getDomain(), $c->isSecure(), $c->isHttpOnly()
		);
	}
    
}
