<?php

namespace App\Entity\UserManagement;

use App\Repository\UserManagement\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @Vich\Uploadable
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Email(
     *      message = "Email invalid",
     * )
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @ORM\Column(type="string")
     */
    private $firstName;

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @ORM\Column(type="string")
     */
    private $lastName;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var string The plain password
     * @ORM\Column(type="string", nullable=true)
     */
    private $plainPassword;

    /**
     * @var string The API token
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private $apiToken;

    /**
     * @Vich\UploadableField(mapping="user_avatar", fileNameProperty="imageName")
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $imageName;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    public function __contruct(){
        $this->active = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getPlainPassword(){
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword){
        $this->plainPassword = $plainPassword;
    }

    public function getApiToken(){
        return $this->apiToken;
    }

    public function setApiToken(string $apiToken){
        $this->apiToken = $apiToken;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName){
        $this->firstName = $firstName;
    }

    public function getLastName(){
        return $this->lastName;
    }

    public function setLastName(string $lastName){
        $this->lastName = $lastName;
    }

    public function getImageFile(){
        return $this->imageFile;
    }

    public function setImageFile($imageFile){
        $this->imageFile = $imageFile;

        if(null !== $imageFile){
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageName(){
        return $this->imageName;
    }

    public function setImageName($imageName){
        $this->imageName = $imageName;
    }

    public function getAddress(){
        return $this->address;
    }

    public function setAddress($address){
        $this->address = $address;
    }

    public function getCity(){
        return $this->city;
    }

    public function setCity($city){
        $this->city = $city;
    }

    public function isActive(){
        return $this->active;
    }

    public function setActive($active){
        $this->active = $active;
    }
}
