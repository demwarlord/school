<?php

$news = [
    [
        'title' => 'В Украине создадут онлайн-карту',
        'content' => '
            <p>Кабинет министров планирует создать электронную карту, на которой в режиме онлайн будет отображаться 
            реальная ситуация с наличием интернет-покрытия в каждом населенном пункте Украины.</p>
            <p>Об этом сообщил министр цифровой трансформации Михаил Федоров на своей странице в Facebook.</p>
            <p>"Одна из целей министерства – в течение 3-х лет все без исключения населенные пункты и их социальные 
            объекты обеспечить высокоскоростным доступом к интернету. Уверен, что это сильно повлияет на 
            темпы развития государства и бизнеса", - написал Федоров.</p>
        ',
        'image' => 'https://images.unian.net/photos/2016_05/1464695115-4524.jpg',
    ],
    [
        'title' => 'Рейтинг Айфона',
        'content' => '
            <p>Швейцарец сможет накопить необходимую сумму на iPhone 11 за 4,8 дня, американец – за 5,8 дня, 
           a люксембуржец – за 6,7 дня.</p>
            <p>Украинцу, чтобы купить iPhone 11, необходимо работать 97 дней. Это на 40 дней меньше, чем год назад.</p>
            <p>Об этом говорят данные исследования портала</p>
        ',
        'image' => 'https://images.unian.net/photos/2019_09/1568153473-4194.JPG',
    ],
    [
        'title' => 'Xiaomi Mi Mix Alpha – это смартфон',
        'content' => '
            <p>Компания Xiaomi представила один из самых оригинальных смартфонов 2019 года, который получил название 
            Mi Mix Alpha. Девайс не только получил дисплей, который занимает 180,6% площади корпуса, но и стал первым 
            в мире смартфоном с 108 Мп камерой.</p>
            <p>Xiaomi Mi Mix Alpha – это смартфон, практически весь корпус которого занимает дисплей, за исключением 
            небольшой полосы для камеры на тыльной стороне. Экран полностью лишен вырезов и отверстий, и заменяет 
            динамик за счет проводимости звука</p>
        ',
        'image' => 'https://images.unian.net/photos/2019_09/1569327623-5435.JPG',
    ],
    [
        'title' => 'В Китае презентовали новый камерофон от Realme',
        'content' => '
            <p>Не прошло и двух недель с момента официальной премьеры 64-мегапиксельного смартфона Realme XT в Индии, 
            и вот сегодня суббренд Realme китайской компании Oppo представил в Китае еще одну модель с 64-мегапиксельной 
            камерой - Realme X2.</p>
            <p>"По сути, Realme X2 - это слегка улучшенная версия смартфона Realme XT. Внешне эти модели полностью 
            идентичны, а отличия скрываются внутри, причем этих самых отличий всего четыре. Первое - более 
            производительная платформа. Если Realme XT построен на платформе Qualcomm Snapdragon 712, то Realme X2 
            досталась однокристальная система чуть помощнее - игровая Snapdragon 730G", - говорится в сообщении.</p>
        ',
        'image' => 'https://images.unian.net/photos/2019_09/1569327040-8455.jpg',
    ],
];


// Add news
foreach ($news as $item) {
    /** @var Demka_News_Model_News $modelNews */
    $modelNews = Mage::getModel('demkanews/news');
    $item['priority'] = mt_rand(0, 10);
    $item['created'] = date("Y-m-d H:i:s", mt_rand(1561902164, 1569850964));
    $item['content'] = trim($item['content']);
    $modelNews->addData($item);
    $modelNews->save();
}

$modelNews = Mage::getModel('demkanews/news');
$collectionNews = $modelNews->getCollection();
$collectionNewsData = $collectionNews->getData();
$newsIDs = array_column($collectionNewsData, 'id');

try {
    $currentStore = Mage::app()->getStore()->getId();
    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

    // Add new category
    $parentId = '2';
    $category = Mage::getModel('catalog/category');
    $category->setName('school products');
    $category->setUrlKey('schoolpro');
    $category->setIsActive(1);
    $category->setDisplayMode('PRODUCTS');
    $category->setIsAnchor(1); //active anchor
    $category->setStoreId(Mage::app()->getStore()->getId());
    $parentCategory = Mage::getModel('catalog/category')->load($parentId);
    $category->setPath($parentCategory->getPath());
    $category->save();
    $idCat = $category->getId();

    // Add 10 products with random data
    for ($i = 0; $i < 10; $i++) {
        $item = [];
        $item['name'] = "Product-" . $i;

        $rnd = mt_rand(0, count($news));
        $randomNews = $rnd !== 0 ? array_rand($newsIDs, $rnd) : [];
        $randomNews = is_array($randomNews) ? $randomNews : [$randomNews];
        $tsgNews = !empty($randomNews) ? array_values(array_intersect_key($newsIDs, array_flip($randomNews))) : [];
        $tsgNewsMain = !empty($tsgNews) ? $tsgNews[mt_rand(0, count($tsgNews) - 1)] : 0;

        if (!empty($tsgNews)) {
            $item['tsg_news'] = $tsgNews;
            $item['tsg_main_news'] = (int)$tsgNewsMain;
        }

        /** @var Mage_Catalog_Model_Product $modelProduct */
        $modelProduct = Mage::getModel('catalog/product');
        $modelProduct->setStoreId(0)->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE);

        $modelProduct
            ->setAttributeSetId($modelProduct->getDefaultAttributeSetId())
            ->setWebsiteIds([1])
            ->setWeight(mt_rand(10, 50))
            ->setStatus(1)
            ->setSku("PRO-" . mt_rand(100000, 999999))
            ->setTaxClassId(2)
            ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->setPrice(mt_rand(1000, 50000))
            ->setMetaTitle('test meta title for ' . $item['name'])
            ->setMetaKeyword('test meta keyword for ' . $item['name'])
            ->setMetaDescription('test meta description for ' . $item['name'])
            ->setDescription('This is a long description for ' . $item['name'])
            ->setShortDescription('This is a short description for ' . $item['name'])
            ->setStockData(array(
                    'manage_stock' => 1,
                    'is_in_stock' => 1,
                    'qty' => mt_rand(5, 50)
                )
            )
            ->setCategoryIds([$idCat]);

        $modelProduct->addData($item);
        Mage::log(print_r($modelProduct->getData(), true));
        $modelProduct->save();
    }

} catch (Exception $e) {
    echo Mage::log($e->getMessage());
}