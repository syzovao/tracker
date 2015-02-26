<?php
namespace Oro\ProjectBundle\Security;

use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Oro\ProjectBundle\Entity\Project;


class ProjectVoter extends AbstractVoter
{
    const MODIFY = 'MODIFY';
    const VIEW = 'VIEW';
    const VIEW_LIST = 'VIEW_LIST';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Return an array of supported classes. This will be called by supportsClass
     *
     * @return array an array of supported classes, i.e. array('Acme\DemoBundle\Model\Product')
     */
    protected function getSupportedClasses()
    {
        return array('Oro\ProjectBundle\Entity\Project');
    }

    /**
     * Return an array of supported attributes. This will be called by supportsAttribute
     *
     * @return array an array of supported attributes, i.e. array('CREATE', 'READ')
     */
    protected function getSupportedAttributes()
    {
        return array(
            self::MODIFY,
            self::VIEW,
            self::VIEW_LIST
        );
    }

    /**
     * Perform a single access check operation on a given attribute, object and (optionally) user
     * It is safe to assume that $attribute and $object's class pass supportsAttribute/supportsClass
     * $user can be one of the following:
     *   a UserInterface object (fully authenticated user)
     *   a string               (anonymously authenticated user)
     *
     * @param string $attribute
     * @param \Oro\ProjectBundle\Entity\Project|string $project
     * @param UserInterface|string $user
     *
     * @return bool
     */
    protected function isGranted($attribute, $project, $user = null)
    {
        if (!is_object($user)) {
            return false;
        }

        if (is_string($project)) {
            $project = new $project;
        }

        $authChecker = $this->container->get('security.authorization_checker');
        switch ($attribute) {
            case self::VIEW_LIST:
            case self::MODIFY:
                if ($authChecker->isGranted('ROLE_ADMIN') || $authChecker->isGranted('ROLE_MANAGER')) {
                    return true;
                }
                break;
            case self::VIEW:
                if ($authChecker->isGranted('ROLE_ADMIN') || $authChecker->isGranted('ROLE_MANAGER')) {
                    return true;
                }
                /** @var  \Oro\ProjectBundle\Entity\Project $project */
                if ($authChecker->isGranted('ROLE_USER') && $project->isProjectMember($user)) {
                    return true;
                }
                return false;
                break;
        }

        return false;
    }
}
