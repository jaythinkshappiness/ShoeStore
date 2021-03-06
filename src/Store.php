<?php
class Store
    {
        private $name;
        private $id;

        function __construct($name, $id = null)
        {
            $this->name = $name;
            $this->id = $id;
        }
        function getName()
        {
            return $this->name;
        }

        function setName($new_name)
        {
            $this->name = (string) $new_name;
        }
        function getId()
        {
            return $this->id;
        }
        function save()
        {
            $GLOBALS['DB']->exec("INSERT INTO stores (name) VALUES ('{$this->getName()}');");
            $this->id = $GLOBALS['DB']->lastInsertId();
        }
        static function getAll()
        {
            $returned_stores = $GLOBALS['DB']->query("SELECT * FROM stores;");
            $stores = [];
            foreach($returned_stores as $store) {
               $name = $store['name'];
               $id = $store['id'];
               $new_Store = new Store($name, $id);
               array_push($stores, $new_Store);
            }
            return $stores;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM stores;");
        }

        function update($new_name)
       {
           $GLOBALS['DB']->exec("UPDATE stores SET name = '{$new_name}' WHERE id = {$this->getId()};");
           $this->setName($new_name);
       }

       function delete()
       {
           $GLOBALS['DB']->exec("DELETE FROM stores WHERE id = {$this->getId()};");
           $GLOBALS['DB']->exec("DELETE FROM stores_brands WHERE store_id = {$this->getId()};");
       }
       static function find($search_id)
       {
           $foundstore = null;
           $stores = Store::getAll();
           foreach($stores as $stores) {
               $stores_id = $stores->getId();
               if ($stores_id == $search_id) {
                   $foundstore = $stores;
               }
           }
           return $foundstore;
       }

       function addBrand($input)
       {
           $GLOBALS['DB']->exec("INSERT INTO stores_brands (store_id, brand_id) VALUES ({$this->getId()}, {$input->getBrandId()});");
       }
       function getBrands()
       {
           $returned_brands = $GLOBALS['DB']->query("SELECT brands.* FROM stores
               JOIN stores_brands ON (stores_brands.store_id = stores.id)
               JOIN brands ON (brands.id = stores_brands.brand_id)
               WHERE stores.id = {$this->getId()};");


           $brands = [];

           foreach($returned_brands as $brand) {
               $name = $brand['name'];
               $id = $brand['id'];
               $new_brand = new Brand($name,$id);
               array_push($brands,$new_brand);

           }
           return $brands;
       }

    }
?>
