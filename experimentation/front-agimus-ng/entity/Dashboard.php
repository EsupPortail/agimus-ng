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
}
