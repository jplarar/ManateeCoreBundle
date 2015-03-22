<?php

namespace Manatee\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="Reviews")
 */
class Review {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", unique=TRUE)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $reviewId;

    /**
     * @ORM\Column(type="text")
     */
    protected $content;

    /**
     * 1 = *
     * 2 = **
     * 3 = ***
     * 4 = ****
     * 5 = *****
     *
     * @ORM\Column(type="integer")
     */
    private $rating;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $timestamp;

    #########################
    ## OBJECT RELATIONSHIP ##
    #########################

    /**
     * @ORM\ManyToOne(targetEntity="Listing", inversedBy="reviews")
     * @ORM\JoinColumn(name="listingId", referencedColumnName="listingId", nullable=FALSE)
     */
    protected $listingId;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="reviews")
     * @ORM\JoinColumn(name="userId", referencedColumnName="userId", nullable=FALSE)
     */
    protected $userId;

    #########################
    ##      CONSTRUCTOR    ##
    #########################

    public function __construct()
    {

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
     * Get ReviewId
     * @return integer
     */
    public function getReviewId()
    {
        return $this->reviewId;
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
     * @return Review
     */
    public function setContent($content)
    {
        $this->content = $content;
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
     * @return Review
     */
    public function setTimestampValue()
    {
        $this->timestamp = new \Datetime("now");
        return $this;
    }

    /**
     * Get Rating
     * @return integer
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set Rating
     * @param string $rating
     * @return Review
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
        return $this;
    }

    /**
     * Get ListingId
     * @return \Manatee\CoreBundle\Entity\Listing
     */
    public function getListingId()
    {
        return $this->listingId;
    }

    /**
     * Set ListingId
     * @param \Manatee\CoreBundle\Entity\Listing $listingId
     * @return Review
     */
    public function setListingId($listingId)
    {
        $this->listingId = $listingId;
        return $this;
    }

    /**
     * Get UserId
     * @return \Manatee\CoreBundle\Entity\User
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set UserId
     * @param \Manatee\CoreBundle\Entity\User $userId
     * @return Review
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

}