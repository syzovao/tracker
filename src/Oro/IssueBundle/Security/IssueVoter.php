<?php
namespace Oro\IssueBundle\Security;

use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\UserInterface;


class IssueVoter extends AbstractVoter
{
    const ACCESS = 'ACCESS';

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
        return array('Oro\IssueBundle\Entity\Issue');
    }

    /**
     * Return an array of supported attributes. This will be called by supportsAttribute
     *
     * @return array an array of supported attributes, i.e. array('CREATE', 'READ')
     */
    protected function getSupportedAttributes()
    {
        return array(
            self::ACCESS
        );
    }

    /**
     * Perform a single access check operation on a given attribute, object and (optionally) user
     * It is safe to assume that $attribute and $object's class pass supportsAttribute/supportsClass
     * $user can be one of the following:
     *   a UserInterface object (fully authenticated user)
     *   a string               (anonymously authenticated user)
     *
     * @param string               $attribute
     * @param object               $issue
     * @param UserInterface|string $user
     *
     * @return bool
     */
    protected function isGranted($attribute, $issue, $user = null)
    {
        if (!is_object($user)) {
            return false;
        }

        $authChecker = $this->container->get('security.authorization_checker');
        switch ($attribute) {
            case self::ACCESS:
                if ($authChecker->isGranted('ROLE_ADMIN') || $authChecker->isGranted('ROLE_MANAGER')) {
                    return true;
                }
                /** @var  \Oro\IssueBundle\Entity\Issue $issue */
                if ($authChecker->isGranted('ROLE_USER')
                    && $issue->getProject()->isProjectMember($user->getUsername())) {
                    return true;
                }
                return false;
                break;
        }

        return false;
    }
}
