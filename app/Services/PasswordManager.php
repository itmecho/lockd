<?php

namespace Lockd\Services;

use Illuminate\Contracts\Encryption\Encrypter;
use Lockd\Models\Folder;
use Lockd\Models\Password;

/**
 * Class PasswordManager
 *
 * @package Lockd\Services
 * @author Iain Earl <synapse791@gmail.com>
 */
class PasswordManager extends BaseService
{
    /** @var Encrypter */
    private $encrypter;

    /**
     * PasswordManager constructor
     *
     * @param Encrypter $encrypter
     */
    public function __construct(Encrypter $encrypter)
    {
        $this->encrypter = $encrypter;
    }

    /**
     * Creates a new Password with the provided details
     *
     * @param Folder $folder
     * @param string $name
     * @param string $password
     * @param string|null $url
     * @param string|null $user
     * @return bool|Password
     */
    public function create(Folder $folder, $name, $password, $url = null, $user = null)
    {
        $errors = [];

        if (empty($name))
            $errors[] = 'Please provide a name';

        if (empty($password))
            $errors[] = 'Please provide a password';

        if (!empty($errors))
            return $this->setBadRequestError($errors);

        $newPassword = new Password();

        $newPassword->name = $name;
        $newPassword->url = $url;
        $newPassword->user = $user;
        $newPassword->password = $this->encrypter->encrypt($password);

        if (!$this->associateEntities($newPassword->folder(), $folder))
            return false;

        if (!$this->saveEntity($newPassword))
            return false;

        return $newPassword;
    }

    /**
     * Update a Password based on the passed data
     *
     * @param Password $password
     * @param array $data
     * @return bool|Password
     */
    public function update(Password $password, $data = [])
    {
        if (empty($data))
            return $password;

        if (isset($data['name']) && !empty($data['name']))
            $password->name = $data['name'];

        if (isset($data['password']) && !empty($data['password']))
            $password->password = $this->encrypter->encrypt($data['password']);

        if (isset($data['url']) && !empty($data['url']))
            $password->url = $data['url'];

        if (isset($data['user']) && !empty($data['user']))
            $password->user = $data['user'];

        if (isset($data['folder']) && !empty($data['folder']))
            if (!$this->associateEntities($password->folder(), $data['folder']))
                return false;

        if (!$this->saveEntity($password))
            return false;

        return $password;
    }

    /**
     * Returns decrypted password from Password entity
     *
     * @param Password $password
     * @return string
     */
    public function getPassword(Password $password)
    {
        return $this->encrypter->decrypt($password->password);
    }
}