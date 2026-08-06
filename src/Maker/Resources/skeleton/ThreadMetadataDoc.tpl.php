<?= "<?php\n" ?>
namespace <?= $namespace ?>;

<?= $use_statements ?>

#[ODM\EmbeddedDocument]
class <?= $class_name ?> extends BaseThreadMetadata
{
    #[ODM\ReferenceOne(targetDocument: <?= $participantEntClassName ?>::class)]
    protected ParticipantInterface $participant;
}