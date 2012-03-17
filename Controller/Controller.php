<?php

namespace Knp\Bundle\RadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller.
 *
 * Provides missing methods for the base controller.
 */
class Controller extends BaseController
{
    /**
     * Shortcut to return Validator service.
     *
     * @return Validator
     */
    public function getValidator()
    {
        return $this->get('validator');
    }

    /**
     * Shortcut to validator validate method.
     *
     * @param object     $object The object to validate
     * @param array|null $groups The validator groups to use for validating
     *
     * @return ConstraintViolationList
     */
    public function validate($object, $groups = null)
    {
        return $this->getValidator()->validate($object, $groups);
    }

    /**
     * Shortcut to return Session instance.
     *
     * @return Session
     */
    public function getSession()
    {
        return $this->getRequest()->getSession();
    }

    /**
     * Shortcut to return the Security Context service.
     *
     * @return SecurityContext
     *
     * @throws \LogicException If SecurityBundle is not available
     */
    public function getSecurityContext()
    {
        if (!$this->container->has('security.context')) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        return $this->container->get('security.context');
    }

    /**
     * Shortcut to check current user rights with Security Context.
     *
     * @param array $attributes
     * @param mixed $object
     *
     * @return Boolean
     */
    public function isGranted($attributes, $object = null)
    {
        return $this->getSecurityContext()->isGranted($attributes, $object);
    }

    /**
     * Returns an AccessDeniedException.
     *
     * This will result in a 403 response code. Usage example:
     *
     *     throw $this->createAccessDeniedException('You have no rights');
     *
     * @return AccessDeniedException
     */
    public function createAccessDeniedException($message = 'Access Denied', \Exception $previous = null)
    {
        return new AccessDeniedException($message, $previous);
    }

    /**
     * Ensures the user is granted or throws an AccessDeniedException
     *
     * @example
     *
     *      $this->isGrantedOr403('EDIT', $article);
     *
     * @param  array  $attributes
     * @param  mixed  $object
     * @param  string $message
     *
     * @throws AccessDeniedException
     */
    public function isGrantedOr403($attributes, $object = null, $message = 'Access Denied')
    {
        if (!$this->isGranted($attributes, $object)) {
            throw $this->createAccessDeniedException($message);
        }
    }

    /**
     * Shortcut to return Doctrine EntityManager service.
     *
     * @param string $name The entity manager name (null for the default one)
     *
     * @return EntityManager
     */
    public function getEntityManager($name = null)
    {
        return $this->getDoctrine()->getEntityManager($name);
    }

    /**
     * Shortcut to return Doctrine Entity Repository.
     *
     * @param string $repositoryName The repository name
     * @param string $managerName    The entity manager name (null for default one)
     *
     * @return EntityRepository
     */
    public function getEntityRepository($repositoryName, $managerName = null)
    {
        return $this->getEntityManager($managerName)->getRepository($repositoryName);
    }

    /**
     * Shortcut to return Doctrine ManagerRegistry service.
     *
     * @param string $name The document manager name (null for the default one)
     *
     * @return ManagerRegistry
     */
    public function getDocumentManager($name = null)
    {
        return $this->getDoctrine()->getManager($name);
    }

    /**
     * Shortcut to return Doctrine Document Repository.
     *
     * @param string $repositoryName The repository name
     * @param string $managerName    The document manager name (null for default one)
     *
     * @return DocumentRepository
     */
    public function getDocumentRepository($repositoryName, $managerName = null)
    {
        return $this->getDocumentManager($managerName)->getRepository($repositoryName);
    }

    /**
     * Renders a hash into JSON.
     *
     * @param array    $hash     The hash
     * @param Response $response A response instance
     *
     * @return Response A Response instance
     */
    public function renderJson(array $hash, Response $response = null)
    {
        if (null === $response) {
            $response = new Response();
        }

        $response->setContent(json_encode($hash));
        $response->headers->set('Content-type', 'application/json');

        return $response;
    }

    /**
     * Shortcut to find an entity by its identifier
     *
     * @param  string $class       The entity class name
     * @param  mixed  $id          The entity identifier
     * @param  string $managerName The document manager to use
     *
     * @return object or NULL if the entity was not found
     */
    public function findEntity($class, $id, $managerName = null)
    {
        return $this->getEntityRepository($class, $managerName)->find($id);
    }

    /**
     * Shortcut to find an entity by criteria
     *
     * @param  string $class       The entity class name
     * @param  array  $criteria    An array of criteria (field => value)
     * @param  string $managerName The entity manager to use
     *
     * @return object or NULL if the entity was not found
     */
    public function findEntityBy($class, array $criteria, $managerName = null)
    {
        return $this->getEntityRepository($class, $managerName)->findOneBy($criteria);
    }

    /**
     * Finds the entity matching the specified id or throws a NotFoundHttpException
     *
     * @param  string $class       The entity class name
     * @param  mixed  $id          The entity identifier
     * @param  string $managerName The entity manager to use
     *
     * @return object The found entity
     *
     * @throws NotFoundHttpException if the entity was not found
     */
    public function findEntityOr404($class, $id, $managerName = null)
    {
        $entity = $this->findEntity($class, $id, $managerName);

        if (null === $entity) {
            throw $this->createNotFoundException(sprintf(
                'The %s entity with id "%s" was not found.',
                $class,
                is_array($id) ? implode(' ', $id) : $id
            ));
        }

        return $entity;
    }

