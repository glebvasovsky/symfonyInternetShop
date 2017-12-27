<?php

namespace AppBundle\Service;

use AppBundle\Entity\Category;
use AppBundle\Entity\CategoryProduct;
use AppBundle\Entity\CategoryRelation;
use AppBundle\Entity\ProductPrice;
use AppBundle\Entity\ProductPhoto;
use AppBundle\Entity\File;
use AppBundle\Entity\Level;
use AppBundle\Entity\Product;
use AppBundle\Entity\FieldType;
use AppBundle\Entity\FieldValue;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use finfo;

class CreateProduct 
{
    /**
     * @var array
     */
    protected $namingRules = [
            'Чайники' => 'Чайник',
            'Термопоты' => 'Термопот',
            'Плитки' => 'Плитка',
            'Весы' => 'Весы',
            'Блендеры миксеры' => '',
            'Вафельницы' => 'Вафельница',
            'Кофеварки кофемолки' => '',
            'Кипятильники' => 'Кипятильник',
            'Утюги' => 'Утюг',
            'Уход' => '',
            'Чайники со свистком' => 'Чайник со свистком',
            'Заварочные чайники' => 'Заварочный чайник',
            'Френч-прессы' => 'Френч-пресс',
            'ТВ товар' => '',
            'Хозтовары' => '',
        ];
    
    /**
     * @var array
     */
    protected $categoryNamingRules = [
            'Чайники' => 'teapots',
            'Термопоты' => 'thermopot',
            'Плитки' => 'stoves',
            'Весы' => 'weighers',
            'Блендеры миксеры' => 'blenders mixers',
            'Вафельницы' => 'waffle irons',
            'Кофеварки кофемолки' => 'coffee makers coffee grinders',
            'Кипятильники' => 'boilers',
            'Утюги' => 'irons',
            'Уход' => 'care',
            'Чайники со свистком' => 'whistling kettles',
            'Заварочные чайники' => 'teapot for making tea',
            'Френч-прессы' => 'french-press',
            'ТВ товар' => 'tv goods',
            'Хозтовары' => 'household goods',
            'Gelberk' => 'gelberk',
            'Техника для кухни' => 'kitchen',
            'Посуда' => 'dishes',
        ];
    
    /**
     * @var array
     */
    protected $priceLevel;
    
