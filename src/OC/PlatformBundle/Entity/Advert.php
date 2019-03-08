<?php

namespace OC\PlatformBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
// use pour Validator
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Advert
 *
 * @ORM\Table(name="advert")
 * @ORM\Entity(repositoryClass="OC\PlatformBundle\Repository\AdvertRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Advert
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     * @Assert\DateTime()
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\Length(min=10)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     * @Assert\Length(min=2, minMessage="L'auteur doit faire au moins {{ limit }} caractères.")
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     * @Assert\NotBlank()
     */
    private $content;

    /**
    * @ORM\Column(name="published", type="boolean")
    */
    private $published = true;

    /**
    * @ORM\OneToOne(targetEntity="OC\PlatformBundle\Entity\Image", cascade={"persist"})
    * @Assert\Valid()
    */
    private $image;

    /**
    * @ORM\ManyToMany(targetEntity="OC\PlatformBundle\Entity\Category", cascade={"persist"})
    */
    private $categories;

    // Inverse du ManyToOne = OneToMany, pour le côté inverse
    // mappedBy : nom de la propiété de l'autre côté de la relation, dans Application
    /**
    * @ORM\OneToMany(targetEntity="OC\PlatformBundle\Entity\Application", mappedBy="advert")
    */
    private $applications; // Notez le « s », une annonce est liée à plusieurs candidatures

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
    * @ORM\Column(name="nb_applications", type="integer")
    */
    private $nbApplications = 0;

    /**
    * @Gedmo\Slug(fields={"title"})
    * @ORM\Column(name="slug", type="string", length=255, unique=true)
    */
    private $slug;

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
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return Advert
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

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Advert
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set author.
     *
     * @param string $author
     *
     * @return Advert
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
     * @return Advert
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
     * Set published.
     *
     * @param bool $published
     *
     * @return Advert
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published.
     *
     * @return bool
     */
    public function getPublished()
    {
        return $this->published;
    }

    # On force l'agument à être de type Image. On accepte les valeurs null car relation facultative
    public function setImage(Image $image = null)
    {
      $this->image = $image;
    }

    public function getImage()
    {
      return $this->image;
    }
    # Pour avoir l'url : $url = $advert->getImage()->getUrl();

    # En BDD, une colonne image_id est bien présente dans la table advert, cependant :
    # 1/ L'objet Advert NE contient PAS d'attribut image_id.
    # 2/ L'attribut image de l'objet Advert NE contient PAS l'id de l'Image liée, il contient une INSTANCE de la classe OC\PlatformBundle\Entity\Image qui, elle, contient un attribut id.

    # N'allez donc jamais m'écrire $advert->getImageId(), pour récupérer l'id de l'image liée, il faut d'abord récupérer l'Image elle-même puis son id, comme ceci : $advert->getImage()->getId()

  // Comme la propriété $categories doit être un ArrayCollection,
  // On doit la définir dans un constructeur :
    public function __construct()
    {
      $this->date       = new \Datetime();
      $this->categories = new ArrayCollection();
      $this->applications = new ArrayCollection();
    }

    // Notez le singulier, on ajoute une seule catégorie à la fois
    public function addCategory(Category $category)
    {
      // Ici, on utilise l'ArrayCollection vraiment comme un tableau
      $this->categories[] = $category;
    }

    public function removeCategory(Category $category)
    {
      // Ici on utilise une méthode de l'ArrayCollection, pour supprimer la catégorie en argument
      $this->categories->removeElement($category);
    }

    // Notez le pluriel, on récupère une liste de catégories ici !
    public function getCategories()
    {
      return $this->categories;
    }

    // on modifie le setter d'un côté, et on utilise ensuite ce setter-là. C'est simple, mais important à respecter. Donc $advert->addApplication() mais PAS $application->setAdvert()
    public function addApplication(Application $application)
    {
      $this->applications[] = $application;
      // On lie l'annonce à la candidature
      $application->setAdvert($this);
      return $this;
    }

    public function removeApplication(Application $application)
    {
      $this->applications->removeElement($application);
      // Et si notre relation était facultative (nullable=true, ce qui n'est pas notre cas ici attention) :
      // $application->setAdvert(null);
    }

    public function getApplications()
    {
      return $this->applications;
    }

    /**
    * @param \DateTime $updatedAt
    */
    public function setUpdatedAt(\Datetime $updatedAt = null)
    {
      $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
      return $this->updatedAt;
    }

    /**
    * @ORM\PreUpdate
    */
    public function updateDate()
    {
      $this->setUpdatedAt(new \Datetime());
    }

    /**
     * @return integer
     */
    public function getNbApplications()
    {
      return $this->nbApplications;
    }

    /**
     * @param integer $nbApplications
     */
    public function setNbApplications($nbApplications)
    {
      $this->nbApplications = $nbApplications;
    }

    public function increaseApplication()
    {
      $this->nbApplications ++;
    }

    public function decreaseApplication()
    {
      $this->nbApplications--;
    }


}
