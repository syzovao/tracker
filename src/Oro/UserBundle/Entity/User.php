<?php

namespace Oro\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="oro_users")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface, \Serializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=60, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="fullname", type="string", length=255, nullable=true)
     */
    private $fullname;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar_path", type="string", length=255, nullable=true)
     */
    private $avatar_path;

    /**
     * @var \Symfony\Component\HttpFoundation\File\UploadedFile
     * @Assert\File(maxSize="5M")
     */
    private $avatar_file;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    private $temp;

    /**
     * @ORM\Column(name="roles", type="string", length=255)
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="users")
     * @ORM\JoinColumn(name="role", referencedColumnName="roles")
     */
    private $roles;

    /**
     * @var string
     */
    private $salt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->salt = null;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set full name
     *
     * @param string $fullname
     * @return User
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get full name
     *
     * @return string
     */
    public function getFullname()
    {
        return $this->fullname;
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
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set avatar image path
     *
     * @param string $avatarPath
     * @return User
     */
    public function setAvatarPath($avatarPath)
    {
        $this->avatar_path = $avatarPath;

        return $this;
    }

    /**
     * Get avatar image path
     *
     * @return string
     */
    public function getAvatarPath()
    {
        return $this->avatar_path;
    }

    /**
     * Get avatar File
     *
     * @return UploadedFile
     */
    public function getAvatarFile()
    {
        return $this->avatar_file;
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
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set roles
     *
     * @param string $roles
     * @return $this
     */
    public function setRoles($roles)
    {
        if (is_object($roles)) {
            $this->roles = $roles->getRole();
        } elseif (is_array($roles)) {
            if (isset($roles['role'])) {
                $this->roles = $roles['role'];
            }
        } else {
            $this->roles = $roles;
        }
        return $this;
    }

    /**
     * Get roles
     *
     * @return string
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Serialize user
     *
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->salt,
        ));
    }

    /**
     * Unserialize user
     *
     * @param string $serialized
     * @return string
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->salt
            ) = unserialize($serialized);
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * Sets Set avatar File.
     *
     * @param UploadedFile $file
     */
    public function setAvatarFile(UploadedFile $file = null)
    {
        $this->avatar_file = $file;
        // check if we have an old image path
        if (is_file($this->getAbsolutePath())) {
            // store the old name to delete after the update
            $this->temp = $this->getAbsolutePath();
        } else {
            $this->avatar_path = 'initial';
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getAvatarFile()) {
            $filename = sha1(uniqid(mt_rand(), true));
            $this->avatar_path = $filename . '.' . $this->getAvatarFile()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getAvatarFile()) {
            return;
        }

        // check if we have an old image
        if (isset($this->temp)) {
            // delete the old image
            unlink($this->temp);
            // clear the temp image path
            $this->temp = null;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $this->getAvatarFile()->move(
            $this->getUploadRootDir(),
            $this->getAvatarFile()->getClientOriginalName()
        );

        // set the path property to the filename where you've saved the file
        $this->avatar_path = $this->getAvatarFile()->getClientOriginalName();

        $this->setAvatarFile(null);
    }

    /**
     * @ORM\PreRemove()
     */
    public function storeFilenameForRemove()
    {
        $this->temp = $this->getAbsolutePath();
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if (isset($this->temp)) {
            unlink($this->temp);
        }
    }

    public function getAbsolutePath()
    {
        return null === $this->avatar_path
            ? null
            : $this->getUploadRootDir() . '/' . $this->avatar_path;
    }

    public function getWebPath()
    {
        return null === $this->avatar_path
            ? null
            : $this->getUploadDir() . '/' . $this->avatar_path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/user/images';
    }

    public function getAvatarImagePath($size = 100)
    {
        if (!empty($this->getAvatarPath())) {
            $imagePath = $this->getUploadDir() . '/' . $this->avatar_path;
        } else {
            $imagePath = $this->getUploadDir() . '/default/placeholder' . $size . '.jpg';
        }
        return $imagePath;
    }
}
