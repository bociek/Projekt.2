<?php
/**
 * Album Repository.
 */

namespace Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Utils\Paginator;

class AlbumRepository
{
    /**
     * Number of items per page.
     *
     * const int NUM_ITEMS
     */
    const NUM_ITEMS = 100;
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

        return $queryBuilder->select( 's.album_id', 's.artist', 'a.album', 's.year')
            ->from('songs', 's')
            ->innerJoin('s', 'albums', 'a', 's.album_id=a.id')
            ->groupBy('s.album_id');

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

       /* /*$pagesNumber = $this->countAllPages();

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

    /**
     * Show album.
     *
     * @param $id
     * @return array
     */
    public function showAlbum()
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('s.album_id', 's.title', 'a.album', 's.artist', 's.year')
            ->from('songs', 's')
            ->innerJoin('s', 'albums', 'a', 's.album_id=a.id')
            ->groupBy('a.album');
            /*->where('s.album_id = :id')
            ->setParameter(':id', $id, \PDO::PARAM_INT);*/

        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * Add album.
     *
     * @param $album
     * @return int
     * @throws DBALException
     */
    public function addAlbum($album)
    {
        try {

            $this->db->beginTransaction();

            $this->db->insert('albums_data', [
                'artist' => $album['artist'],
                'album' => $album['album'],
                'year' => $album['year'],
            ]);

            $this->db->commit();

        } catch (DBALException $exception) {
            throw $exception;
        }
    }
}