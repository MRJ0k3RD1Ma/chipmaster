<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;
use yii\db\Expression;

/**
 * Category model
 *
 * @property int $id
 * @property int $parent_id
 * @property string $name_uz
 * @property string $name_ru
 * @property string $slug
 * @property string $icon
 * @property int $image_id
 * @property string $spec_template
 * @property int $sort_order
 * @property int $status
 * @property string $created
 * @property string $updated
 *
 * @property File $image
 * @property Category $parent
 * @property Category[] $children
 */
class Category extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public static function tableName()
    {
        return '{{%category}}';
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
                'attribute' => 'name_uz',
                'slugAttribute' => 'slug',
                'ensureUnique' => true,
                'immutable' => false,
            ],
        ];
    }

    public function rules()
    {
        return [
            [['name_uz', 'name_ru'], 'required'],
            [['name_uz', 'name_ru', 'slug', 'icon'], 'string', 'max' => 255],
            [['parent_id', 'sort_order', 'status', 'image_id'], 'integer'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['sort_order', 'default', 'value' => 0],
            ['status', 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE]],
            ['spec_template', 'safe'],
            ['image_id', 'exist', 'skipOnError' => true, 'targetClass' => File::class, 'targetAttribute' => ['image_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Ota kategoriya',
            'name_uz' => 'Nomi (UZ)',
            'name_ru' => 'Nomi (RU)',
            'slug' => 'Slug',
            'icon' => 'Icon',
            'image_id' => 'Rasm',
            'spec_template' => 'Spetsifikatsiya shabloni',
            'sort_order' => 'Tartib',
            'status' => 'Status',
            'created' => 'Yaratilgan',
            'updated' => 'Yangilangan',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        unset($fields['created'], $fields['updated'], $fields['image_id']);
        $fields['created_at'] = 'created';
        $fields['updated_at'] = 'updated';

        $fields['status'] = function () {
            return $this->status == self::STATUS_ACTIVE ? 'ACTIVE' : 'INACTIVE';
        };

        $fields['spec_template'] = function () {
            return $this->spec_template ? json_decode($this->spec_template, true) : null;
        };

        $fields['image'] = function () {
            return $this->getImageData();
        };

        return $fields;
    }

    protected function getImageData()
    {
        $image = $this->image;
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

    public function extraFields()
    {
        return ['parent', 'children'];
    }

    public function getParent()
    {
        return $this->hasOne(Category::class, ['id' => 'parent_id']);
    }

    public function getChildren()
    {
        return $this->hasMany(Category::class, ['parent_id' => 'id']);
    }

    public function getImage()
    {
        return $this->hasOne(File::class, ['id' => 'image_id']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // spec_template JSON formatda saqlash
            if (is_array($this->spec_template)) {
                $this->spec_template = json_encode($this->spec_template, JSON_UNESCAPED_UNICODE);
            }
            return true;
        }
        return false;
    }
}
