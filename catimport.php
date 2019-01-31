<?php
error_reporting(-1);
ini_set('display_errors', 'On');
require_once 'app/Mage.php';
Mage::app("admin");

//  $categoryCollection = Mage::getModel('catalog/category')->getCollection()->addFieldToFilter('level',  array('gteq' => 2));
// foreach($categoryCollection as $category) {
//    if ($category->getProductCount() === 0) {
//        print_r ($category->entity_id);
//        echo "<br><hr><br>";
//        $category->delete();
//     }
// }

die();



function getProducts()
{
    $file = 'products.csv';
    $arrResult = array();
    $headers = false;
    $handle = fopen($file, "r");
    //die('sd');
    if (empty($handle) === false) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        	//print($data);
            if (!$headers) {
                $headers[] = $data;
            } else {
                $arrResult[] = $data;
            }
        }
        fclose($handle);
    }
    return $arrResult;
}

echo"<pre>";
$categoryList = getProducts();
//print_r($categoryList);
//die();

foreach ($categoryList as $key => $value) {
	if($value[0] !== 2) {
	    //createCategory(2, $value[1]);
		createCategory(getcategoryid($value[0]), $value[1]);
	}
	else {
		
		//createCategory(2, $value[1]);
	}
}



function createCategory($parentId, $cateName)
{


if($parentId < 2) return;
echo $parentId."-".$cateName."<br>";
//return;

	// $parentId=2;
	try{
	    $category = Mage::getModel('catalog/category');
	    $category->setName($cateName);
	    //$category->setUrlKey('new-category');
	    $category->setIsActive(1);
	    $category->setDisplayMode(Mage_Catalog_Model_Category::DM_PRODUCT);
	    $category->setIsAnchor(1); //for active achor
	    $category->setStoreId(Mage::app()->getStore()->getId());
	    $parentCategory = Mage::getModel('catalog/category')->load($parentId);
	    $category->setPath($parentCategory->getPath());
	    $category->save();
	    echo "sdf";
	} catch(Exception $e) {
	    var_dump($e);
	}
}

function getcategoryid($cateName)
{

$category = Mage::getResourceModel('catalog/category_collection')
    ->addFieldToFilter('name', $cateName)
    ->getFirstItem(); // The parent category

return $categoryId = $category->getId();
}




?>