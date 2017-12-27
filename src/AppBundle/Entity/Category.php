<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 * 
 * @ORM\Table(name="category")
 */
class Category 
{
    /**
     * @var int 
     * 
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="category_pkey")
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
     * @var bool
     * 
     * @ORM\Column(type="boolean", name="is_root")
     */
    protected $isRoot;
    
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
     * @ORM\OneToMany(targetEntity="CategoryRelation", mappedBy="child")
     */
    protected $childrenRelations;

    /**
     * @var Collection
     * 
     * @ORM\OneToMany(targetEntity="CategoryRelation", mappedBy="parent")
     */
    protected $parentsRelations;
    
    /**
     * @var Collection
     * 
     * @ORM\OneToMany(targetEntity="CategoryProduct", mappedBy="category")
     */
    protected $categoryProducts;
    
    /**
     * @var Collection
     * 
     * @ORM\OneToMany(targetEntity="FieldType", mappedBy="category")
     */
    protected $fieldTypes;
    
    public function __construct()
    {
        $this->categoryProducts = new ArrayCollection();
        $this->fieldTypes = new ArrayCollection();
        $this->childrenRelations = new ArrayCollection();
        $this->parentsRelations = new ArrayCollection();
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
     * @param bool $isRoot
     *
     * @return self
     */
    public function setIsRoot(bool $isRoot): self
    {
        $this->isRoot = $isRoot;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsRoot(): bool
    {
        return $this->isRoot;
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
     * @param CategoryRelation $childRelation
     *
     * @return self
     */
    public function addChildRelation(CategoryRelation $childRelation): self
    {
        if (!$this->childrenRelations->contains($childRelation)) {
            $this->childrenRelations[] = $childRelation;
        }
        
        return $this;
    }

    /**
     * @param CategoryRelation $childRelation
     * 
     * @return self
     */
    public function removeChildRelation(CategoryRelation $childRelation): self
    {
        if ($this->childrenRelations->contains($childRelation)) {
            $this->childrenRelations->removeElement($childRelation);
        }
        
        return $this;
    }

    /**
     * @return Collection  | CategoryRelation[]
     */
    public function getChildrenRelations(): Collection
    {
        return $this->childrenRelations;
    }

    /**
     * @param CategoryRelation $parentRelation
     *
     * @return self
     */
    public function addParentRelation(CategoryRelation $parentRelation): self
    {
        if (!$this->parentsRelations->contains($parentRelation)) {
            $this->parentsRelations[] = $parentRelation;
        }

        return $this;
    }

    /**
     * @param CategoryRelation $parentRelation
     * 
     * @return self
     */
    public function removeParentRelation(CategoryRelation $parentRelation): self
    {
        if ($this->parentsRelations->contains($parentRelation)) {
            $this->parentsRelations->removeElement($parentRelation);
        }
        
        return $this;
    }

    /**
     * @return Collection | CategoryRelation[]
     */
    public function getParentsRelations(): Collection
    {
        return $this->parentsRelations;
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
     * @param FieldType $fieldType
     *
     * @return self
     */
    public function addFieldType(FieldType $fieldType): self
    {
        if (!$this->fieldTypes->contains($fieldType)) {
            $this->fieldTypes[] = $fieldType;
        }
        
        return $this;
    }

    /**
     * @param FieldType $fieldType
     * 
     * @return self
     */
    public function removeFieldType(FieldType $fieldType): self
    {
        if ($this->fieldTypes->contains($fieldType)) {
            $this->fieldTypes->removeElement($fieldType);
        }
        
        return $this;
    }

    /**
     * @return Collection | FieldType[]
     */
    public function getFieldTypes(): Collection
    {
        return $this->fieldTypes;
    }
}
