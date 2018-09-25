<?php
/**
 * Myshelf repository.
 */
namespace Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Utils\Paginator;

class MyshelfRepository
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
            ->select('COUNT(DISTINCT d.id) AS total_results')
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
     * Query all records.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder Result
     */
    protected function queryAll()
    {
        $queryBuilder = $this->db->createQueryBuilder();

        return $queryBuilder->select( 'd.artist', 'd.song_title', 'd.album_title', 'd.track_id', 'd.year')
            ->from('songs_added', 'd');

    }

    /**
     * Show MyShelf
     *
     * Shows items that user added.
     *
     * @param $id
     */
    public function showMyshelf($id)
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('d.id', 'd.artist', 'd.song_title', 'd.album_title', 'd.track_id', 'd.year', 'd.user_id')
            ->from('songs_added', 'd');
    }

    /**
     * Add song.
     *
     * Adds song.
     *
     * @param $album
     * @return int
     * @throws DBALException
     */
    public function addSong($song)
    {
        try {

            $this->db->beginTransaction();

            $this->db->insert('songs_added', [
                'artist' => $song['artist'],
                'song_title' => $song['song_title'],
                'album_title' => $song['album_title'],
                'track_id' => $song['track_id'],
                'year' => $song['year'],
                'user_id' => $song['user_id']
            ]);

            $this->db->commit();

        } catch (DBALException $exception) {
            throw $exception;
        }
    }

    /**
     * Insert album to separate table.
     *
     * @param $album
     */
 /*   private function insertAlbum($album)
    {
        $this->db->insert('albums_data', [
            'artist' => $album['artist'],
            'album' => $album['album'],
            'year' => $album['year']
        ]);
    }*/

    /**
     * Select album
     *
     * @param $album
     * @return array
     */
 /*   private function selectAlbum()
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('a.artist', 'a.album', 'a.year')
            ->from('albums_added', 'a');

        return $queryBuilder->execute()->fetchAll();
    }*/

   /* private function similarName($subject, $pattern)
    {
        $subject = ;

        foreach($subject as $album) {

        }
    }*/

    /**
     * Delete song.
     *
     * @param $id
     * @throws DBALException
     */
    public function deleteSong($id)
    {
        try {

            $this->db->beginTransaction();

            $this->db->delete('songs_added', ['id' => $id]);

            $this->db->commit();

        } catch (DBALException $exception) {
            throw $exception;
        }
    }

    /**
     * Add chapter.
     *
     * @param $chapter
     * @throws DBALException
     */
    public function addChapter($chapter)
    {
        try {

            $this->db->beginTransaction();

            $this->db->insert('chapters_added', [
                'author' => $chapter['author'],
                'chapter_title' => $chapter['chapter_title'],
                'audiobook_title' => $chapter['audiobook_title'],
                'chapter_id' => $chapter['chapter_id'],
                'year' => $chapter['year'],
                'user_id' => $chapter['user_id']
            ]);

            $this->db->commit();

        } catch (DBALException $exception) {
            throw $exception;
        }
    }

    /**
     * Delete chapter.
     *
     * @param $id
     * @throws DBALException
     */
    public function deleteChapter($id)
    {
        try {

            $this->db->beginTransaction();

            $this->db->delete('chapters_added', ['id' => $id]);

            $this->db->commit();

        } catch (DBALException $exception) {
            throw $exception;
        }
    }

    /**
     * Add episode.
     *
     * @param $episode
     * @throws DBALException
     */
    public function addEpisode($episode)
    {
        try {

            $this->db->beginTransaction();

            $this->db->insert('episodes_added', [
                'author' => $episode['author'],
                'episode_title' => $episode['episode_title'],
                'podcast_title' => $episode['podcast_title'],
                'episode_id' => $episode['episode_id'],
                'year' => $episode['year'],
                'user_id' => $episode['user_id']
            ]);

            $this->db->commit();

        } catch (DBALException $exception) {
            throw $exception;
        }
    }

    /**
     * Delete episode.
     *
     * @param $id
     * @throws DBALException
     */
    public function deleteEpisode($id)
    {
        try {

            $this->db->beginTransaction();

            $this->db->delete('episodes_added', ['id' => $id]);

            $this->db->commit();

        } catch (DBALException $exception) {
            throw $exception;
        }
    }

    /*public function showMyAlbums($id)
    {
        $queryBuilder = $this->db->createQueryBuilder();
    }

    public function showMyAudiobooks($id)
    {
        $queryBuilder = $this->db->createQueryBuilder();
    }

    public function showMyPodcasts($id)
    {
        $queryBuilder = $this->db->createQueryBuilder();
    }*/
}