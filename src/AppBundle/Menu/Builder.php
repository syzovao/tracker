<?php
namespace AppBundle\Menu;

use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Matcher\Voter\RegexVoter;
use Knp\Menu\Matcher\Voter\RouteVoter;
use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    /**
     * Create main menu
     *
     * @param FactoryInterface $factory
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     */
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $authChecker = $this->container->get('security.authorization_checker');

        $menu = $factory->createItem('root');

        $menu->setChildrenAttribute('class', 'nav nav-pills');
        $menu->setChildrenAttribute('role', 'tablist');

        $menu->addChild($this->container->get('translator')->trans('menu.home'), array('route' => 'dashboard'));
        $menu->addChild('Users', array(
            'label' => $this->container->get('translator')->trans('menu.users'),
            'route' => 'oro_user_index',
            'extras' => array(
                'routes' => array(
                    array('route' => 'oro_user_create'),
                    array('route' => 'oro_user_view'),
                    array('route' => 'oro_user_update')
                )
            ),
            'linkAttributes' => array(
                'class' => 'dropdown-toggle',
                'data-toggle' => 'dropdown',
                'role' => 'link',
                'aria-expanded' => false
            )
        ))
            ->setAttribute('class', 'dropdown')
            ->setChildrenAttributes(array('class' => 'dropdown-menu', 'role' => 'menu'));

        if (!false === $authChecker->isGranted(array('ROLE_ADMIN', 'ROLE_MANAGER'))) {
            $menu['Users']->addChild('Users list', array(
                'label' => $this->container->get('translator')->trans('menu.users_list'),
                'route' => 'oro_user_index'
            ));
        }
        if (!false === $authChecker->isGranted('ROLE_ADMIN')) {
            $menu['Users']->addChild('User create', array(
                'label' => $this->container->get('translator')->trans('menu.users_create'),
                'route' => 'oro_user_create'
            ));
        }

        $currentUser = $this->container->get('security.token_storage')->getToken()->getUser();
        $menu['Users']->addChild('My profile', array(
            'label' => $this->container->get('translator')->trans('menu.users_profile'),
            'route' => 'oro_user_view',
            'routeParameters' => array('id' => $currentUser->getId())
        ));

        $menu->addChild('Projects', array(
            'label' => $this->container->get('translator')->trans('menu.projects'),
            'route' => 'oro_project',
            'extras' => array(
                'routes' => array(
                    array('route' => 'oro_project_create'),
                    array('route' => 'oro_project_view'),
                    array('route' => 'oro_project_update')
                )
            ),
            'linkAttributes' => array(
                'class' => 'dropdown-toggle',
                'data-toggle' => 'dropdown',
                'role' => 'link',
                'aria-expanded' => false
            )
        ))
            ->setAttribute('class', 'dropdown')
            ->setChildrenAttributes(array('class' => 'dropdown-menu', 'role' => 'menu'));
        $menu['Projects']->addChild('Projects list', array(
            'label' => $this->container->get('translator')->trans('menu.projects_list'),
            'route' => 'oro_project'
        ));

        if (!false === $authChecker->isGranted(array('ROLE_ADMIN', 'ROLE_MANAGER'))) {
            $menu['Projects']->addChild('Create Project', array(
                'label' => $this->container->get('translator')->trans('menu.projects_create'),
                'route' => 'oro_project_create'
            ));
        }

        $menu->addChild('Issues', array(
            'label' => $this->container->get('translator')->trans('menu.issues'),
            'route' => 'oro_issue',
            'extras' => array(
                'routes' => array(
                    array('route' => 'oro_issue_create'),
                    array('route' => 'oro_issue_view'),
                    array('route' => 'oro_issue_update')
                )
            ),
            'linkAttributes' => array(
                'class' => 'dropdown-toggle',
                'data-toggle' => 'dropdown',
                'role' => 'link',
                'aria-expanded' => false
            )
        ))
            ->setAttribute('class', 'dropdown')
            ->setChildrenAttributes(array('class' => 'dropdown-menu', 'role' => 'menu'));
        $menu['Issues']->addChild('Issues List', array(
            'label' => $this->container->get('translator')->trans('menu.issues_list'),
            'route' => 'oro_issue'
        ));
        $menu['Issues']->addChild('Create Issue', array(
            'label' => $this->container->get('translator')->trans('menu.issues_create'),
            'route' => 'oro_issue_create'
        ));

        $menu->addChild('Log out', array(
            'label' => $this->container->get('translator')->trans('menu.logout'),
            'route' => 'logout'
        ));

        return $menu;
    }

}
