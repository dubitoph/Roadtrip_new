<?php

namespace App\Entity\rating;

use App\Entity\user\User;
use App\Entity\advert\Advert;
use App\Entity\booking\Booking;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\rating\ResponseToRating;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\rating\RatingRepository")
 */
class Rating
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * 
     * @Assert\Length(
     *      min = 10,
     *      max = 1000,
     *      minMessage = "The comment must contain at least {{ limit }} characters",
     *      maxMessage = "The comment cannot contain more than {{ limit }} characters"
     * )
     */
    private $comment;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @Assert\GreaterThan(
     *     value = 0
     * )
     * @Assert\LessThan(
     *     value = 6
     * )
     */
    private $score;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $ratingApproved;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\booking\Booking", inversedBy="ratings")
     */
    private $booking;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\rating\ResponseToRating", mappedBy="rating", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $responseToRating;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\advert\Advert", inversedBy="ratings")
     */
    private $advert;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\user\User", inversedBy="createdRatings")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\user\User", inversedBy="receivedRatings")
     */
    private $tenant;


    public function __construct() 
	{
        
        $this->createdAt = new \DateTime();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {

        return $this->createdAt;

    }

    public function getFormattedCreatedAt(): string
    {

        return $this->createdAt->format('d-m-Y');

    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getRatingApproved(): ?bool
    {
        return $this->ratingApproved;
    }

    public function setRatingApproved(?bool $ratingApproved): self
    {
        $this->ratingApproved = $ratingApproved;

        return $this;
    }

    public function getBooking(): ?Booking
    {
        return $this->booking;
    }

    public function setBooking(?Booking $booking): self
    {
        $this->booking = $booking;

        return $this;
    }

    public function getResponseToRating(): ?ResponseToRating
    {
        return $this->responseToRating;
    }

    public function setResponseToRating(?ResponseToRating $responseToRating): self
    {
        $this->responseToRating = $responseToRating;
/*
        // set the owning side of the relation if necessary
        if ($this !== $responseToRating->getRating()) {
            $responseToRating->setRating($this);
        }
*/
        return $this;
    }

    public function getAdvert(): ?Advert
    {
        return $this->advert;
    }

    public function setAdvert(?Advert $advert): self
    {
        $this->advert = $advert;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function geTenant(): ?User
    {
        return $this->tenant;
    }

    public function setTenant(?User $tenant): self
    {
        $this->tenant = $tenant;

        return $this;
    }
}
