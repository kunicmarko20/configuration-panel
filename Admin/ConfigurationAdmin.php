<?php

namespace KunicMarko\SonataConfigurationPanelBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ConfigurationAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('value');
    }
    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        // Don't you just hate that mosaic list mode?
        unset($this->listModes['mosaic']);

        $listMapper
            ->add('name')
            ->add('value', null, ['template' => 'ConfigurationPanelBundle:CRUD:list_field_value.html.twig'])
            ->add('createdAt', null, ['template' => 'ConfigurationPanelBundle:CRUD:list_field_created_at.html.twig'])
            ->add('_action', null, [
                'actions' => [
                    'edit' => [
                        'template' => 'ConfigurationPanelBundle:CRUD:list_action_edit.html.twig'
                    ],
                    'delete' => [
                        'template' => 'ConfigurationPanelBundle:CRUD:list_action_delete.html.twig'
                    ],
                ]
            ]);
    }

    public function configure()
    {
        $this->setTemplate('list', 'ConfigurationPanelBundle:CRUD:list.html.twig');
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $object = $this->getSubject();

        $formMapper
            ->with('Content')
                ->add('name', TextType::class);
        $object->generateFormField($formMapper);
    }
}
