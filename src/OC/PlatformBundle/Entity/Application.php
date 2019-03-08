<?php

namespace OC\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Application
 *
 * @ORM\Table(name="application")
 * @ORM\Entity(repositoryClass="OC\PlatformBundle\Repository\ApplicationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Application
{
    # Plusieurs candidatures peuvent être liées à 1 annonce
    # C'est le côté Many qui est propriétaire (qui a une colonne référence, ici advert_id)
    # inversedBy pour signaler la relation bidirectionnelle
    /**
    * @ORM\ManyToOne(targetEntity="OC\PlatformBundle\Entity\Advert", inversedBy="applications")
    * @ORM\JoinColumn(nullable=false)
    */
    # JoinColumn(nullable=false) interdit la création d'une candidature sans annonce
    private $advert;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    public function __construct()
    {
      // Par défaut, la date de la candidature est la date d'aujourd'hui
      $this->date = new \Datetime();
    }


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set author.
     *
     * @param string $author
     *
     * @return Application
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author.
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set content.
     *
     * @param string $content
     *
     * @return Application
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return Application
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    // Pas de = null ici, car relation obligatoire définie plus haut
    public function setAdvert(Advert $advert)
    {
      $this->advert = $advert;
      return $this;
    }

    public function getAdvert()
    {
      return $this->advert;
    }

    /**
    * @ORM\PrePersist
    */
    public function increase()
    {
      $this->getAdvert()->increaseApplication();
    }

    /**
    * @ORM\PreRemove
    */
    public function decrease()
    {
      $this->getAdvert()->decreaseApplication();
    }
}
