<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;
use yii\db\Expression;

/**
 * Product model
 *
 * @property int $id
 * @property int $category_id
 * @property string $brand_id
 * @property string $name_uz
 * @property string $name_ru
 * @property string $slug
 * @property string $description_uz
 * @property string $description_ru
 * @property string $sku
 * @property int $price
 * @property int $discount_price
 * @property string $discount_expires
 * @property string $specifecations
 * @property int $stock_quantity
 * @property int $status
 * @property int $featured
 * @property string $seo_title
 * @property string $seo_description
 * @property string $created
 * @property string $updated
 * @property int $image_id
 * @property int $is_device
 *
 * @property Category $category
 * @property Brand $brand
 * @property File $image
 * @property ProductImage[] $images
 * @property ProductGuide[] $guides
 */
class Product extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const FEATURED_NO = 0;
    const FEATURED_YES = 1;

    const IS_DEVICE_NO = 0;
    const IS_DEVICE_YES = 1;

    public static function tableName()
    {
        return '{{%product}}';
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
            [['category_id', 'name_uz', 'name_ru', 'sku', 'price'], 'required'],
            [['name_uz', 'name_ru', 'slug', 'sku', 'seo_title'], 'string', 'max' => 255],
            [['brand_id'], 'string', 'max' => 255],
            [['description_uz', 'description_ru', 'seo_description'], 'string'],
            [['category_id', 'price', 'discount_price', 'stock_quantity', 'status', 'featured', 'image_id', 'is_device'], 'integer'],
            ['sku', 'unique'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['featured', 'default', 'value' => self::FEATURED_YES],
            ['is_device', 'default', 'value' => self::IS_DEVICE_YES],
            ['discount_price', 'default', 'value' => 0],
            ['stock_quantity', 'default', 'value' => 0],
            ['status', 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE]],
            ['featured', 'in', 'range' => [self::FEATURED_NO, self::FEATURED_YES]],
            ['is_device', 'in', 'range' => [self::IS_DEVICE_NO, self::IS_DEVICE_YES]],
            ['discount_expires', 'safe'],
            ['specifecations', 'safe'],
            ['category_id', 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
            ['image_id', 'exist', 'skipOnError' => true, 'targetClass' => File::class, 'targetAttribute' => ['image_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Kategoriya',
            'brand_id' => 'Brend',
            'name_uz' => 'Nomi (UZ)',
            'name_ru' => 'Nomi (RU)',
            'slug' => 'Slug',
            'description_uz' => 'Tavsif (UZ)',
            'description_ru' => 'Tavsif (RU)',
            'sku' => 'SKU',
            'price' => 'Narx',
            'discount_price' => 'Chegirma narxi',
            'discount_expires' => 'Chegirma muddati',
            'specifecations' => 'Xususiyatlar',
            'stock_quantity' => 'Ombordagi soni',
            'status' => 'Status',
            'featured' => 'Tanlangan',
            'seo_title' => 'SEO sarlavha',
            'seo_description' => 'SEO tavsif',
            'image_id' => 'Rasm',
            'is_device' => 'Qurilma',
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

        $fields['featured'] = function () {
            return (bool)$this->featured;
        };

        $fields['is_device'] = function () {
            return (bool)$this->is_device;
        };

        $fields['specifecations'] = function () {
            return $this->specifecations ? json_decode($this->specifecations, true) : null;
        };

        return $fields;
    }

    public function extraFields()
    {
        return ['category', 'brand', 'image', 'images', 'guides'];
    }

    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    public function getBrand()
    {
        return $this->hasOne(Brand::class, ['id' => 'brand_id']);
    }

    public function getImage()
    {
        return $this->hasOne(File::class, ['id' => 'image_id']);
    }

    public function getImages()
    {
        return $this->hasMany(ProductImage::class, ['product_id' => 'id'])
            ->andWhere(['status' => ProductImage::STATUS_ACTIVE])
            ->orderBy(['sort_order' => SORT_ASC]);
    }

    public function getGuides()
    {
        return $this->hasMany(ProductGuide::class, ['product_id' => 'id'])
            ->andWhere(['status' => ProductGuide::STATUS_ACTIVE])
            ->orderBy(['sort_order' => SORT_ASC]);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (is_array($this->specifecations)) {
                $this->specifecations = json_encode($this->specifecations, JSON_UNESCAPED_UNICODE);
            }
            return true;
        }
        return false;
    }
}
