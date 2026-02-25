<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * User model
 *
 * @property int $id
 * @property string $phone
 * @property string|null $name
 * @property int $is_verified
 * @property int $status
 * @property string $created
 * @property string $updated
 * @property array|null $settings
 */
class User extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const VERIFIED_NO = 0;
    const VERIFIED_YES = 1;

    public static function tableName()
    {
        return '{{%user}}';
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
            ['phone', 'required'],
            ['phone', 'string', 'max' => 255],
            ['phone', 'unique'],
            ['name', 'string', 'max' => 255],
            [['is_verified', 'status'], 'integer'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE]],
            ['is_verified', 'default', 'value' => self::VERIFIED_NO],
            ['is_verified', 'in', 'range' => [self::VERIFIED_NO, self::VERIFIED_YES]],
            ['settings', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phone' => 'Telefon',
            'name' => 'Ism',
            'is_verified' => 'Tasdiqlangan',
            'status' => 'Status',
            'created' => 'Yaratilgan',
            'updated' => 'Yangilangan',
            'settings' => 'Sozlamalar',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        unset($fields['created'], $fields['updated']);
        $fields['created_at'] = 'created';
        $fields['updated_at'] = 'updated';

        $fields['status'] = function () {
            return $this->status == self::STATUS_ACTIVE ? 'ACTIVE' : 'INACTIVE';
        };

        $fields['is_verified'] = function () {
            return $this->is_verified == self::VERIFIED_YES;
        };

        return $fields;
    }

    /**
     * Find user by phone number
     *
     * @param string $phone
     * @return static|null
     */
    public static function findByPhone($phone)
    {
        return static::findOne(['phone' => $phone, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Get settings as array
     *
     * @return array
     */
    public function getSettingsArray()
    {
        if (empty($this->settings)) {
            return [];
        }
        return is_array($this->settings) ? $this->settings : json_decode($this->settings, true);
    }

    /**
     * Set settings from array
     *
     * @param array $settings
     */
    public function setSettingsArray($settings)
    {
        $this->settings = json_encode($settings);
    }
}