    /**
     * @var EntityManager
     */
    protected $em;
    
    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em) {
        $this->em = $em;
        
        $this->priceLevel = [
            'prepayment' => $this->em
                ->getRepository(Level::class)
                ->findOneBy(['name' => 'prepayment']),
            'prepayment_delivery' => $this->em
                ->getRepository(Level::class)
                ->findOneBy(['name' => 'prepayment_delivery']),
            'postponement_payment' => $this->em
                ->getRepository(Level::class)
                ->findOneBy(['name' => 'postponement_payment']),
        ];
    }

    /**
     * @param string $basePath
     * @param string $fileName
     */
    public function create(string $basePath, string $fileName): void
    {
        if (empty($this->priceLevel['prepayment'])) {
            $this->priceLevel['prepayment'] = new Level();
            $this->priceLevel['prepayment']->setName('prepayment');
            
            $this->em->persist($this->priceLevel['prepayment']);
        }   
        
        if (empty($this->priceLevel['prepayment_delivery'])) {
            $this->priceLevel['prepayment_delivery'] = new Level();
            $this->priceLevel['prepayment_delivery']->setName('prepayment_delivery');
            
            $this->em->persist($this->priceLevel['prepayment_delivery']);
        }   
        
        if (empty($this->priceLevel['postponement_payment'])) {
            $this->priceLevel['postponement_payment'] = new Level();
            $this->priceLevel['postponement_payment']->setName('postponement_payment');
            
            $this->em->persist($this->priceLevel['postponement_payment']);
        }   
        
        if (false === ($handle = fopen($basePath . '/app/Resources/doc/' . $fileName, 'r')) ) {
            throw new FileNotFoundException('Файл ' . $fileName . ' с каталогом товаров не найден в директории ' . $basePath . '/app/Resources/doc/');
        }
        
        if (false === ($csvArray = fgetcsv($handle, 0, ';', '"'))) {
            throw new Exception('Файл ' . $basePath . '/app/Resources/doc/' . $fileName . ' пустой');
        }
        
        $labels = [
            'code' => $csvArray[0],
            'vendorCode' => $csvArray[1],
            'discription' => $csvArray[2],
            'GLK СпецЦена (предоплата)' => $csvArray[3],
            'GLK Дилер(предопл+достав)' => $csvArray[4],
            'GLK Дилер (отсрочка)' => $csvArray[5],
            'inStockSPB' => $csvArray[6],
            'inStockMSK' => $csvArray[7],
            'isNew' => $csvArray[8],
            'boxAmount' => $csvArray[9],
            'consumerPackBarcode' => $csvArray[10],
            'transportPackBarcode' => $csvArray[11],
            'category' => $csvArray[12],
            'rootCategory' => $csvArray[13],
        ];

        while (false !== ($csvOriginal = fgetcsv($handle, 0, ';', '"'))) {
            $csv = array_map('trim', $csvOriginal);

            $data = [
                'code' => $csv[0],
                'vendorCode' => $csv[1],
                'discription' => $csv[2],
                'GLK СпецЦена (предоплата)' => $csv[3],
                'GLK Дилер(предопл+достав)' => $csv[4],
                'GLK Дилер (отсрочка)' => $csv[5],
                'inStockSPB' => $csv[6],
                'inStockMSK' => $csv[7],
                'isNew' => $csv[8],
                'boxAmount' => $csv[9],
                'consumerPackBarcode' => $csv[10],
                'transportPackBarcode' => $csv[11],
                'category' => $csv[12],
                'rootCategory' => $csv[13],
            ];

            $dataAttributes = [
                // внутри столько массивов, сколько атрибутов у продукта
                [
                    'name' => 'inStockMSK',
                    'type' => 'text',
                    'label' => $labels['inStockMSK'],
                    'value' => $data['inStockMSK'],
                ],
                [
                    'name' => 'inStockSPB',
                    'type' => 'text',
                    'label' => $labels['inStockSPB'],
                    'value' => $data['inStockSPB'],
                ],
                [
                    'name' => 'boxAmount',
                    'type' => 'text',
                    'label' => $labels['boxAmount'],
                    'value' => $data['boxAmount'],
                ],
                [
                    'name' => 'transportPackBarcode',
                    'type' => 'text',
                    'label' => $labels['transportPackBarcode'],
                    'value' => $data['transportPackBarcode'],
                ],
                [
                    'name' => 'consumerPackBarcode',
                    'type' => 'text',
                    'label' => $labels['consumerPackBarcode'],
                    'value' => $data['consumerPackBarcode'],
                ],
                [
                    'name' => 'code',
                    'type' => 'text',
                    'label' => $labels['code'],
                    'value' => $data['code'],
                ],
                [
                    'name' => 'vendorCode',
                    'type' => 'text',
                    'label' => $labels['vendorCode'],
                    'value' => $data['vendorCode'],
                ],
                [
                    'name' => 'isNew',
                    'type' => 'text',
                    'label' => $labels['isNew'],
                    'value' => $data['isNew'],
                ]
            ];  

            // Создаём продукт, если ещё не создан с таким slug
            $slug = strtolower($data['vendorCode']);

            $product = $this->em->getRepository(Product::class)
                    ->findOneBy([
                        'slug' => $slug,
                    ]);

            if (empty($product)) {
                $product = new Product();
            }

            $product->setSlug($slug);
            $product->setName($this->namingRules[$data['category']] . ' ' . $data['vendorCode']);
            $product->setDescription($data['discription']); // nl2br()


            // Цена для предоплаты (3 уровень, самая низкая цена)
            $prepaymentPrice = new ProductPrice();
            $prepaymentPrice->setValue((float) str_replace(',', '.', $data['GLK СпецЦена (предоплата)']));
            $prepaymentPrice->setLevel($this->priceLevel['prepayment']);
            $prepaymentPrice->setProduct($product);

            // Цена предоплата + доставка (2 уровень, средняя цена)
            $prepaymentDeliveryPrice = new ProductPrice();
            $prepaymentDeliveryPrice->setValue((float) str_replace(',', '.', $data['GLK Дилер(предопл+достав)']));
            $prepaymentDeliveryPrice->setLevel($this->priceLevel['prepayment_delivery']);
            $prepaymentDeliveryPrice->setProduct($product);

            // Цена для отсрочки платежа (1 уровень, самая высокая цена)
            $postponementPaymentPrice = new ProductPrice();
            $postponementPaymentPrice->setValue((float) str_replace(',', '.', $data['GLK Дилер (отсрочка)']));
            $postponementPaymentPrice->setLevel($this->priceLevel['postponement_payment']);
            $postponementPaymentPrice->setProduct($product);

            $this->em->persist($product);
            $this->em->persist($prepaymentPrice);
            $this->em->persist($prepaymentDeliveryPrice);
            $this->em->persist($postponementPaymentPrice);

            // Создаём путь к фото товара
            $productPhoto = $this->em->getRepository(ProductPhoto::class)
                    ->findOneBy([
                        'product' => $product,
                    ]);

            // Если файла не существует, то объекты File и ProductPhoto не будут созданы
            if (file_exists($basePath . '/web/images/products/' . $data['vendorCode'] . '.jpg')) {
                if (empty($productPhoto)) {
                    $productPhoto = new ProductPhoto();
                }

                $productPhoto->setProduct($product);
                $productPhoto->setHeight(75);
                $productPhoto->setWidth(95);

                $file = $this->em->getRepository(File::class)
                    ->findOneBy([
                        'name' => $data['vendorCode'] . '.jpg',
                    ]);

                if (empty($file)) {
                    $file = new File();
                }

                $file->setName($data['vendorCode'] . '.jpg');
                $file->setPath('/images/products/' . $file->getName());

                $baseFilePath = $basePath . '/web' . $file->getPath();
                $file->setMimeType((new finfo())->file($baseFilePath, FILEINFO_MIME_TYPE));
                $file->setSize(filesize($baseFilePath));

                $this->em->persist($file);

                $productPhoto->setFile($file);
                $this->em->persist($productPhoto);
            }

            // Создаём категорию товара, если такой ещё нет
            $category = $this->em->getRepository(Category::class)
                    ->findOneBy([
                        'slug' => $this->categoryNamingRules[$data['category']],
                    ]);

            if (empty($category)) {
                $category = new Category();
            }

            $category->setName($data['category']);
            $category->setSlug($this->categoryNamingRules[$data['category']]);
            $category->setIsRoot(false);

            $rootCategory = $this->em->getRepository(Category::class)
                ->findOneBy([
                    'slug' => $this->categoryNamingRules[$data['rootCategory']],
                ]);
            // Создаём родительские категории для новосозданной
            if (empty($rootCategory)) { 
                $rootCategory = new Category();
            }

            $rootCategory->setName($data['rootCategory']);
            $rootCategory->setSlug($this->categoryNamingRules[$data['rootCategory']]);
            $rootCategory->setIsRoot(true);

            $this->em->persist($rootCategory);

            $categoryRelation = $this->em->getRepository(CategoryRelation::class)
                ->findOneBy([
                    'child' => $category,
                    'parent' => $rootCategory,
                ]);

            if (empty($categoryRelation)) { 
                $categoryRelation = new CategoryRelation();
            }

            $categoryRelation->setChild($category);
            $categoryRelation->setParent($rootCategory);
            $categoryRelation->setSequence(0);

            $this->em->persist($categoryRelation);
            $this->em->persist($category);

            // Устанавливаем связь с категорией товара, если ещё не установлена
            $categoryProduct = $this->em->getRepository(CategoryProduct::class)
                    ->findOneBy([
                        'category' => $category,
                        'product' => $product,
                    ]);

            if (empty($categoryProduct)) {
                $categoryProduct = new CategoryProduct();
            }

            $categoryProduct->setCategory($category);
            $categoryProduct->setProduct($product);
            $categoryProduct->setSequence(66);

            $this->em->persist($categoryProduct);

            // Добавляем атрибуты товара (количество может меняться)
            foreach ($dataAttributes as $data) {
                $type = $this->em->getRepository(FieldType::class)
                    ->findOneBy([
                        'name' => $data['name'],
                        'category' => $category,
                    ]);

                if (empty($type)) {
                    $type = new FieldType();
                }

                $type->setName($data['name']);
                $type->setType($data['type']);
                $type->setLabel($data['label']);
                $type->setCategory($category);

                $value = $this->em->getRepository(FieldValue::class)
                    ->findOneBy([
                        'fieldType' => $type,
                        'product' => $product,
                    ]);

                if (empty($value)) {
                    $value = new FieldValue();
                }

                // Значение свойства обновляем даже если свойство существует
                $value->setValue($data['value']);
                $value->setProduct($product);
                $value->setFieldType($type);

                $this->em->persist($value);
                $this->em->persist($type);
            }

            // Сохраняем всё в БД
            $this->em->flush(); 
        }
        
        fclose($handle);
    }
}
