<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping\AttributeOverrides;
use Doctrine\ORM\Mapping\AttributeOverride;

 /**
 *  @AttributeOverrides({
 *    @AttributeOverride(name="username",
 *        column=@ORM\Column(
 *            name     = "username",
 *            nullable = true,
 *        )
 *    ),
 *    @AttributeOverride(name="usernameCanonical",
 *        column=@ORM\Column(
 *            name     = "username_canonical",
 *            nullable = true,
 *        )
 *    ),
 *    @AttributeOverride(name="email",
 *        column=@ORM\Column(
 *            name     = "email",
 *            nullable = true,
 *        )
 *    ),
 *    @AttributeOverride(name="emailCanonical",
 *        column=@ORM\Column(
 *            name     = "email_canonical",
 *            nullable = true,
 *        )
 *    ),
 *    @AttributeOverride(name="password",
 *        column=@ORM\Column(
 *            name     = "password",
 *            nullable = true,
 *        )
 *    ),
 * })
 * 
 * @ORM\Entity
 * @ORM\Table(name="`user`")
 */
class User extends BaseUser
{
    /**
     * @var int 
     * 
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="user_pkey")
     */
    protected $id;
    
    /**
     * @var string
     * 
     * @ORM\Column(type="string") 
     */
    protected $phone;

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
     * @var Level
     * 
     * @ORM\ManyToOne(targetEntity="Level", inversedBy="users")
     * @ORM\JoinColumn(name="level_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $level;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
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
     * @param Level $level
     *
     * @return self
     */
    public function setLevel(Level $level): self
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return Level
     */
    public function getLevel(): Level
    {
        return $this->level;
    }
}
