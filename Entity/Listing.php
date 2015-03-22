<?php

namespace Manatee\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="Listings")
 */
class Listing {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", unique=TRUE)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $listingId;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="text")
     */
    protected $content;

    /**
     * @ORM\Column(type="string")
     */
    protected $area;

    /**
     * @ORM\Column(type="string")
     */
    protected $schedule;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $timestamp;

    #########################
    ## OBJECT RELATIONSHIP ##
    #########################

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="listings")
     * @ORM\JoinColumn(name="categoryId", referencedColumnName="categoryId", nullable=FALSE)
     */
    protected $categoryId;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="listings")
     * @ORM\JoinColumn(name="userId", referencedColumnName="userId", nullable=FALSE)
     */
    protected $userId;

    /**
     * @ORM\OneToMany(targetEntity="Review", mappedBy="listingId")
     */
    protected $reviews;

    #########################
    ##      CONSTRUCTOR    ##
    #########################

    public function __construct()
    {
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
    ## GETTERs AND SETTERs ##
    #########################

    /**
     * Get ListingId
     * @return integer
     */
    public function getListingId()
    {
        return $this->listingId;
    }

    /**
     * Get Area
     * @return string
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set Area
     * @param string $area
     * @return Listing
     */
    public function setArea($area)
    {
        $this->area = $area;
        return $this;
    }

    /**
     * Get Name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Name
     * @param string $name
     * @return Listing
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get Content
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set Content
     * @param string $content
     * @return Listing
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get Schedule
     * @return string
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * Set Schedule
     * @param string $schedule
     * @return Listing
     */
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;
        return $this;
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
     * @return Listing
     */
    public function setTimestampValue()
    {
        $this->timestamp = new \Datetime("now");
        return $this;
    }

    /**
     * Get Price
     * @return integer
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set Price
     * @param string $price
     * @return Listing
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Get CategoryId
     * @return \Niva\Wolf\CoreBundle\Entity\Segment
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set CategoryId
     * @param \Niva\Wolf\CoreBundle\Entity\Segment $categoryId
     * @return Listing
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    /**
     * Get UserId
     * @return \Niva\Wolf\CoreBundle\Entity\Advertiser
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set UserId
     * @param \Niva\Wolf\CoreBundle\Entity\Advertiser $userId
     * @return Listing
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * Add review
     *
     * @param Review $review
     * @return Listing
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