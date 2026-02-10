<?php

namespace common\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * Admin model
 *
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $password
 * @property string $phone
 * @property int $role_id
 * @property int $status
 * @property string $auth_token
 * @property string $token_expire
 * @property string $created
 * @property string $updated
 *
 * @property AdminRole $role
 */
class Admin extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public static function tableName()
    {
        return '{{%admin}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created',
                'updatedAtAttribute' => 'updated',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['name', 'username', 'role_id'], 'required'],
            [['name', 'username', 'phone'], 'string', 'max' => 255],
            ['password', 'string', 'max' => 500],
            ['username', 'unique'],
            [['role_id', 'status'], 'integer'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE]],
            ['role_id', 'exist', 'targetClass' => AdminRole::class, 'targetAttribute' => 'id'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Ism',
            'username' => 'Login',
            'password' => 'Parol',
            'phone' => 'Telefon',
            'role_id' => 'Rol',
            'status' => 'Status',
            'created' => 'Yaratilgan',
            'updated' => 'Yangilangan',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();
        unset($fields['password'], $fields['auth_token'], $fields['token_expire']);

        // created -> created_at, updated -> updated_at
        unset($fields['created'], $fields['updated']);
        $fields['created_at'] = 'created';
        $fields['updated_at'] = 'updated';

        // status -> ACTIVE/INACTIVE
        $fields['status'] = function () {
            return $this->status == self::STATUS_ACTIVE ? 'ACTIVE' : 'INACTIVE';
        };

        return $fields;
    }

    public function extraFields()
    {
        return ['role'];
    }

    public function getRole()
    {
        return $this->hasOne(AdminRole::class, ['id' => 'role_id']);
    }

    public function setPassword($password)
    {
        $this->password = \Yii::$app->security->generatePasswordHash($password);
    }

    public function validatePassword($password)
    {
        return \Yii::$app->security->validatePassword($password, $this->password);
    }
}
