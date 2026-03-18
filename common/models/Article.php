<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;
use yii\db\Expression;

/**
 * Article model
 *
 * @property int $id
 * @property string $slug
 * @property string $name_uz
 * @property string $name_ru
 * @property int $navigation_id
 * @property string $description_uz
 * @property string $description_ru
 * @property string $detail_uz
 * @property string $detail_ru
 * @property int $show_counter
 * @property string $publish_date
 * @property int $status
 * @property string $created
 * @property string $updated
 *
 * @property Navigation $navigation
 */
class Article extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public static function tableName()
    {
        return '{{%article}}';
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
            [['name_uz', 'name_ru', 'navigation_id', 'description_uz', 'description_ru'], 'required'],
            [['name_uz', 'name_ru', 'slug'], 'string', 'max' => 255],
            [['description_uz', 'description_ru'], 'string'],
            [['detail_uz', 'detail_ru'], 'string'],
            [['navigation_id', 'show_counter', 'status'], 'integer'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['show_counter', 'default', 'value' => 0],
            ['publish_date', 'safe'],
            ['status', 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE]],
            ['navigation_id', 'exist', 'skipOnError' => true, 'targetClass' => Navigation::class, 'targetAttribute' => ['navigation_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'slug' => 'Slug',
            'name_uz' => 'Nomi (UZ)',
            'name_ru' => 'Nomi (RU)',
            'navigation_id' => 'Navigatsiya',
            'description_uz' => 'Tavsif (UZ)',
            'description_ru' => 'Tavsif (RU)',
            'detail_uz' => 'Batafsil (UZ)',
            'detail_ru' => 'Batafsil (RU)',
            'show_counter' => 'Ko\'rishlar soni',
            'publish_date' => 'Nashr sanasi',
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

    public function extraFields()
    {
        return ['navigation'];
    }

    public function getNavigation()
    {
        return $this->hasOne(Navigation::class, ['id' => 'navigation_id']);
    }

    public function incrementCounter()
    {
        $this->updateCounters(['show_counter' => 1]);
    }
}
