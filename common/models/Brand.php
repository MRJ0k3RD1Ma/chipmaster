<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\UploadedFile;

/**
 * Brand model
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $logo_url
 * @property int $status
 * @property string $created
 * @property string $updated
 */
class Brand extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public $logoFile;

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
        ];
    }

    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 50],
            [['slug', 'logo_url'], 'string', 'max' => 255],
            ['slug', 'unique'],
            ['status', 'integer'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE]],
            ['logoFile', 'file', 'extensions' => ['png', 'jpg', 'jpeg', 'gif', 'webp'], 'maxSize' => 5 * 1024 * 1024],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nomi',
            'slug' => 'Slug',
            'logo_url' => 'Logo URL',
            'logoFile' => 'Logo fayl',
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

        return $fields;
    }

    public function uploadLogo()
    {
        if ($this->logoFile === null) {
            return true;
        }

        $uploadPath = Yii::getAlias('@frontend/web/upload/brand');

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $fileName = time() . '_' . Yii::$app->security->generateRandomString(8) . '.' . $this->logoFile->extension;
        $filePath = $uploadPath . '/' . $fileName;

        if ($this->logoFile->saveAs($filePath)) {
            // Eski rasmni o'chirish
            if ($this->logo_url) {
                $oldFile = Yii::getAlias('@frontend/web') . $this->logo_url;
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }
            $this->logo_url = '/upload/brand/' . $fileName;
            return true;
        }

        return false;
    }
}
