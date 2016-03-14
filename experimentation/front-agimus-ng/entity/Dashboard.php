<?php

/**
* @ORM\Entity
* @ORM\Table(name="dashboard")
*/
class Dashboard {

    /**
    * @ORM\Id
    * @ORM\Column(type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    protected $id;

    /**
    * @ORM\Column(type="string", length=100)
    */
    protected $title;

    /**
    * @ORM\Column(type="text", nullable=true)
    */
    protected $description;

    /**
    * @ORM\Column(type="string", nullable=true)
    */
    private $roles;

    /**
    * @ORM\Column(type="string", length=250)
    */
    protected $url;

    private $graphes = array();


    public function getRoles()
    {
        return explode(',', $this->roles);      
    }

    /**
     * Set roles
     *
     * @param string $roles
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $Dashboard;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Dashboard
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Dashboard
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add graphes
     *
     * @param $graphe
     * @return Dashboard
     */
    public function addGraphe($graphe)
    {
        $this->graphes[] = $graphe;

        return $this;
    }

    /**
     * Set graphes
     *
     * @param $graphes
     * @return Dashboard
     */
    public function setGraphe($graphes)
    {
        $this->graphes = $graphes;

        return $this;
    }

    /**
     * Remove graphes
     *
     * @param $graphes
     */
    public function removeGraphe($graphes)
    {
        $this->graphes->removeElement($graphes);
    }

    /**
     * Get graphes
     *
     * @return array
     */
    public function getGraphes()
    {
        return $this->graphes;
    }
    /**
     * Set url
     *
     * @param string $url
     * @return Graphe
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    public function getCheckedUrl($from_time="", $to_time="") {
        //check if embed is in url
        $parsed_url = parse_url(str_replace('?','%3F',$this->url));
        
        //check port
        if(isset($parsed_url['port'])) unset($parsed_url['port']);

        //check embed
        $urlfragment = parse_url($this->url, PHP_URL_FRAGMENT);
        $parseurlfragment = parse_url($urlfragment);
        $urlqueryfragment = parse_url($urlfragment, PHP_URL_QUERY);

        //check time
        //_g=(time:(from:'2015-07-01',mode:absolute,to:'2015-07-30'))
        if($to_time=="") {
            $yesterday = mktime(0, 0, 0, date("m"), date("d")-1,   date("Y"));
            $to_time = date("Y-m-d", $yesterday);
        }

        if($from_time=="") {
            $lastmonth = mktime(0, 0, 0, date("m")-1, date("d")-1,   date("Y"));
            $from_time = date("Y-m-d",$lastmonth);
        }


        parse_str($urlqueryfragment, $get_array);
        $a="";
        if(isset($get_array["_a"])) $a=$get_array["_a"];
        elseif(isset($get_array["amp;_a"])) $a=$get_array["amp;_a"];

        if(isset($get_array["_g"])) $g=$get_array["_g"];
        elseif(isset($get_array["amp;_g"])) $g=$get_array["amp;_g"];

	if(isset($g)) {
		$g = preg_replace(
				array("/refreshInterval:\([^\)]*\)/","/time:\([^\)]*\)/"),
				array("refreshInterval:(display:Off,pause:!f,section:0,value:0)","time:(from:'".$from_time."',mode:absolute,to:'".$to_time."')"),
				$g);
		if (strpos( $g , "refreshInterval:(" ) === false ) {
			$g .= "refreshInterval:(display:Off,pause:!f,section:0,value:0)";
		}
		if (strpos( $g , "time:(" ) === false ) {
			$g .= "time:(from:'".$from_time."',mode:absolute,to:'".$to_time."')";
		}
	}
	else {
		$g = "(refreshInterval:(display:Off,pause:!f,section:0,value:0),time:(from:'".$from_time."',mode:absolute,to:'".$to_time."'))";
	}

        //preg_match('/timestamp:\[(.*) TO (.*)\]/', $a, $matches);

        if(preg_match('/timestamp:\[(.*) TO (.*)\]/', $a, $matches)==1) {
            $g="(refreshInterval:(display:Off,pause:!f,section:0,value:0),time:(from:".$matches[1].",mode:quick,to:".$matches[2]."))";
        }
        

        $new_query = "embed&_g=".$g."&_a=".$a;
        $parseurlfragment['query'] = $new_query;
        $urlfragment = $this->unparse_url($parseurlfragment);
        $parsed_url['fragment'] = $this->clean_fragment($urlfragment);

        $new_url = $this->unparse_url($parsed_url);
        
        return $new_url;
    }

    function clean_fragment($fragment) {
      return str_replace( '"' ,  '%22' , $fragment );
    }

    function unparse_url($parsed_url) { 
      $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : ''; 
      $host     = isset($parsed_url['host']) ? $parsed_url['host'] : ''; 
      $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : ''; 
      $user     = isset($parsed_url['user']) ? $parsed_url['user'] : ''; 
      $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : ''; 
      $pass     = ($user || $pass) ? "$pass@" : ''; 
      $path     = isset($parsed_url['path']) ? $parsed_url['path'] : ''; 
      $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : ''; 
      $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : ''; 
      return "$scheme$user$pass$host$port$path$query$fragment"; 
    }
}
