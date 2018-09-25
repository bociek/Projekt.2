<?php
/**
 * Photo Repository.
 */

namespace Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
class PhotoRepository
{
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
     * Save record.
     *
     * @param array $photo Photo
     *
     * @return boolean Result
     */
    public function save($photo)
    {
        if (isset($photo['id']) && ctype_digit((string) $photo['id'])) {
            // update record
            $id = $photo['id'];
            unset($photo['id']);

            return $this->db->update('photos', $photo, ['id' => $id]);

        } else {
            // add new record
            return $this->db->insert('photos', $photo);
        }
    }
}