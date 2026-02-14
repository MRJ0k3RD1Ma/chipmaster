<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * ProductImage model
 *
 * @property int $id
 * @property int $product_id
 * @property int $image_id
 * @property string $alt_text
 * @property int $sort_order
 * @property int $is_primary
 * @property string $created
 * @property string $updated
 * @property int $status
 *
 * @property Product $product
 * @property File $image
 */
class ProductImage extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const IS_PRIMARY_NO = 0;
    const IS_PRIMARY_YES = 1;

    public static function tableName()
    {
        return '{{%product_image}}';
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
            [['product_id', 'image_id'], 'required'],
            [['product_id', 'image_id', 'sort_order', 'is_primary', 'status'], 'integer'],
            [['alt_text'], 'string', 'max' => 255],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['sort_order', 'default', 'value' => 0],
            ['is_primary', 'default', 'value' => self::IS_PRIMARY_NO],
            ['status', 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE]],
            ['is_primary', 'in', 'range' => [self::IS_PRIMARY_NO, self::IS_PRIMARY_YES]],
            ['product_id', 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
            ['image_id', 'exist', 'skipOnError' => true, 'targetClass' => File::class, 'targetAttribute' => ['image_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Mahsulot',
            'image_id' => 'Rasm',
            'alt_text' => 'Alt matn',
            'sort_order' => 'Tartib',
            'is_primary' => 'Asosiy rasm',
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

        $fields['is_primary'] = function () {
            return (bool)$this->is_primary;
        };

        return $fields;
    }

    public function extraFields()
    {
        return ['product', 'image'];
    }

    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    public function getImage()
    {
        return $this->hasOne(File::class, ['id' => 'image_id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($this->is_primary == self::IS_PRIMARY_YES) {
            // Boshqa rasmlarni is_primary=0 qilish
            self::updateAll(
                ['is_primary' => self::IS_PRIMARY_NO],
                ['and', ['product_id' => $this->product_id], ['!=', 'id', $this->id]]
            );

            // Mahsulotning image_id sini yangilash
            Product::updateAll(
                ['image_id' => $this->image_id],
                ['id' => $this->product_id]
            );
        }
    }
}