    /**
     * Finds the entity matching the specified criteria or throws a NotFoundHttpException
     *
     * @param  string $class       The entity class name
     * @param  mixed  $id          The entity identifier
     * @param  string $class       The entity class name
     *
     * @return object The found entity
     *
     * @throws NotFoundHttpException if the entity was not found
     */
    public function findEntityByOr404($class, array $criteria, $managerName = null)
    {
        $entity = $this->findEntityBy($class, $criteria, $managerName);

        if (null === $entity) {
            throw $this->createNotFoundException(sprintf(
                'The %s entity with %s was not found.',
                $class,
                implode(' and ', array_map(
                    function ($k, $v) { sprintf('%s "%s"', $k, $v); },
                    array_flip($criteria),
                    $criteria
                ))
            ));
        }

        return $entity;
    }

    /**
     * Shortcut to find a document by its identifier
     *
     * @param  string $class       The document class name
     * @param  string $id          The document class name
     * @param  string $managerName The document manager to use
     *
     * @return object or NULL if the document was not found
     */
    public function findDocument($class, $id, $managerName = null)
    {
        return $this->getDocumentRepository($class, $managerName)->find($id);
    }

    /**
     * Shortcut to find a document by criteria
     *
     * @param  string $class       The document class name
     * @param  array  $criteria    An array of criteria (field => value)
     * @param  string $managerName The document manager to use
     *
     * @return object or NULL if the document was not found
     */
    public function findDocumentBy($class, array $criteria, $managerName = null)
    {
        return $this->getDocumentRepository($class, $managerName)->findOneBy($criteria);
    }

    /**
     * Finds the document matching the specified id or throws a NotFoundHttpException
     *
     * @param  string $class       The document class name
     * @param  string $id          The document class name
     * @param  string $managerName The document manager to use
     *
     * @return object The found document
     *
     * @throws NotFoundHttpException if the document was not found
     */
    public function findDocumentOr404($class, $id, $managerName = null)
    {
        $document = $this->findDocument($class, $id, $managerName);

        if (null === $document) {
            throw $this->createNotFoundException(sprintf(
                'The %s document with id "%s" was not found.',
                $class,
                $id
            ));
        }

        return $document;
    }

    /**
     * Finds the document matching the specified criteria or throws a NotFoundHttpException
     *
     * @param  string $class       The document class name
     * @param  array  $criteria    An array of criteria (field => value)
     * @param  string $managerName The document manager to use
     *
     * @return object The found document
     *
     * @throws NotFoundHttpException if the document was not found
     */
    public function findDocumentByOr404($class, array $criteria, $managerName = null)
    {
        $document = $this->getDocumentRepository($class, $managerName)->findOneBy($criteria);

        if (null === $document) {
            throw $this->createNotFoundException(sprintf(
                'The %s document with %s was not found.',
                $class,
                implode(' and ', array_map(
                    function ($k, $v) { sprintf('%s "%s"', $k, $v); },
                    array_flip($criteria),
                    $criteria
                ))
            ));
        }

        return $document;
    }

    /**
     * Adds some dynamic shortcuts
     *
     * @example
     *
     *  // find a post by id
     *  $entity = $this->findPostEntity(123);
     *  $document = $this->findPostDocument('theMongoId');
     *
     *  // find a post by slug
     *  $entity = $this->findPostEntityBySlug('the-slug');
     *  $document = $this->findPostDocumentBySlug('the-slug');
     *
     * @method find{Class}Entity($id, $managerName = null)
     * @method find{Class}EntityOr404($id, $managerName = null)
     *
     * @method find{Class}EntityBy{Property}($value, $managerName = null)
     * @method find{Class}EntityBy{Property}Or404($value, $managerName = null)
     *
     * @method find{Class}Document($id, $managerName = null)
     * @method find{Class}DocumentOr404($id, $managerName = null)
     *
     * @method get{Class}EntityRepository($managerName = null)
     *
     * @method get{Class}DocumentRepository($managerName = null)
     */
    public function __call($method, array $arguments)
    {
        if (preg_match('/^find(\w+)(Entity|Document)(By(\w+?))?(Or404)?$/', $method, $matches)) {
            if (0 === count($arguments)) {
                throw new \BadMethodCallException(
                    'You must pass an id as first argument.'
                );
            }

            $class   = sprintf('App:%s', $matches[1]);
            $manager = empty($arguments[1]) ? null : $arguments[1];
            $finder  = sprintf(
                'find%s%s%s',
                $matches[2],
                empty($matches[3]) ? '' : 'By',
                empty($matches[5]) ? '' : 'Or404'
            );

            if (empty($matches[4])) {
                $value = $arguments[0];
            } else {
                $value = array(lcfirst($matches[4]) => $arguments[0]);
            }

            return $this->$finder($class, $value, $manager);
        } elseif (preg_match('/^get(\w+)(Entity|Document)Repository$/', $method, $matches)) {
            $class   = sprintf('App:%s', $matches[1]);
            $manager = empty($arguments[1]) ? null : $arguments[1];
            $getter  = sprintf('get%sRepository', $matches[2]);

            return $this->$getter($class, $manager);
        }

        throw new \BadMethodCallException(sprintf(
            'The method %s does not exist.',
            $method
        ));
    }
}
