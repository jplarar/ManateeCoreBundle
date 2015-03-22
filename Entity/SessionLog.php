<?php

namespace Manatee\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="SessionLogs")
 */
class SessionLog
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", unique=TRUE)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $sessionLogId;

    /**
     * @ORM\Column(type="string", length=32)
     */
    protected $apiKey;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $userAgent;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $timestamp;


    #########################
    ## OBJECT RELATIONSHIP ##
    #########################

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="sessionLogs")
     * @ORM\JoinColumn(name="userId", referencedColumnName="userId", nullable=FALSE)
     */
    protected $userId;


    #########################
    ##      CONSTRUCTOR    ##
    #########################

    public function __construct()
    {
        // empty.
    }


    #########################
    ## GETTERs AND SETTERs ##
    #########################

    /**
     * Get SessionLogId
     * @return integer
     */
    public function getSessionLogId()
    {
        return $this->sessionLogId;
    }

    /**
     * Get ApiKey
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Set ApiKey
     * @param string $apiKey
     * @return SessionLog
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * Get UserAgent
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Set UserAgent
     * @param string $userAgent
     * @return SessionLog
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
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
     * Set Timestamp
     * @param \DateTime $timestamp
     * @return SessionLog
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * Set TimestampValue
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @return SessionLog
     */
    public function setTimestampValue()
    {
        $this->timestamp = new \Datetime("now");
        return $this;
    }


    #########################
    ##  OBJECT REL: G & S  ##
    #########################

    /**
     * Get UserId
     * @return User
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set UserId
     * @param User $userId
     * @return SessionLog
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }
}