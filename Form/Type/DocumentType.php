<?php

declare(strict_types=1);

namespace Doctrine\Bundle\MongoDBBundle\Form\Type;

use Doctrine\Bundle\MongoDBBundle\Form\ChoiceList\MongoDBQueryBuilderLoader;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Persistence\ObjectManager;
use InvalidArgumentException;
use ReturnTypeWillChange;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface;
use Symfony\Bridge\Doctrine\Form\Type\DoctrineType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function interface_exists;

/**
 * Form type for a MongoDB document
 */
class DocumentType extends DoctrineType
{
    public function getLoader(ObjectManager $manager, object $queryBuilder, string $class): EntityLoaderInterface
    {
        return new MongoDBQueryBuilderLoader(
            $queryBuilder,
            $manager,
            $class,
        );
    }

    /** @return void */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(['document_manager' => null]);

        $registry   = $this->registry;
        $normalizer = static function (Options $options, $manager) use ($registry) {
            if (isset($options['document_manager']) && $manager) {
                throw new InvalidArgumentException('You cannot set both an "em" and "document_manager" option.');
            }

            $manager = $options['document_manager'] ?: $manager;

            if ($manager === null) {
                return $registry->getManagerForClass($options['class']);
            }

            if ($manager instanceof ObjectManager) {
                return $manager;
            }

            return $registry->getManager($manager);
        };

        $resolver->setNormalizer('em', $normalizer);

        $resolver->setAllowedTypes('document_manager', ['null', 'string', DocumentManager::class]);
    }

    /**
     * @internal Symfony 2.8 compatibility
     *
     * @return string
     */
    #[ReturnTypeWillChange]
    public function getBlockPrefix()
    {
        return 'document';
    }
}

interface_exists(ObjectManager::class);
