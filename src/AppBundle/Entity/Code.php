<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="code")
 */
class Code 
{
    /**
     * @var int 
     * 
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="code_pkey")
     */
    private $id;
    
    /**
     * @var string
     * 
     * @ORM\Column(type="string", name="phone")
     */
    protected $phone;

    /**
     * @var int 
     * 
     * @ORM\Column(type="integer")
     */
    private $value;
    
    /**
     * @var DateTime 
     * 
     * @ORM\Column(type="datetime", name="created_at")
     */
    private $createdAt;
    
    /** @var bool
     * 
     * @ORM\Column(type="boolean", name="is_login", nullable=true)
     */
    protected $isLogin;
    
    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
    
    /**
     * @return string 
     */
    public function getPhone(): string
    {
        return $this->phone;
    }
    
    /**
     * @param string $phone
     * 
     * @return self
     */
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }
    
    /**
     * @param int $value
     * 
     * @return self
     */
    public function setValue(int $value): self
    {
        $this->value = $value;
        
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
     * @return bool
     */
    public function getIsLogin(): bool
    {
        return $this->isLogin;
    }
    
    /**
     * @param bool $isLogin
     * 
     * @return self
     */
    public function setIsLogin(bool $isLogin): self
    {
        $this->isLogin = $isLogin;
        
        return $this;
    }

}

