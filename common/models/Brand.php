<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;
use yii\db\Expression;

/**
 * Brand model
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $logo_id
 * @property int $status
 * @property string $created
 * @property string $updated
 *
 * @property File $logo
 */
class Brand extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public static function tableName()
    {
        return '{{%brand}}';
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
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'name',
                'slugAttribute' => 'slug',
                'ensureUnique' => true,
                'immutable' => false,
            ],
        ];
    }

    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 50],
            ['slug', 'string', 'max' => 255],
            ['slug', 'unique'],
            [['logo_id', 'status'], 'integer'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE]],
            ['logo_id', 'exist', 'skipOnError' => true, 'targetClass' => File::class, 'targetAttribute' => ['logo_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nomi',
            'slug' => 'Slug',
            'logo_id' => 'Logo',
            'status' => 'Status',
            'created' => 'Yaratilgan',
            'updated' => 'Yangilangan',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        unset($fields['created'], $fields['updated'], $fields['logo_id']);
        $fields['created_at'] = 'created';
        $fields['updated_at'] = 'updated';

        $fields['status'] = function () {
            return $this->status == self::STATUS_ACTIVE ? 'ACTIVE' : 'INACTIVE';
        };

        $fields['logo'] = function () {
            return $this->getImageData();
        };

        return $fields;
    }

    /**
     * Logo file relation
     */
    public function getLogo()
    {
        return $this->hasOne(File::class, ['id' => 'logo_id']);
    }

    protected function getImageData()
    {
        $image = $this->logo;
        if (!$image) {
            return null;
        }

        $baseUrl = '/api/v1/getfile/' . $image->slug;
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $isImage = in_array(strtolower($image->exts), $imageExtensions);

        $data = [
            'id' => $image->id,
            'status' => $image->status == File::STATUS_ACTIVE ? 'ACTIVE' : 'INACTIVE',
            'slug' => $image->slug,
            'exts' => $image->exts,
            'download_url' => $baseUrl,
            'download' => null,
            'url' => null,
        ];

        if ($isImage) {
            $data['download'] = [
                'sm' => $baseUrl . '?size=sm',
                'md' => $baseUrl . '?size=md',
                'lg' => $baseUrl . '?size=lg',
            ];
            $data['url'] = [
                'sm' => $image->getSmallUrl(),
                'md' => $image->getMediumUrl(),
                'lg' => $image->getOriginalUrl(),
            ];
        }

        return $data;
    }
}
