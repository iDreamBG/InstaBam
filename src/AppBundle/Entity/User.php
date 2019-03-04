<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User implements UserInterface
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
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Insta", mappedBy="author")
     */
    private $instabam;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Role")
     * @ORM\JoinTable(name="users_roles",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")})
     */
    private $roles;

    /**
     * Many Users have Many Users.
     * @ORM\ManyToMany(targetEntity="User", mappedBy="following")
     */
    private $followers;

    /**
     * Many Users have many Users.
     * @ORM\ManyToMany(targetEntity="User", inversedBy="followers")
     * @ORM\JoinTable(name="followers",
     *      joinColumns={@ORM\JoinColumn(name="follower_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="followed_id", referencedColumnName="id")}
     *      )
     */
    private $following;

    /**
     * @var int
     *
     * @ORM\Column(name="followersCounter", type="integer")
     */
    private $followersCounter;

    /**
     * @var int
     *
     * @ORM\Column(name="followingCounter", type="integer")
     */
    private $followingCounter;

    /**
     * @var int
     *
     * @ORM\Column(name="instaCounter", type="integer")
     */
    private $instaCounter;

    /**
     * Many Users can like Many Insta.
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Insta")
     * @ORM\JoinTable(name="likes",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="insta_id", referencedColumnName="id")}
     *      )
     */
    private $instaLikes;

    public function __construct()
    {
        $this->instabam = new ArrayCollection();
        $this->roles = new ArrayCollection();

        $this->followers = new ArrayCollection();
        $this->following = new ArrayCollection();

        $this->instaLikes = new ArrayCollection();
        $this->followersCounter = 0;
        $this->followingCounter = 0;
        $this->instaCounter = 0;
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
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return array('ROLE_USER');
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        $stringRoles = [];
        foreach ($this->roles as $role)
        {
            /**
             * @var $role Role
             */
            $stringRoles[] = $role->getRole();
        }

        return $stringRoles;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return ArrayCollection
     */
    public function getInstabam()
    {
        return $this->instabam;
    }

    /**
     * @param Insta insta
     * @return User
     */
    public function addPost(Insta $insta)
    {
        $this->instabam[] = $insta;
        return $this;
    }

    /**
     * @param Role $role
     *
     * @return User
     */
    public function addRole(Role $role)
    {
        $this->roles[] = $role;
        return $this;
    }

    /**
     * @param Insta $insta
     * @return bool
     */
    public function isAuthor(Insta $insta) {
        return $insta->getAuthorId() === $this->getId();
    }

    /**
     * @return bool
     */
    public function isAdmin() {
        return in_array("ROLE_ADMIN", $this->getRoles());
    }

    /**
     * @return ArrayCollection
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * @param User $follower
     * @return User
     */
    public function setFollower(User $follower)
    {
        $this->followers[] = $follower;
        return $this;
    }

    public function removeFollower(User $follower)
    {
        $this->followers->removeElement($follower);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getFollowing()
    {
        return $this->following;
    }

    /**
     * @param User $following
     * @return User
     */
    public function setFollowing(User $following)
    {
        $this->following[] = $following;
        return $this;
    }

    public function removeFollowing(User $following)
    {
        $this->following->removeElement($following);
        return $this;
    }

    public function isUserFollowed(User $following)
    {
        return $this->following->contains($following);
    }

    /**
     * @return ArrayCollection
     */
    public function getInstaLikes()
    {
        return $this->instaLikes;
    }

    public function setInstaLike(Insta $instaLike)
    {
        $this->instaLikes[] = $instaLike;
        return $this;
    }

    public function removeInstaLike(Insta $instaLike)
    {
        $this->instaLikes->removeElement($instaLike);
        return $this;
    }

    public function isExistInstaLike(Insta $instaLike)
    {
        return $this->instaLikes->contains($instaLike);
    }

    /**
     * @return int
     */
    public function getFollowersCounter()
    {
        return $this->followersCounter;
    }

    public function incrementFollowersCounter()
    {
        $this->followersCounter++;
    }

    public function decrementFollowersCounter()
    {
        $this->followersCounter--;
    }

    /**
     * @return int
     */
    public function getFollowingCounter()
    {
        return $this->followingCounter;
    }

    public function incrementFollowingCounter()
    {
        $this->followingCounter++;
    }

    public function decrementFollowingCounter()
    {
        $this->followingCounter--;
    }

    /**
     * @return int
     */
    public function getInstaCounter()
    {
        return $this->instaCounter;
    }

    public function incrementInstaCounter()
    {
        $this->instaCounter++;
    }

    public function decrementInstaCounter()
    {
        $this->instaCounter--;
    }
}

