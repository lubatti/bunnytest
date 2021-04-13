<?php
namespace App\Infrastructure\Persistence\Doctrine\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use InvalidArgumentException;

class BaseRepository extends EntityRepository
{
    protected const DEFAULT_PAGE = 1;
    protected const DEFAULT_LIMIT = 9999;

    public function em()
    {
        return $this->_em;
    }

    public function save($entity, bool $flush = true)
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $entity;
    }

    /**
     * Finds an entity by its primary key.
     * If not found, throws an exception
     *
     * @param mixed $pk Primary key
     * @param string $customMessage Custom message
     *
     * @throws InvalidArgumentException
     *
     * @return mixed
     */
    public function findOrFail($pk, $customMessage = '')
    {
        if (is_null($pk)) {
            $this->throwExceptionIfNotFound($pk, $customMessage);
        }

        $result = $this->find($pk);

        if (!$result) {
            $this->throwExceptionIfNotFound($pk, $customMessage);
        }

        return $result;
    }

    private function throwExceptionIfNotFound($pk, string $customMessage = '')
    {
        $message = !empty($customMessage) ?
            $customMessage :
            $this->_entityName . ' with id ' . strval($pk) . ' could not be found';

        throw new InvalidArgumentException($message);
    }

    protected function paginate(QueryBuilder $dql, int $page = self::DEFAULT_PAGE, int $limit = self::DEFAULT_LIMIT)
    {
        $paginator = new Paginator($dql);

        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1)) // Offset
            ->setMaxResults($limit); // Limit

        return $paginator;
    }
}
