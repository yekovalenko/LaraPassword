<?php
/**
 * This file is part of LaraPassword.
 * Copyright (c) 2019  Yevhenii Kovalenko
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yekovalenko\LaraPassword;

use Yekovalenko\LaraPassword\Exceptions\InvalidData;
use Yekovalenko\LaraPassword\Exceptions\NotFound;
use Yekovalenko\LaraPassword\Exceptions\BadGeneratorAttributes;
use Yekovalenko\LaraPassword\Models\LPPassword;
use Yekovalenko\LaraPassword\Models\LPCategory;
use Illuminate\Encryption\Encrypter;

/**
 * Class LaraPasswordManage
 * @package Yekovalenko\LaraPassword
 */
class LaraPasswordManage
{
    /**
     * Schema builder instance.
     *
     * @var \Illuminate\Database\Schema\Builder
     */
    protected $schema;


    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->encrypter = new Encrypter(config('larapassword.hash'));
    }

    /**
     * @param int $length
     * @param bool $letters
     * @param bool $numbers
     * @param bool $chars
     * @param bool $uppercase
     * @return string
     * @throws BadGeneratorAttributes
     */
    public function random($length = 12, $letters = true, $numbers = true, $chars = true, $uppercase = true)
    {
        if (!is_int($length) || strlen($length) <= 0) {
            throw new BadGeneratorAttributes;
        }

        $string = "";
        if ($letters) {
            $string .= 'abcdefghijkmopqrstuwxyz';
            if ($uppercase) {
                $string .= 'ABCDEFGHIJKLMNPQRSTUWXYZ';
            }
        }
        if ($numbers) {
            $string .= '123456789';
        }
        if ($chars) {
            $string .= '!@#$%^&*(){}:"?.';
        }

        if (!$string || $string === '') {
            throw new BadGeneratorAttributes;
        }
        $pass = array();
        $alphaLength = strlen($string) - 1;
        if ($length > 254)
            $length = 254;
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $string[$n];
        }
        $password = implode($pass);

        return $password;
    }

    /**
     * @param $data
     * @return bool
     */
    private function validatePasswordData($data)
    {
        if ($data['category_id'])
            if (!is_numeric($data['category_id']))
                return false;

        if (!$data['label'])
            return false;
        elseif (strlen($data['label']) > 254)
            return false;

        if ($data['description'])
            if (strlen($data['description']) > 2000)
                return false;

        if (isset($data['metadata']))
            if (!is_array($data['metadata']))
                return false;

        return true;
    }

    /**
     * @param $data
     * @return bool
     */
    private function validateCategoryData($data)
    {
        if ($data['parent_id'])
            if (!is_numeric($data['parent_id']))
                return false;

        if (!$data['title'])
            return false;
        elseif (strlen($data['title']) > 254)
            return false;

        if ($data['description'])
            if (strlen($data['description']) > 2000)
                return false;

        return true;
    }

    /**
     * @param $data
     * @return mixed
     * @throws InvalidData
     * @throws NotFound
     */
    public function addPassword($data)
    {
        $validate = $this->validatePasswordData($data);
        if (!$validate)
            throw new InvalidData('Invalid data!');

        if ($data['category_id']) {
            $category = LPCategory::find($data['category_id']);
            if (!$category)
                throw new NotFound('Category not found');
        }

        $password = new LPPassword;
        $password->category_id = $data['category_id'] ?: 0;
        $password->label = $this->encrypter->encrypt($data['label']);
        $password->login = $this->encrypter->encrypt($data['login']);
        $password->password = $this->encrypter->encrypt($data['password']);
        $password->url = $this->encrypter->encrypt($data['url']);
        $password->description = $data['description'];
        if ($data['metadata']) {
            $password->metadata = serialize($data['metadata']);
        }
        $password->save();
        return $password->id;
    }

    /**
     * @param null $category_id
     * @return array
     */
    public function getPasswords($category_id = null)
    {
        if ($category_id !== null) {
            $passwords = LPPassword::where('category_id', $category_id)->get();
        } else {
            $passwords = LPPassword::all();
        }

        if ($passwords != null) {
            foreach ($passwords as &$password) {
                $password->label = $this->encrypter->decrypt($password->label);
                $password->login = $this->encrypter->decrypt($password->login);
                $password->password = $this->encrypter->decrypt($password->password);
                $password->url = $this->encrypter->decrypt($password->url);
            }
            return $passwords->toArray();
        } else
            return [];
    }

    /**
     * @param $password_id
     * @return array
     * @throws NotFound
     */
    public function getPassword($password_id)
    {
        if (!$password_id)
            throw new NotFound('Password ID is missing');

        $password = LPPassword::find($password_id);
        if ($password != null) {
            $password->label = $this->encrypter->decrypt($password->label);
            $password->login = $this->encrypter->decrypt($password->login);
            $password->password = $this->encrypter->decrypt($password->password);
            $password->url = $this->encrypter->decrypt($password->url);
            return $password->toArray();
        } else
            return [];
    }

