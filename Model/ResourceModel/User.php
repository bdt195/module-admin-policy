<?php
declare(strict_types=1);

namespace TonyB\AdminPolicy\Model\ResourceModel;

use Magento\Framework\Acl\Data\CacheInterface;
use Magento\Framework\App\ObjectManager;
use Magento\User\Model\Backend\Config\ObserverConfig;
use Magento\User\Model\User as ModelUser;

/**
 * Class User
 */
class User extends \Magento\User\Model\ResourceModel\User
{
    const PASSWORD_RETAIN_LIMIT = 24;

    /**
     * @var ObserverConfig|null
     */
    protected $observerConfig;

    /**
     * User constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Authorization\Model\RoleFactory $roleFactory
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param null $connectionName
     * @param CacheInterface|null $aclDataCache
     * @param ObserverConfig|null $observerConfig
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Authorization\Model\RoleFactory $roleFactory,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        $connectionName = null,
        CacheInterface $aclDataCache = null,
        ObserverConfig $observerConfig = null
    ) {
        parent::__construct(
            $context,
            $roleFactory,
            $dateTime,
            $connectionName,
            $aclDataCache,
            $observerConfig
        );

        $this->observerConfig = $observerConfig ?: ObjectManager::getInstance()->get(ObserverConfig::class);
    }

    /**
     * Purge and get remaining old password hashes
     *
     * @param ModelUser $user
     * @param int $retainLimit
     * @return array
     */
    public function getOldPasswords($user, $retainLimit = self::PASSWORD_RETAIN_LIMIT): array
    {
        $userId = (int)$user->getId();
        $table = $this->getTable('admin_passwords');

        $this->removeOldPassword($user, $retainLimit);
        $this->removeExpiredPassword($user);

        // get all remaining passwords
        return $this->getConnection()->fetchCol(
            $this->getConnection()
                ->select()
                ->from($table, 'password_hash')
                ->where('user_id = :user_id'),
            [':user_id' => $userId]
        );
    }

    /**
     * @param ModelUser $user
     * @param $retainLimit
     */
    protected function removeOldPassword(ModelUser $user, $retainLimit)
    {
        $userId = (int)$user->getId();
        $table = $this->getTable('admin_passwords');

        $retainPasswordIds = $this->getConnection()->fetchCol(
            $this->getConnection()
                ->select()
                ->from($table, 'password_id')
                ->where('user_id = :user_id')
                ->order('password_id ' . \Magento\Framework\DB\Select::SQL_DESC)
                ->limit($retainLimit),
            [':user_id' => $userId]
        );
        $where = [
            'user_id = ?' => $userId
        ];
        if ($retainPasswordIds) {
            $where['password_id NOT IN (?)'] = $retainPasswordIds;
        }
        $this->getConnection()->delete($table, $where);
    }

    /**
     * @param ModelUser $user
     */
    protected function removeExpiredPassword(ModelUser $user)
    {
        $userId = (int)$user->getId();
        $table = $this->getTable('admin_passwords');

        $where = [
            'user_id = ?' => $userId,
            'last_updated <= ?' =>
                time() - $this->observerConfig->getAdminPasswordLifetime() * self::PASSWORD_RETAIN_LIMIT
        ];
        $this->getConnection()->delete($table, $where);
    }
}
