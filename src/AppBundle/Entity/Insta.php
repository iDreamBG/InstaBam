<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Insta
 *
 * @ORM\Table(name="instabam")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InstaRepository")
 */
class Insta
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
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAdded", type="datetime")
     */
    private $dateAdded;

    /**
     * @var int
     * @ORM\Column(name="authorId", type="integer")
     */
    private $authorId;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="instabam")
     * @ORM\JoinColumn(name="authorId", referencedColumnName="id")
     */
    private $author;

    /**
     * @var int
     *
     * @ORM\Column(name="likesCounter", type="integer")
     */
    private $likesCounter;

    /**
     * Many insta can have Many Users likes.
     * @ORM\ManyToMany(targetEntity="User", mappedBy="instaLikes")
     */
    private $userLikes;

    public function __construct()
    {
        $this->dateAdded = new \DateTime('now');
        $this->userLikes = new ArrayCollection();
        $this->likesCounter = 0;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Insta
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     *
     * @return Insta
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Get dateAdded
     *
     * @return string
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
   }

    /**
     * @return int
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }

    /**
     * @param integer $authorId
     *
     * @return Insta
     */
    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;
        return $this;
    }
    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param User $author
     * @return Insta
     */
    public function setAuthor(User $author = null)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return int
     */
    public function getLikesCounter()
    {
        return $this->likesCounter;
    }

    public function incrementLikesCounter()
    {
        $this->likesCounter++;
    }

    public function decrementLikesCounter()
    {
        $this->likesCounter--;
    }

    /**
     * @return ArrayCollection
     */
    public function getUserLikes()
    {
        return $this->userLikes;
    }

    public function setUserLike(User $userLike)
    {
        $this->userLikes[] = $userLike;
        return $this;
    }

    public function removeUserLike(User $userLike)
    {
        $this->userLikes->removeElement($userLike);
        return $this;
    }
}