//    /**
//     * @param $column
//     * @param $value
//     * @return array
//     * @throws InvalidData
//     */
//    public function searchPassword($column, $value)
//    {
//        if (!$column || !$value)
//            throw new InvalidData('Missing field and value arguments');
//
//        if (!in_array($column, ['label', 'login', 'url']))
//            throw new InvalidData('Password search can be only on the following fields: "label", "login", "url"');
//
//        $passwords = LPPassword::where($column, $value)->get();
//
//        if ($passwords != null) {
//            foreach ($passwords as &$password) {
//                $password->label = $this->encrypter->decrypt($password->label);
//                $password->login = $this->encrypter->decrypt($password->login);
//                $password->password = $this->encrypter->decrypt($password->password);
//                $password->url = $this->encrypter->decrypt($password->url);
//            }
//            return $passwords->toArray();
//        } else
//            return [];
//    }

    /**
     * @param $password_id
     * @param $data
     * @return mixed
     * @throws InvalidData
     * @throws NotFound
     */
    public function editPassword($password_id, $data)
    {
        if (!$password_id)
            throw new NotFound('Password ID is missing');

        $password = LPPassword::find($password_id);
        if (!$password)
            throw new NotFound('Password not found');

        $validate = $this->validatePasswordData($data);
        if (!$validate)
            throw new InvalidData('Invalid data');

        if ($data['parent_id']) {
            $parent_category = LPCategory::find($data['parent_id']);
            if (!$parent_category->exists())
                throw new NotFound('Parent category not found');
        }

        $password->category_id = $data['category_id'] ?: 0;
        $password->label = $this->encrypter->encrypt($data['label']);
        $password->login = $this->encrypter->encrypt($data['login']);
        $password->password = $this->encrypter->encrypt($data['password']);
        $password->url = $this->encrypter->encrypt($data['url']);
        $password->description = $data['description'];
        if ($data['metadata']) {
            $password->metadata = serialize($data['metadata']);
        }
        $password->save();
        return $password->id;
    }

    /**
     * @param $password_id
     * @return mixed
     */
    public function removePassword($password_id)
    {
        if (!$password_id)
            return true;

        $pass = LPPassword::find($password_id);
        if (!$pass)
            return true;

        return $pass->delete();
    }


    /**
     * @param $data
     * @return mixed
     * @throws InvalidData
     * @throws NotFound
     */
    public function addCategory($data)
    {
        $validate = $this->validateCategoryData($data);
        if (!$validate)
            throw new InvalidData('Invalid data!');

        if ($data['parent_id']) {
            $parent_category = LPCategory::find($data['parent_id']);
            if (!$parent_category)
                throw new NotFound('Parent category not found');
        }

        $category = new LPCategory;
        $category->parent_id = $data['parent_id'] ?: 0;
        $category->title = $data['title'];
        $category->description = $data['description'];
        $category->save();
        return $category->id;
    }

    /**
     * @param $category_id
     * @param $data
     * @return mixed
     * @throws InvalidData
     * @throws NotFound
     */
    public function editCategory($category_id, $data)
    {
        if (!$category_id)
            throw new NotFound('Category ID is missing');

        $category = LPCategory::find($category_id);
        if (!$category)
            throw new NotFound('Category not found');

        $validate = $this->validateCategoryData($data);
        if (!$validate)
            throw new InvalidData('Invalid data');

        if ($data['parent_id']) {
            $parent_category = LPCategory::find($data['parent_id']);
            if (!$parent_category->exists())
                throw new NotFound('Parent category not found');
        }

        $category->parent_id = $data['parent_id'] ?: 0;
        $category->title = $data['title'];
        $category->description = $data['description'];
        $category->save();
        return $category->id;
    }

    /**
     * @param $category_id
     * @return array
     * @throws NotFound
     */
    public function getCategory($category_id)
    {
        if (!$category_id)
            throw new NotFound('Category ID is missing');

        $category = LPCategory::find($category_id);
        if ($category != null)
            return $category->toArray();
        else
            return [];
    }

    /**
     * @param null $parent_id
     * @return array
     */
    public function getCategories($parent_id = null)
    {
        if ($parent_id !== null) {
            $categories = LPCategory::where('parent_id', $parent_id)->get();
        } else {
            $categories = LPCategory::all();
        }

        if ($categories != null)
            return $categories->toArray();
        else
            return [];
    }

    /**
     * @param $category_id
     * @return bool
     */
    public function removeCategory($category_id)
    {
        if (!$category_id)
            return true;

        $category = LPCategory::find($category_id);
        if (!$category)
            return true;

        LPPassword::where('category_id', $category_id)->delete();
        $cats = LPCategory::where('parent_id', $category_id)->get();
        foreach ($cats as $cat) {
            $this->removeCategory($cat->id);
            $cat->delete();
        }
        return $category->delete();
    }
}
