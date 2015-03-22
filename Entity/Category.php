<?php

namespace Manatee\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="categories")
 */
class Category {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", unique=TRUE)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $categoryId;

    /**
     * @ORM\Column(type="string", length=80)
     */
    protected $name;

    #########################
    ## OBJECT RELATIONSHIP ##
    #########################

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="subcategories")
     * @ORM\JoinColumn(name="parent", referencedColumnName="categoryId", nullable=TRUE)
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     */
    protected $subcategories;

    /**
     * @ORM\OneToMany(targetEntity="Listing", mappedBy="listingId")
     */
    protected $listings;

    #########################
    ##      CONSTRUCTOR    ##
    #########################
    public function __construct()
    {
        $this->subcategories = new ArrayCollection();
        $this->listings = new ArrayCollection();
    }

    ##### GETTERS & SETTERS #####
    /**
     * Get CategoryId
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set Name
     * @param string $name
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * Add Subcategory
     * @param Category $category
     * @return Category
     */
    public function addSubcategory(Category $category)
    {
        $this->subcategories[] = $category;

        return $this;
    }

    /**
     * Remove Subcategory
     * @param Category Category
     */
    public function removeSubcategory(Category $category)
    {
        $this->subcategories->removeElement($category);
    }

    /**
     * Get Subcategories
     * @return ArrayCollection
     */
    public function getSubcategories()
    {
        return $this->subcategories;
    }

    /**
     * Add Listing
     * @param Listing $listing
     * @return Category
     */
    public function addListing(Listing $listing)
    {
        $this->listings[] = $listing;

        return $this;
    }

    /**
     * Remove Listing
     * @param Listing $listing
     */
    public function removeListing(Listing $listing)
    {
        $this->listings->removeElement($listing);
    }

    /**
     * Get Listings
     * @return ArrayCollection
     */
    public function getListings()
    {
        return $this->listings;
    }

    /**
     * Set Parent
     * @param Category $category
     * @return Category
     */
    public function setParent(Category $category)
    {
        $this->parent = $category;
        return $this;
    }

    /**
     * Get Parent
     * @return Category
     */
    public function getParent()
    {
        return $this->parent;
    }

    #########################
    ##   SPECIAL METHODS   ##
    #########################

    /**
     * Get human readable route (First Level)
     * @return string
     */
    public function getRoute()
    {
        if ($this->getParent())
        {
            return $this->getParent()->getName().' -> '.$this->name;
        }else{
            return '-';
        }
    }
}