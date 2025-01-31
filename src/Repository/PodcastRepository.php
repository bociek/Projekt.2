<?php
/**
 * Podcast Repository.
 */

namespace Repository;

use Doctrine\DBAL\Connection;
use Utils\Paginator;

class PodcastRepository
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
     * PodcastRepository constructor.
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

        return $queryBuilder->select( 'e.title_id', 'e.author', 'p.title', 'e.year')
            ->from('episodes', 'e')
            ->innerJoin('e', 'podcasts', 'p', 'e.title_id=p.id')
            ->groupBy('e.title_id');
    }

    public function findAllPaginated($page = 1)
    {
        $countQueryBuilder = $this->queryAll($page)
            ->select('COUNT(DISTINCT p.id) AS total_results')
            ->setMaxResults(1);

        $paginator = new Paginator($this->queryAll($page), $countQueryBuilder);
        $paginator->setCurrentPage($page);
        $paginator->setMaxPerPage(static::NUM_ITEMS);

        return $paginator->getCurrentPageResults();
    }

    protected function countAllPages()
    {
        $pagesNumber = 1;

        $queryBuilder = $this->queryAll();
        $queryBuilder->select('COUNT(DISTINCT p.id) AS total_results')
            ->setMaxResults(1);

        $result = $queryBuilder->execute()->fetch();

        if ($result) {
            $pagesNumber =  ceil($result['total_results'] / static::NUM_ITEMS);
        } else {
            $pagesNumber = 1;
        }

        return $pagesNumber;
    }

    public function showEpisodes($id)
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('e.episode_id', 'e.episode_title', 'p.title', 'e.author')
            ->from('episodes', 'e')
            ->innerJoin('e', 'podcasts', 'p', 'e.title_id=p.id')
            ->where('e.title_id = :id')
            ->setParameter(':id', $id, \PDO::PARAM_INT);

        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * Add podcast.
     *
     * @param $podcast
     * @return int
     */
    public function addPodcast($podcast)
    {
        try {

            $this->db->beginTransaction();

            $this->db->insert('podcasts_data', [
                'author' => $podcast['author'],
                'title' => $podcast['title'],
                'year' => $podcast['year'],
            ]);

            $this->db->commit();

        } catch (DBALException $exception) {
            throw $exception;
        }
    }
}