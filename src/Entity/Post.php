<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[UniqueEntity('sourceId')]

class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Ignore]
    private ?int $id = null;

    #[Groups(['posts'])]
    #[ORM\ManyToOne(targetEntity: PostUser::class, inversedBy: 'products')]
    #[ORM\JoinColumn(name: "post_user_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private ?PostUser $postUser;

    #[Ignore]
    #[ORM\Column(unique: true)]
    private ?int $sourceId = null;

    #[Groups(['posts'])]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[Groups(['posts'])]
    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $body = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPostUser(): ?PostUser
    {
        return $this->postUser;
    }

    public function setPostUser(PostUser $postUser): static
    {
        $this->postUser = $postUser;

        return $this;
    }

    public function getSourceId(): ?int
    {
        return $this->sourceId;
    }

    public function setSourceId(int $sourceId): static
    {
        $this->sourceId = $sourceId;

        return $this;
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

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): static
    {
        $this->body = $body;

        return $this;
    }
}
