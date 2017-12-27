<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 * 
 * @ORM\Table(name="product")
 */
class Product 
{
    /**
     * @var int 
     * 
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="product_pkey")
     */
    protected $id;
    
    /**
     * @var string
     * 
     * @ORM\Column(type="string") 
     */
    protected $name;
    
    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=100) 
     */
    protected $slug;
    
    /**
     * @var string
     * 
     * @ORM\Column(type="text") 
     */
    protected $description;
    
    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * 
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;
    
    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * 
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;
    
    /**
     * @var Collection
     * 
     * @ORM\OneToMany(targetEntity="CategoryProduct", mappedBy="product")
     */
    protected $categoryProducts;
    
    /**
     * @var Collection 
     * 
     * @ORM\OneToMany(targetEntity="ProductPrice", mappedBy="product")
     */
    protected $productPrices;
    
    /**
     * @var Collection
     * 
     * @ORM\OneToMany(targetEntity="BasketProduct", mappedBy="product")
     */
    protected $basketProducts;
    
    /**
     * @var Collection
     * 
     * @ORM\OneToMany(targetEntity="FieldValue", mappedBy="product")
     */
    protected $fieldValues;
    
    public function __construct()
    {
        $this->categoryProducts = new ArrayCollection();
        $this->basketProducts = new ArrayCollection();
        $this->productPrices = new ArrayCollection();
        $this->fieldValues = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * @param string $slug
     *
     * @return self
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $description
     *
     * @return self
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param DateTime $createdAt
     *
     * @return self
     */
    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $updatedAt
     *
     * @return self
     */
    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param CategoryProduct $categoryProduct
     *
     * @return self
     */
    public function addCategoryProduct(CategoryProduct $categoryProduct): self
    {
        if (!$this->categoryProducts->contains($categoryProduct)) {
            $this->categoryProducts[] = $categoryProduct;
        }

        return $this;
    }

    /**
     * @param CategoryProduct $categoryProduct
     * 
     * @return self
     */
    public function removeCategoryProduct(CategoryProduct $categoryProduct): self
    {
        if ($this->categoryProducts->contains($categoryProduct)) {
            $this->categoryProducts->removeElement($categoryProduct);
        }
        
        return $this;
    }

    /**
     * @return Collection | CategoryProduct[]
     */
    public function getCategoryProducts(): Collection
    {
        return $this->categoryProducts;
    }

    /**
     * @param ProductPrice $productPrice
     *
     * @return self
     */
    public function addProductPrice(ProductPrice $productPrice): self
    {
        if (!$this->productPrices->contains($productPrice)) {
            $this->productPrices[] = $productPrice;
        }
        
        return $this;
    }

    /**
     * @param ProductPrice $productPrice
     * 
     * @return self
     */
    public function removeProductPrice(ProductPrice $productPrice): self
    {
        if ($this->productPrices->contains($productPrice)) {
            $this->productPrices->removeElement($productPrice);
        }
        
        return $this;
    }

    /**
     * @return Collection | ProductPrice[]
     */
    public function getProductPrices(): Collection
    {
        return $this->productPrices;
    }
    
    /**
     * @param BasketProduct $basketProduct
     *
     * @return self
     */
    public function addBasketProduct(BasketProduct $basketProduct): self
    {
        if (!$this->basketProducts->contains($basketProduct)) {
            $this->basketProducts[] = $basketProduct;
        }
        
        return $this;
    }

    /**
     * @param BasketProduct $basketProduct
     * 
     * @return self
     */
    public function removeBasketProduct(BasketProduct $basketProduct): self
    {
        if ($this->basketProducts->contains($basketProduct)) {
            $this->basketProducts->removeElement($basketProduct);
        }
        
        return $this;
    }

    /**
     * @return Collection | BasketProduct[]
     */
    public function getBasketProducts(): Collection
    {
        return $this->basketProducts;
    }

    /**
     * @param FieldValue $fieldValue
     *
     * @return self
     */
    public function addFieldValue(FieldValue $fieldValue): self
    {
        if (!$this->fieldValues->contains($fieldValue)) {
            $this->fieldValues[] = $fieldValue;
        }
        
        return $this;
    }

    /**
     * @param FieldValue $fieldValue
     * 
     * @return self
     */
    public function removeFieldValue(FieldValue $fieldValue): self
    {
        if ($this->fieldValues->contains($fieldValue)) {
            $this->fieldValues->removeElement($fieldValue);
        }
        
        return $this;
    }

    /**
     * @return Collection | FieldValue[]
     */
    public function getFieldValues(): Collection
    {
        return $this->fieldValues;
    }
}
