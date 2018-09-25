<?php
/**
 * Admin Repository.
 */

namespace Repository;

use Doctrine\DBAL\Connection;
use Utils\Paginator;

class AdminRepository
{
    /**
     * Number of items per page.
     *
     * const int NUM_ITEMS
     */
    const NUM_ITEMS = 15;
    /**
     * Doctrine DBAL connection.
     *
     * @var \Doctrine\DBAL\Connection $db
     */
    protected $db;

    /**
     * AlbumRepository constructor.
     *
     * @param \Doctrine\DBAL\Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Show user data.
     *
     * @param $id
     * @return array
     */
    public function showUserData($id)
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('u.fname', 'u.lname', 'u.email', 'u.country')
            ->from('user_data', 'u')
            ->where('u.user_id = :id')
            ->setParameter(':id', $id, \PDO::PARAM_INT);

        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * Show all users.
     *
     * @return array
     */
    public function showAllUsers()
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('o.id', 'o.login', 'u.fname', 'u.lname', 'u.email', 'u.country')
            ->from('users', 'o')
            ->innerJoin('o', 'user_data', 'u', 'o.id=u.user_id');

        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * Delete user.
     *
     * @param $user_id
     */
    public function deleteUser($user_id)
    {
        try {

            $this->db->beginTransaction();

            $this->db->delete('users', ['id' => $user_id]);
            $this->db->delete('user_data', ['user_id' => $user_id]);

            $this->db->commit();

        } catch (DBALException $exception) {
            throw $exception;
        }
    }

    /**
     * Find for uniqueness.
     *
     * @param string          $name Element name
     * @param int|string|null $id   Element id
     *
     * @return array Result
     */
    public function findForUniqueness($name, $id = null)
    {
        $queryBuilder = $this->queryAll();
        $queryBuilder->where('t.name = :name')
            ->setParameter(':name', $name, \PDO::PARAM_STR);
        if ($id) {
            $queryBuilder->andWhere('t.id <> :id')
                ->setParameter(':id', $id, \PDO::PARAM_INT);
        }

        return $queryBuilder->execute()->fetchAll();
    }
}