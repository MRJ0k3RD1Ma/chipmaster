<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\UploadedFile;

/**
 * Category model
 *
 * @property int $id
 * @property int $parent_id
 * @property string $name_uz
 * @property string $name_ru
 * @property string $slug
 * @property string $icon
 * @property string $image
 * @property string $spec_template
 * @property int $sort_order
 * @property int $status
 * @property string $created
 * @property string $updated
 */
class Category extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public $imageFile;

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
        ];
    }

    public function rules()
    {
        return [
            [['name_uz', 'name_ru'], 'required'],
            [['name_uz', 'name_ru', 'slug', 'icon', 'image'], 'string', 'max' => 255],
            ['slug', 'unique'],
            [['parent_id', 'sort_order', 'status'], 'integer'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['sort_order', 'default', 'value' => 0],
            ['status', 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE]],
            ['spec_template', 'safe'],
            ['imageFile', 'file', 'extensions' => ['png', 'jpg', 'jpeg', 'gif', 'webp'], 'maxSize' => 5 * 1024 * 1024],
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
            'image' => 'Rasm',
            'imageFile' => 'Rasm fayl',
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

        unset($fields['created'], $fields['updated']);
        $fields['created_at'] = 'created';
        $fields['updated_at'] = 'updated';

        $fields['status'] = function () {
            return $this->status == self::STATUS_ACTIVE ? 'ACTIVE' : 'INACTIVE';
        };

        $fields['spec_template'] = function () {
            return $this->spec_template ? json_decode($this->spec_template, true) : null;
        };

        return $fields;
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

    public function uploadImage()
    {
        if ($this->imageFile === null) {
            return true;
        }

        $uploadPath = Yii::getAlias('@frontend/web/upload/category');

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $fileName = time() . '_' . Yii::$app->security->generateRandomString(8) . '.' . $this->imageFile->extension;
        $filePath = $uploadPath . '/' . $fileName;

        if ($this->imageFile->saveAs($filePath)) {
            // Eski rasmni o'chirish
            if ($this->image) {
                $oldFile = Yii::getAlias('@frontend/web') . $this->image;
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }
            $this->image = '/upload/category/' . $fileName;
            return true;
        }

        return false;
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
