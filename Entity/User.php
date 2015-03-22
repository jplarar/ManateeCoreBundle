<?php

namespace Manatee\CoreBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="Users", indexes={@ORM\Index(name="user_idx", columns={"username"})})
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", unique=TRUE)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $userId;

    /**
     * @ORM\Column(type="string", length=255, unique=TRUE)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $salt;

    /**
     * @ORM\Column(type="string", length=88)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $role;

    /**
     * @ORM\Column(type="string")
     */
    private $fullName;

    /**
     * @ORM\Column(type="string")
     */
    private $email;

    /**
     * @ORM\Column(type="integer")
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string")
     */
    private $country;

    /**
     * @ORM\Column(type="string")
     */
    private $city;

    /**
     * @ORM\Column(type="integer")
     */
    private $zipcode;

    /**
     * @ORM\Column(type="integer")
     */
    private $credits;

    /**
     * @ORM\Column(type="string")
     */
    private $skype;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $timestamp;

    #########################
    ## OBJECT RELATIONSHIP ##
    #########################

    /**
     * @ORM\OneToMany(targetEntity="PointLogs", mappedBy="userId")
     */
    protected $pointLogs;

    /**
     * @ORM\OneToMany(targetEntity="Listing", mappedBy="userId")
     */
    protected $listings;

    /**
     * @ORM\OneToMany(targetEntity="Review", mappedBy="userId")
     */
    protected $reviews;


    #########################
    ##      CONSTRUCTOR    ##
    #########################

    public function __construct()
    {
        $this->isActive = true;
        $this->salt = md5(uniqid(null, true));
        $this->pointLogs = new ArrayCollection();
        $this->listings = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    #########################
    ##   SPECIAL METHODS   ##
    #########################

    /**
     * Get formatted timestamp
     *
     * @return string
     */
    public function getFormattedTimestamp()
    {
        /** @var \DateTime $date */
        $date = $this->timestamp;

        return $date->format('c');
    }

    #########################
    # USER_INTERFACE METHODS
    # Implemented from abstract class UserInterface
    #########################

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return array($this->role);
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {

    }

    /**
     * @inheritDoc
     */
    public function isEqualTo(User $user)
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->salt !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    #########################
    # SERIALIZABLE METHODS
    #########################

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->userId,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->userId,
            ) = unserialize($serialized);
    }

    public function equals(UserInterface $user)
    {
        return $this->getUsername() === $user->getUsername();
    }


    #########################
    ## GETTERs AND SETTERs ##
    #########################

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set role
     *
     * @param string $role
     * @return User
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Get fullName
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set fullName
     * @param string $fullName
     * @return User
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Get Timestamp
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set TimestampValue
     * @ORM\PrePersist
     * @return User
     */
    public function setTimestampValue()
    {
        $this->timestamp = new \Datetime("now");
        return $this;
    }

    /**
     * Get phoneNumber
     * @return integer
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set phoneNumber
     * @param integer $phoneNumber
     * @return User
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return User
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return User
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Get zipcode
     * @return integer
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * Set zipcode
     * @param integer $zipcode
     * @return User
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;
        return $this;
    }

    /**
     * Get credits
     * @return integer
     */
    public function getCredits()
    {
        return $this->credits;
    }

    /**
     * Set credits
     * @param integer $credits
     * @return User
     */
    public function setCredit($credits)
    {
        $this->credits = $credits;
        return $this;
    }

    /**
     * Set skype
     *
     * @param string $skype
     * @return User
     */
    public function setSkype($skype)
    {
        $this->skype = $skype;

        return $this;
    }

    /**
     * Get skype
     *
     * @return string
     */
    public function getSkype()
    {
        return $this->skype;
    }

    #########################
    ##  OBJECT REL: G & S  ##
    #########################

    /**
     * Add pointLogs
     *
     * @param PointLog $pointLog
     * @return User
     */
    public function addPointLog(PointLog $pointLog)
    {
        $this->pointLogs[] = $pointLog;
        return $this;
    }

    /**
     * Remove pointLog
     *
     * @param PointLog $pointLog
     */
    public function removePointLog(PointLog $pointLog)
    {
        $this->pointLogs->removeElement($pointLog);
    }

    /**
     * Get pointLogs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPointLogs()
    {
        return $this->pointLogs;
    }

    /**
     * Add listing
     *
     * @param Listing $listing
     * @return User
     */
    public function addListing(Listing $listing)
    {
        $this->listings[] = $listing;
        return $this;
    }

    /**
     * Remove listing
     *
     * @param Listing $listing
     */
    public function removeListing(Listing $listing)
    {
        $this->listings->removeElement($listing);
    }

    /**
     * Get listings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getListings()
    {
        return $this->listings;
    }

    /**
     * Add review
     *
     * @param Review $review
     * @return User
     */
    public function addReview(Review $review)
    {
        $this->reviews[] = $review;
        return $this;
    }

    /**
     * Remove review
     *
     * @param Review $review
     */
    public function removeReview(Review $review)
    {
        $this->reviews->removeElement($review);
    }

    /**
     * Get reviews
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReviews()
    {
        return $this->reviews;
    }
}