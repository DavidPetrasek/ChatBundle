<?= "<?php\n" ?>
namespace <?= $namespace ?>;

<?= $use_statements ?>

#[ORM\Entity]
class <?= $class_name ?> extends BaseThreadMetadata
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options:["unsigned" => true])]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: FOSChatThread::class, inversedBy: 'metadata')]
    protected ThreadInterface $thread;

    #[ORM\ManyToOne(targetEntity: <?= $participantEntClassName ?>::class)]
    protected ParticipantInterface $participant;
}