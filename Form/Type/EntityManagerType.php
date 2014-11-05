<?php
namespace Igdr\Bundle\ManagerBundle\Form\Type;

use Igdr\Bundle\ManagerBundle\Manager\AbstractManager;
use Igdr\Bundle\ManagerBundle\Model\ManagerFactoryInterface;
use Igdr\Bundle\ManagerBundle\Model\ManagerFactoryTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Entity manager type
 */
class EntityManagerType extends AbstractType implements ManagerFactoryInterface
{
    use ManagerFactoryTrait;

    /**
     * @param array $options
     *
     * @return AbstractManager
     */
    private function getManager($options)
    {
        if (is_object($options['manager'])) {
            $manager = $options['manager'];
        } else {
            $manager = $this->managerFactory->get($options['manager'])->order()->where();
        }

        return $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $choiceList = function (Options $options) {
            return $this->getManager($options)->findAll();
        };

        $getClass = function (Options $options) {
            return $this->getManager($options)->getRepositoryName();
        };

        $resolver->setDefaults(array(
            'manager' => '',
            'class'   => $getClass,
            'choices' => $choiceList
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'entity';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'entity_manager';
    }
}
