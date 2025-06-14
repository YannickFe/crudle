<?= "<?php\n" ?>

namespace <?= $namespace ?>;

<?= $use_statements ?>

#[ORM\Entity(repositoryClass: <?= $repository_class_name ?>::class)]
<?php if ($api_resource): ?>
#[ApiResource]
<?php endif ?>
class <?= $class_name . "\n" ?>
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

<?= $additional_fields ?? '' ?>
}
