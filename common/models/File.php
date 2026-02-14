<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * File model
 *
 * @property int $id
 * @property string $name
 * @property string $exts
 * @property string $url
 * @property int $status
 * @property string $created
 * @property string $updated
 */
class File extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public static function tableName()
    {
        return '{{%files}}';
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
            [['name', 'url'], 'required'],
            [['name', 'exts', 'url'], 'string', 'max' => 255],
            [['status'], 'integer'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE]],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Fayl nomi',
            'exts' => 'Kengaytma',
            'url' => 'URL',
            'status' => 'Status',
            'created' => 'Yaratilgan',
            'updated' => 'Yangilangan',
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

        // URL ni JSON formatdan decode qilish
        $fields['url'] = function () {
            $decoded = json_decode($this->url, true);
            return $decoded ?: $this->url;
        };

        return $fields;
    }

    /**
     * Rasm URL larini olish
     */
    public function getUrls()
    {
        $decoded = json_decode($this->url, true);
        return $decoded ?: ['original' => $this->url];
    }

    /**
     * Original URL
     */
    public function getOriginalUrl()
    {
        $urls = $this->getUrls();
        return $urls['original'] ?? $this->url;
    }

    /**
     * Medium URL
     */
    public function getMediumUrl()
    {
        $urls = $this->getUrls();
        return $urls['medium'] ?? $this->getOriginalUrl();
    }

    /**
     * Small URL
     */
    public function getSmallUrl()
    {
        $urls = $this->getUrls();
        return $urls['small'] ?? $this->getOriginalUrl();
    }
}
