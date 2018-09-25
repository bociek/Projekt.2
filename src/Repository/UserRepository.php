<?php
/**
 * User Repository.
 */

namespace Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Utils\Paginator;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class UserRepository
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
     * Load user by his login
     *
     * @param $login
     * @return array
     */
    public function loadUserByLogin($login)
    {
        try{
            $user = $this->getUserByLogin($login);

            if (!$user || !count($user)) {
                throw new UsernameNotFoundException(
                    sprintf('Username "%s" does not exist.', $login)
                );
            }

            $roles = $this->getUserRoles($user['id']);

            if (!$roles || !count($roles)) {
                throw new UsernameNotFoundException(
                    sprintf('Username "%s" does not exist.', $login)
                );
            }

            return [
                'login' => $user['login'],
                'password' => $user['password'],
                'roles' => $roles,
            ];
        } catch (DBALException $exception) {
            throw new UsernameNotFoundException(
                sprintf('Username "%s" does not exist.', $login)
            );
        } catch (UsernameNotFoundException $exception) {
            throw $exception;
        }
    }

    /**
     * Get user by his login
     *
     * @param $login
     * @return array|mixed
     */
    public function getUserByLogin($login)
    {
        try {
            $queryBuilder = $this->db->createQueryBuilder();
            $queryBuilder->select('l.id', 'l.login', 'l.password')
                ->from('users', 'l')
                ->where('l.login = :login')
                ->setParameter(':login', $login, \PDO::PARAM_STR);

            return $queryBuilder->execute()->fetch();
        } catch (DBALException $exception) {
            return [];
        }
    }

    /**
     * Get user's roles.
     *
     * @param $userID
     * @return array
     */
    public function getUserRoles($userID)
    {
        $roles = [];

        try {
            $queryBuilder = $this->db->createQueryBuilder();
            $queryBuilder->select('r.name')
                ->from('users', 'l')
                ->innerJoin('l', 'roles', 'r', 'l.role_id = r.id')
                ->where('l.id = :id')
                ->setParameter(':id', $userID, \PDO::PARAM_INT);
            $result = $queryBuilder->execute()->fetchAll();

            if ($result) {
                $roles = array_column($result, 'name');
            }

            return $roles;
        } catch (DBALException $exception) {
            return $roles;
        }
    }

    /**
     * Register user.
     *
     * @param $user_data
     * @return mixed
     * @throws DBALException
     */
    public function registerUser($user_data)
    {
        try {

            $this->db->beginTransaction();

            $this->insertToUsers($user_data);

            $id = $this->db->lastInsertId();

            $this->insertOtherData($user_data,$id);

            $this->db->commit();

        } catch (DBALException $exception) {
            throw $exception;
        }
        return $user_data;
    }

    /**
     * Insert user's login and password.
     *
     * @param $user_data
     * @return int
     */
    private function insertToUsers($user_data)
    {
        $this->db->insert('users', [
            'login' => $user_data['login'],
            'password' => $user_data['password'],
            'role_id' => 2
        ]);

        return 1;
    }

    /**
     * Insert user's data.
     *
     * @param $user_data
     * @param $insertID
     * @return int
     */
    private function insertOtherData($user_data, $insertID)
    {

        $this->db->insert('user_data', [
            'fname' => $user_data['fname'],
            'lname' => $user_data['lname'],
            'email' => $user_data['email'],
            //'bday' => $user_data['bday'],
            'country' => $user_data['country'],
            'user_id' => $insertID
        ]);

        return 1;
    }

    /**
     * Get user's login by his ID.
     *
     * @return array
     */
    public function getUserIdByLogin($user_login)
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('u.id')
            ->from('users', 'u')
            ->where('u.login = :login')
            ->setParameter(':login', $user_login, \PDO::PARAM_STR);

        return $queryBuilder->execute()->fetch()['id'];
    }

    /**
     * Update user data.
     *
     * @param $data
     * @throws DBALException
     */
    public function updateUserData($data, $id)
    {
        $this->db->update('user_data', $data, ['user_id' => $id]);

        return;
    }

    /**
     * Show all by user.
     *
     * @param $user_id
     * @return array
     */
    public function showAllAlbumsByUser($user_id)
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('d.id', 'd.artist', 'd.song_title', 'd.album_title', 'd.track_id', 'd.year')
            ->from('songs_added', 'd')
            ->where('d.user_id = :id')
            ->innerJoin('d', 'user_data', 'a', 'd.user_id=a.user_id')
            ->setParameter(':id', $user_id, \PDO::PARAM_INT);

        return $queryBuilder->execute()->fetchAll();
    }

    public function showAllChaptersByUser($user_id)
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('c.id', 'c.author', 'c.chapter_title', 'c.audiobook_title', 'c.chapter_id', 'c.year')
            ->from('chapters_added', 'c')
            ->where('c.user_id = :id')
            ->innerJoin('c', 'user_data', 'a', 'c.user_id=a.user_id')
            ->setParameter(':id', $user_id, \PDO::PARAM_INT);

        return $queryBuilder->execute()->fetchAll();
    }

    public function showAllEpisodesByUser($user_id)
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('e.id', 'e.author', 'e.episode_title', 'e.podcast_title', 'e.episode_id', 'e.year')
            ->from('episodes_added', 'e')
            ->where('e.user_id = :id')
            ->innerJoin('e', 'user_data', 'a', 'e.user_id=a.user_id')
            ->setParameter(':id', $user_id, \PDO::PARAM_INT);

        return $queryBuilder->execute()->fetchAll();
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
}