<?php

namespace Oro\Bundle\SecurityBundle;

use Oro\Bundle\SecurityBundle\Acl\Domain\ObjectIdentityFactory;
use Oro\Bundle\SecurityBundle\Metadata\AclAnnotationProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SecurityFacade
{
    private AuthorizationCheckerInterface $authorizationChecker;
    protected AclAnnotationProvider $annotationProvider;
    protected ObjectIdentityFactory $objectIdentityFactory;
    private LoggerInterface $logger;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        AclAnnotationProvider $annotationProvider,
        ObjectIdentityFactory $objectIdentityFactory,
        LoggerInterface $logger
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->annotationProvider = $annotationProvider;
        $this->objectIdentityFactory = $objectIdentityFactory;
        $this->logger = $logger;
    }

    /**
     * Checks if an access to the given method of the given class is granted to the caller
     */
    public function isClassMethodGranted(string $class, ?string $method): bool
    {
        // check method level ACL
        $annotation = $this->annotationProvider->findAnnotation($class, $method);
        if ($annotation !== null) {
            $this->logger->debug(
                sprintf('Check an access using "%s" ACL annotation.', $annotation->getId())
            );

            return $this->authorizationChecker->isGranted(
                $annotation->getPermission(),
                $this->objectIdentityFactory->get($annotation)
            );
        }

        return true;
    }

    /**
     * Get permission for given class and method from the ACL annotation
     */
    public function getClassMethodAnnotationPermission(string $class, ?string $method): ?string
    {
        $annotation = $this->annotationProvider->findAnnotation($class, $method);

        return $annotation ? $annotation->getPermission() : null;
    }

    /**
     * Checks if an access to a resource is granted to the caller
     *
     * @param string|string[] $attributes Can be a role name(s), permission name(s), an ACL annotation id
     *                                    or something else, it depends on registered security voters
     * @param mixed           $object     A domain object, object identity or object identity descriptor (id:type)
     *
     * @return bool
     */
    public function isGranted($attributes, $object = null)
    {
        if ($object === null
            && is_string($attributes)
            && $annotation = $this->annotationProvider->findAnnotationById($attributes)
        ) {
            $this->logger->debug(sprintf('Check an access using "%s" ACL annotation.', $annotation->getId()));

            return $this->authorizationChecker->isGranted(
                $annotation->getPermission(),
                $this->objectIdentityFactory->get($annotation)
            );
        }

        $subject = is_string($object) ? $this->objectIdentityFactory->get($object) : $object;

        if (is_string($attributes)) {
            return $this->authorizationChecker->isGranted($attributes, $subject);
        }

        foreach ($attributes as $attribute) {
            if ($this->authorizationChecker->isGranted($attribute, $subject)) {
                return true;
            }
        }

        return false;
    }
}
