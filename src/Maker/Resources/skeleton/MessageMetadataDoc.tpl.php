<?= "<?php declare(strict_types=1);\n" ?>
namespace <?= $namespace ?>;

<?= $use_statements ?>

#[ODM\EmbeddedDocument]
class <?= $class_name ?> extends BaseMessageMetadata
{
    #[ODM\ReferenceOne(targetDocument: <?= $participantEntClassName ?>::class)]
    protected ParticipantInterface $participant;
}