<?php

namespace Manatee\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="PointLogs")
 */
class PointLog {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", unique=TRUE)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $pointLogId;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $timestamp;

    #########################
    ## OBJECT RELATIONSHIP ##
    #########################

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="pointLogs")
     * @ORM\JoinColumn(name="listingId", referencedColumnName="listingId", nullable=FALSE)
     */
    protected $listingId;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="pointLogs")
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
     * Get PointLogId
     * @return integer
     */
    public function getPointLogId()
    {
        return $this->pointLogId;
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
     * @return PointLog
     */
    public function setTimestampValue()
    {
        $this->timestamp = new \Datetime("now");
        return $this;
    }

    /**
     * Get Amount
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set Amount
     * @param string $amount
     * @return PointLog
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Get ListingId
     * @return \Niva\Wolf\CoreBundle\Entity\Segment
     */
    public function getListingId()
    {
        return $this->listingId;
    }

    /**
     * Set ListingId
     * @param \Niva\Wolf\CoreBundle\Entity\Segment $listingId
     * @return PointLog
     */
    public function setListingId($listingId)
    {
        $this->listingId = $listingId;
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
     * @return PointLog
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

}