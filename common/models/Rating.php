<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * Rating model
 *
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property int $rate
 * @property int $order_id
 * @property string $description
 * @property int $status
 * @property string $created
 * @property string $updated
 *
 * @property User $user
 * @property Product $product
 * @property Orders $order
 */
class Rating extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public static function tableName()
    {
        return '{{%rating}}';
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
            [['user_id', 'product_id', 'order_id'], 'required'],
            [['user_id', 'product_id', 'order_id', 'rate', 'status'], 'integer'],
            [['description'], 'string'],
            ['rate', 'default', 'value' => 5],
            ['rate', 'integer', 'min' => 1, 'max' => 5],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE]],
            ['user_id', 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            ['product_id', 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Foydalanuvchi',
            'product_id' => 'Mahsulot',
            'rate' => 'Baho',
            'order_id' => 'Buyurtma',
            'description' => 'Izoh',
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
        return ['user', 'product', 'order'];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    public function getOrder()
    {
        return $this->hasOne(Orders::class, ['id' => 'order_id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $this->updateProductRating();
    }

    public function afterDelete()
    {
        parent::afterDelete();
        $this->updateProductRating();
    }

    protected function updateProductRating()
    {
        $avgRating = (int) round(
            self::find()
                ->where(['product_id' => $this->product_id, 'status' => self::STATUS_ACTIVE])
                ->average('rate')
        );

        Product::updateAll(
            ['rating' => $avgRating ?: 5],
            ['id' => $this->product_id]
        );
    }
}
