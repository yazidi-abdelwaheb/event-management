<?php

namespace App\Entity;

use App\Enum\EventStatus;
use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\HasLifecycleCallbacks]

class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTime $start_date_time = null;

    #[ORM\Column(length: 255)]
    private ?string $location = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\Column]
    private ?int $capacity = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $organizer = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $end_date_time = null;

    /**
     * @var Collection<int, EventSubscribe>
     */
    #[ORM\OneToMany(targetEntity: EventSubscribe::class, mappedBy: 'event', orphanRemoval: true)]
    private Collection $eventSubscribes;

    #[ORM\Column(enumType: EventStatus::class)]
    private EventStatus $status = EventStatus::UPCOMING;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'Event')]
    private Collection $comments;

    

    public function __construct()
    {
        $this->eventSubscribes = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStartDateTime(): ?\DateTime
    {
        return $this->start_date_time;
    }

    public function getStart_date_time(): ?\DateTime
    {
        return $this->start_date_time;
    }

    public function setStartDateTime(\DateTime $start_date_time): static
    {
        $this->start_date_time = $start_date_time;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

     public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        if ($this->created_at === null) {
            $this->created_at = new \DateTimeImmutable();
        }
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    #[ORM\PrePersist]
    public function setUpdatedAtValue(): void
    {
        if ($this->updated_at === null) {
            $this->updated_at = new \DateTimeImmutable();
        }
    }

    public function getOrganizer(): ?User
    {
        return $this->organizer;
    }

    public function setOrganizer(?User $organizer): static
    {
        $this->organizer = $organizer;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getEndDateTime(): ?\DateTimeImmutable
    {
        return $this->end_date_time;
    }
    public function getEnd_date_time(): ?\DateTimeImmutable
    {
        return $this->end_date_time;
    }

    public function setEndDateTime(?\DateTimeImmutable $end_date_time): static
    {
        $this->end_date_time = $end_date_time;

        return $this;
    }

    /**
     * @return Collection<int, EventSubscribe>
     */
    public function getEventSubscribes(): Collection
    {
        return $this->eventSubscribes;
    }

    public function addEventSubscribe(EventSubscribe $eventSubscribe): static
    {
        if (!$this->eventSubscribes->contains($eventSubscribe)) {
            $this->eventSubscribes->add($eventSubscribe);
            $eventSubscribe->setEvent($this);
        }

        return $this;
    }

    public function removeEventSubscribe(EventSubscribe $eventSubscribe): static
    {
        if ($this->eventSubscribes->removeElement($eventSubscribe)) {
            // set the owning side to null (unless already changed)
            if ($eventSubscribe->getEvent() === $this) {
                $eventSubscribe->setEvent(null);
            }
        }

        return $this;
    }

    public function getSubscribedCount(): ?int
    {
        return $this->eventSubscribes->count();
    }

    public function __tostring(): string
    {
        return $this->title;
    }


    // Date validation logic
    #[Assert\Callback]
    public function validateDates(ExecutionContextInterface $context): void
    {
        $now = new \DateTime();

        if ($this->start_date_time !== null && $this->start_date_time < $now) {
            $context->buildViolation('The start date must be in the future.')
                ->atPath('start_date_time')
                ->addViolation();
        }

        
        if (
            $this->start_date_time !== null &&
            $this->end_date_time   !== null &&
            $this->end_date_time <= $this->start_date_time
        ) {
            $context->buildViolation('The end date must be after the start date.')
                ->atPath('end_date_time')
                ->addViolation();
        }

        
        if ($this->end_date_time !== null && $this->end_date_time < $now) {
            $context->buildViolation('The end date must be in the future.')
                ->atPath('end_date_time')
                ->addViolation();
        }
    }

    public function getStatus(): ?EventStatus
    {
        return $this->status;
    }

    public function setStatus(EventStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setEvent($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getEvent() === $this) {
                $comment->setEvent(null);
            }
        }

        return $this;
    }
}
