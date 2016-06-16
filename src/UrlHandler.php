<?php

namespace Finwo\Datatools;

class UrlHandler
{
    /**
     * @var string
     */
    protected $scheme;

    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var integer
     */
    protected $port;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $query = array();

    /**
     * @var string
     */
    protected $fragment;

    /**
     * UrlHandler constructor.
     *
     * @param $url
     */
    public function __construct($url = null)
    {
        if (gettype($url) == 'string') {
            $this->setUrl($url);
        }
    }

    /**
     * @param $query
     *
     * @return $this
     */
    public function setQuery( $query )
    {
        $this->query = parse_str($query);
        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function setQueryValue( $key, $value )
    {
        $this->query[$key] = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return http_build_query($this->query);
    }

    /**
     * getUrl()
     * Reconstructs the url
     *
     * @return string
     */
    public function getUrl()
    {
        return (isset($this->scheme)? $this->scheme . '://'   : '') .
            (isset($this->user)     ? $this->user             : '') .
            (isset($this->password) ? ':' . $this->password   : '') .
            (isset($this->user)     ? '@'                     : '') .
            (isset($this->host)     ? $this->host             : '') .
            (isset($this->port)     ? ':' . $this->port       : '') .
            (count($this->query)    ? '?' . $this->getQuery() : '') .
            (isset($this->fragment) ? '#' . $this->fragment   : '')
            ;
    }

    public function setUrl( $url )
    {
        $mapper = new \JsonMapper();
        $mapper->map(json_decode(json_encode($this->parseUrl($url))), $this);
    }

    // See http://php.net/manual/en/function.parse-url.php#116456
    // Modified, but still based upon that
    public function parseUrl( $url )
    {
        // Init the output var in advance
        $result = array();

        // Fetch fragment
        if(strpos($url,"#")>-1){
            $a=explode("#",$url,2);
            $url=$a[0];
            $result['fragment']=$a[1];
        }

        // Fetch query
        if(strpos($url,"?")>-1){
            $a=explode("?",$url,2);
            $url=$a[0];
            $result['query']=$a[1];
        }

        // Fetch scheme
        if(strpos($url,"://")>-1){
            $result['scheme']=substr($url,0,strpos($url,"//")-1);
            $url=substr($url,strpos($url,"//")+2,strlen($url));
        }

        // Fetch path
        if(strpos($url,"/")>-1){
            $a=explode("/",$url,2);
            $url=$a[0];
            $result['path']="/".$a[1];
        }

        // Fetch port
        if(strpos($url,":")>-1){
            $a=explode(":",$url,2);
            $url=$a[0];
            $result['port']=$a[1];
        }

        // Only the host remains
        if (strlen($url)) {
            $result['host'] = $url;
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @param string $scheme
     *
     * @return UrlHandler
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
        return $this;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $user
     *
     * @return UrlHandler
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return UrlHandler
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     *
     * @return UrlHandler
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $port
     *
     * @return UrlHandler
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return UrlHandler
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * @param string $fragment
     *
     * @return UrlHandler
     */
    public function setFragment($fragment)
    {
        $this->fragment = $fragment;
        return $this;
    }
}
