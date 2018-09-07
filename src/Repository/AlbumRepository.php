<?php
/**
 * Album Repository.
 */

namespace Repository;

use Doctrine\DBAL\Connection;
use Utils\Paginator;

class AlbumRepository
{
    /**
     * Number of items per page.
     *
     * const int NUM_ITEMS
     */
    const NUM_ITEMS = 30;
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
     * Fetch all records.
     *
     * @return array Result
     */
    public function findAll($table)
    {
        $queryBuilder = $this->queryAll($table);

        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * Query all records.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder Result
     */
    protected function queryAll()
    {
        $queryBuilder = $this->db->createQueryBuilder();

        return $queryBuilder->select( 's.artist', 's.album', 's.year')
            ->from('songs', 's')
            ->groupBy('s.album');

    }

    /**
     * Get records paginated.
     *
     * @param int $page Current page number
     *
     * @return array Result
     */
    public function findAllPaginated($page = 1)
    {
        $countQueryBuilder = $this->queryAll($page)
        /*$queryBuilder->setFirstResult(($page - 1) * static::NUM_ITEMS)
            ->setMaxResults(static::NUM_ITEMS);*/
        ->select('COUNT(DISTINCT s.id) AS total_results')
            ->setMaxResults(1);

        $paginator = new Paginator($this->queryAll($page), $countQueryBuilder);
        $paginator->setCurrentPage($page);
        $paginator->setMaxPerPage(static::NUM_ITEMS);

        /*$pagesNumber = $this->countAllPages();

        $paginator = [
            'page' => ($page < 1 || $page > $pagesNumber) ? 1 : $page,
            'max_results' => static::NUM_ITEMS,
            'pages_number' => $pagesNumber,
            'data' => $queryBuilder->execute()->fetchAll(),
        ];*/

        return $paginator->getCurrentPageResults();
    }

    /**
     * Count all pages.
     *
     * @return int Result
     */
    protected function countAllPages()
    {
        $pagesNumber = 1;

        $queryBuilder = $this->queryAll();
        $queryBuilder->select('COUNT(DISTINCT s.id) AS total_results')
            ->setMaxResults(1);

        $result = $queryBuilder->execute()->fetch();

        if ($result) {
            $pagesNumber =  ceil($result['total_results'] / static::NUM_ITEMS);
        } else {
            $pagesNumber = 1;
        }

        return $pagesNumber;
    }

    /**
     * Find one record.
     *
     * @param string $id Element id
     *
     * @return array|mixed Result
     */
    public function findOneById($id)
    {
        $queryBuilder = $this->queryAll();
        $queryBuilder->where('t.id = :id')
            ->setParameter(':id', $id, \PDO::PARAM_INT);
        $result = $queryBuilder->execute()->fetch();

        return !$result ? [] : $result;
    }

    public function showAlbum()
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('s.track_id', 's.title', 's.album', 's.artist')
            ->from('songs', 's');

        return $queryBuilder->execute()->fetchAll();
    }

}