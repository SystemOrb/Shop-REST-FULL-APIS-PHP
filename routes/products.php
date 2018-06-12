<?php 
/*
 * Backend que se encarga de construir los productos desde la empresa
 * Carlos Estarita
 * API RESTFUL 
 */
require_once '../models/config.php';
require_once '../models/connection.php';
require_once '../models/imagenValidacion.php';
if($_GET) {
    // Evaluamos la URL para saber el tipo de petición
    $productRoute = new products();
    switch($_GET['operationType']) {
        case 'select':
            echo $productRoute->selectProducts($_GET['user_id']);         
            break;
     /**************************************************************
     *  INSERTS
     *************************************************************/
        case 'insert':
            echo $productRoute->insertProduct($_GET['user_id']);
            break;      
        case 'insertDescription':
            echo $productRoute->insertDescription();
            break;
        case 'insertDiscount':
            echo $productRoute->insertDiscount();
            break;
        case 'insertFilter':
            echo $productRoute->insertProductFilter();
            break;
        case 'insertImage':
            echo $productRoute->insertImage();
            break;
        case 'insertSpecial':
            echo $productRoute->insertSpecial();
            break;
        case 'insertCategory':
            echo $productRoute->insertCategory();
            break;
    /**************************************************************
     *  End INSERTS
     *************************************************************/
     /**************************************************************
     *  DELETES
     *************************************************************/
            
     /**************************************************************
     *  UPDATES
     *************************************************************/
        case 'update':
            echo $productRoute->updateProduct($_GET['product_id'], $_GET['user_id']);
            break;
        case 'updateDescription':
            echo $productRoute->updateProductDescription($_GET['product_id'], $_GET['user_id']);
            break;
        case 'updateDiscount':
            echo $productRoute->updateDiscount($_GET['product_id']);
            break;
        case 'updateFilter':
            echo $productRoute->updateFilter();
            break;
        case 'updateImages':
            echo $productRoute->updateImages();
            break;
        case 'updateImage':
            echo $productRoute->updateImage();
            break;
        case 'updateSpecial':
            echo $productRoute->updateSpecial();
            break;
        case 'updateProductCategory':
            echo $productRoute->updateProductCategory();
            break;
        case 'insertFirstImage':
            echo $productRoute->updateImage();
            break;
     /**************************************************************
     *  END UPDATE
     *************************************************************/
     /**************************************************************
     *  DELETE
     *************************************************************/
        case 'deleteProduct':
            echo $productRoute->deleteProduct();
            break;
        case 'deleteSpecial':
            echo $productRoute->deleteSpecial();
            break;
        case 'deleteDiscount':
            echo $productRoute->deleteDiscount();
            break;
     /**************************************************************
     *  END DELETE
     *************************************************************/
        case 'selectOne':
            return $productRoute->getProductById($_GET['product_id'],$_GET['user_id']);
            break;
        default:
            return json_encode('Operación no permitida');
    }
}
class products {
     public function __construct() {
        $this->BBDD = new \dbDriver;
        $this->driver = $this->BBDD->setPDO();
        $this->BBDD->setPDOConfig($this->driver);
    }
    public function selectProducts($user_id) {
        // Obtener todos los productos de la bbdd asociados a la empresa
        try {
            $objectProducts = $this->BBDD->selectDriver('user_id = ?', PREFIX."product", $this->driver);
            $this->BBDD->runDriver(array(
                $this->BBDD->scapeCharts($user_id)
            ), $objectProducts);
            if ($this->BBDD->verifyDriver($objectProducts)) {
                return json_encode($this->BBDD->fetchDriver($objectProducts));
            } else {
                return json_encode('null');
            }
        } catch (PDOException $ex) {
            return json_encode('Fallo en la conexión con la base de datos' . $ex->getMessage() . ' ' . $ex->getFile() . ' ' . $ex->getLine());
        }
    }
    /**************************************************************
     * INSERT MODE
     *************************************************************/
    /*
     * Función para insertar la data padre de un producto en la tienda 
     * para hacer la relación
     */
    public function insertProduct($user_id) {
        try {
            $sql = '?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?';
            $fields = '
                        user_id,model,sku,upc,ean,jan,isbn,mpn,
                        location,quantity,stock_status_id,image,
                        manufacturer_id, shipping,price,points,
                        tax_class_id,date_available,weight,weight_class_id,
                        length,width,height,length_class_id,subtract,minimum,
                        sort_order,status,viewed,date_added,date_modified
                      ';
            $objectProduct = $this->BBDD->insertDriver($sql,PREFIX."product", $this->driver, $fields);
            $this->BBDD->runDriver(array(
                $this->BBDD->scapeCharts($_REQUEST['user_id']),
                $this->BBDD->scapeCharts($_REQUEST['model']),
                $this->BBDD->scapeCharts($_REQUEST['sku']),
                $this->BBDD->scapeCharts($_REQUEST['upc']),
                $this->BBDD->scapeCharts($_REQUEST['ean']),
                $this->BBDD->scapeCharts($_REQUEST['jan']),
                $this->BBDD->scapeCharts($_REQUEST['isbn']),
                $this->BBDD->scapeCharts($_REQUEST['mpn']),
                $this->BBDD->scapeCharts($_REQUEST['location']),
                $this->BBDD->scapeCharts($_REQUEST['quantity']),
                $this->BBDD->scapeCharts($_REQUEST['stock_status_id']),
                $this->BBDD->scapeCharts($_REQUEST['image']),
                $this->BBDD->scapeCharts($_REQUEST['manufacturer_id']),
                $this->BBDD->scapeCharts($_REQUEST['shipping']),
                $this->BBDD->scapeCharts($_REQUEST['price']),
                $this->BBDD->scapeCharts($_REQUEST['points']),
                $this->BBDD->scapeCharts($_REQUEST['tax_class_id']),
                $this->BBDD->scapeCharts($_REQUEST['date_available']),
                $this->BBDD->scapeCharts($_REQUEST['weight']),
                $this->BBDD->scapeCharts($_REQUEST['weight_class_id']),
                $this->BBDD->scapeCharts($_REQUEST['length']),
                $this->BBDD->scapeCharts($_REQUEST['width']),
                $this->BBDD->scapeCharts($_REQUEST['height']),
                $this->BBDD->scapeCharts($_REQUEST['length_class_id']),
                $this->BBDD->scapeCharts($_REQUEST['subtract']),
                $this->BBDD->scapeCharts($_REQUEST['minimum']),
                $this->BBDD->scapeCharts($_REQUEST['sort_order']),
                $this->BBDD->scapeCharts($_REQUEST['status']),
                $this->BBDD->scapeCharts($_REQUEST['viewed']),
                $this->BBDD->scapeCharts($_REQUEST['date_added']),
                $this->BBDD->scapeCharts($_REQUEST['date_modified']),
            ), $objectProduct);
            return json_encode(print_r($_POST));
        } catch (PDOException $ex) {
            return json_encode('Fallo en la conexión con la base de datos' . $ex->getMessage() . ' ' . $ex->getFile() . ' ' . $ex->getLine());
        }        
    }
    /*
     * Función para insertar los datos de lectura en la tienda
     */
    public function insertDescription() {
         try {
        $sql = '?,?,?,?,?,?,?,?';
        $fields = 'product_id,language_id,name,
                   description,tag,meta_title,
                   meta_description,meta_keyword
                   ';
        $objectProductDescription = $this->BBDD->insertDriver($sql,PREFIX.'product_description', $this->driver, $fields);  
        $this->BBDD->runDriver(array(
            $this->BBDD->scapeCharts($_REQUEST['product_id']),
            $this->BBDD->scapeCharts($_REQUEST['language_id']),
            $this->BBDD->scapeCharts($_REQUEST['name']),
            $this->BBDD->scapeCharts($_REQUEST['description']),
            $this->BBDD->scapeCharts($_REQUEST['tag']),
            $this->BBDD->scapeCharts($_REQUEST['meta_title']),
            $this->BBDD->scapeCharts($_REQUEST['meta_description']),
            $this->BBDD->scapeCharts($_REQUEST['meta_keyword']),
        ), $objectProductDescription);
        return json_encode($_POST);
        } catch (PDOException $ex) {
            return json_encode('Fallo en la conexión con la base de datos' . $ex->getMessage() . ' ' . $ex->getFile() . ' ' . $ex->getLine());
        }                   
    }
    // Descuentos de un producto asociado
    public function insertDiscount() {
        $sql = '?,?,?,?,?,?,?,?';
        $fields = 'product_discount_id,product_id,
            customer_group_id, quantity, priority,
            price, date_start, date_end
            ';
        $objectProductDiscount = $this->BBDD->insertDriver($sql,PREFIX.'product_discount', $this->driver, $fields);
        $this->BBDD->runDriver(array(
            $this->BBDD->scapeCharts($_REQUEST['product_discount_id']),
            $this->BBDD->scapeCharts($_REQUEST['product_id']),
            $this->BBDD->scapeCharts($_REQUEST['customer_group_id']),
            $this->BBDD->scapeCharts($_REQUEST['quantity']),
            $this->BBDD->scapeCharts($_REQUEST['priority']),
            $this->BBDD->scapeCharts($_REQUEST['price']),
            $this->BBDD->scapeCharts($_REQUEST['date_start']),
            $this->BBDD->scapeCharts($_REQUEST['date_end'])
        ), $objectProductDiscount);
        return json_encode(print_r($_POST));
    }
    // Insertar filtros de busqueda a un producto
    public function insertProductFilter() {
        try {
            $sql = '?,?';
            $fields = 'product_id, filter_id';
            $ObjectProductFilter = $this->BBDD->insertDriver($sql,PREFIX.'product_filter', $this->driver, $fields);
            $this->BBDD->runDriver(array(
               $this->BBDD->scapeCharts($_POST['product_id']),
               $this->BBDD->scapeCharts($_POST['filter_id']),
            ), $ObjectProductFilter);
            return json_encode(print_r($_POST));
        } catch (Exception $ex) {
            return json_encode('Fallo en la conexión con la base de datos' . $ex->getMessage() . ' ' . $ex->getFile() . ' ' . $ex->getLine());
        }
    }
    /*
     * Función que se encarga de traer un parametro de tipo File
     * validar formato, tipo y transformar el path 
     */
    public function insertImage() {
        $fileImage = new validacionImagen();
        if($fileImage->_getValidate_($_FILES['image'])) {
            $file = $this->loadImage($_FILES['image']);
            if ($file!=null) {
                // Si pasa toda la validación de la imagen entonces publicamos en la bbdd
                try {
                    $sql = '?,?,?,?';
                    $fields = 'product_image_id,product_id,
                               image, sort_order
                               ';
                    $objectProductImage = $this->BBDD->insertDriver($sql,PREFIX.'product_image', $this->driver, $fields);
                    $this->BBDD->runDriver(array(
                        $this->BBDD->scapeCharts($_POST['product_image_id']),
                        $this->BBDD->scapeCharts($_POST['product_id']),
                        $this->BBDD->scapeCharts(str_replace('../../image/', '', $file)),
                        $this->BBDD->scapeCharts($_POST['sort_order']),
                    ), $objectProductImage);
                    return json_encode(print_r($_POST));
                } catch (Exception $ex) {
                    return json_encode('Fallo en la conexión con la base de datos' . $ex->getMessage() . ' ' . $ex->getFile() . ' ' . $ex->getLine());
                }
            }else {
                return json_encode('El peso de la imagen o el formato no es correcto');
            }
        } else {
            return json_encode('Debes cargar un archivo');
        }
    }
    // Añadir un descuento a un unico producto solicitado
    public function insertSpecial() {
        try {
            $sql = '?,?,?,?,?,?';
            $fields = 'product_id,customer_group_id,
                       priority,price,date_start,date_end
                       ';
            $objectProductSpecial = $this->BBDD->insertDriver($sql,PREFIX.'product_special', $this->driver, $fields);
            $this->BBDD->runDriver(array(
                $this->BBDD->scapeCharts($_POST['product_id']),
                $this->BBDD->scapeCharts($_POST['customer_group_id']),
                $this->BBDD->scapeCharts($_POST['priority']),
                $this->BBDD->scapeCharts($_POST['price']),
                $this->BBDD->scapeCharts($_POST['date_start']),
                $this->BBDD->scapeCharts($_POST['date_end']),
            ), $objectProductSpecial);
            return json_encode(print_r($_POST));
        } catch (PDOException $ex) {
            return json_encode('Fallo en la conexión con la base de datos' . $ex->getMessage() . ' ' . $ex->getFile() . ' ' . $ex->getLine());
        }
    }
    // Insertar las categorías de los productos
    public function insertCategory() {
        try{
            $sql = '?,?';
            $fields = 'product_id, category_id';
            $objectProductCategory = $this->BBDD->insertDriver($sql, PREFIX.'product_to_category', $this->driver, $fields);
            $this->BBDD->runDriver(array(
                $this->BBDD->scapeCharts($_POST['product_id']),
                $this->BBDD->scapeCharts($_POST['category_id'])
            ), $objectProductCategory);
            return json_encode(print_r($_POST));
        } catch (PDOException $ex) {
            return json_encode('Fallo en la conexión con la base de datos' . $ex->getMessage() . ' ' . $ex->getFile() . ' ' . $ex->getLine());
        }
    }
     /**************************************************************
     *  END INSERT MODE
     *************************************************************/
     /**************************************************************
     *  UPDATE MODE
     *************************************************************/
        public function updateProduct($product_id,$user_id) {
         try {
             $fields = 'model = ?, sku = ?, upc=?,
                        ean=?,jan=?,isbn=?,mpn=?,
                        location=?,quantity=?,
                        stock_status_id=?,
                        manufacturer_id=?,shipping=?,
                        price=?,points=?,tax_class_id=?,
                        date_available=?,weight=?,
                        weight_class_id=?,length=?,
                        width=?,height=?,length_class_id=?,
                        subtract=?,minimum=?,sort_order=?,
                        status=?,date_added=?,date_modified=?';
             $objectProductUpdate = $this->BBDD->updateDriver('user_id = ? && product_id = ?',PREFIX.'product', $this->driver, $fields);
             $this->BBDD->runDriver(array(
                 $this->BBDD->scapeCharts($_POST['model']),
                 $this->BBDD->scapeCharts($_POST['sku']),
                 $this->BBDD->scapeCharts($_POST['upc']),
                 $this->BBDD->scapeCharts($_POST['ean']),
                 $this->BBDD->scapeCharts($_POST['jan']),
                 $this->BBDD->scapeCharts($_POST['isbn']),
                 $this->BBDD->scapeCharts($_POST['mpn']),
                 $this->BBDD->scapeCharts($_POST['location']),
                 $this->BBDD->scapeCharts($_POST['quantity']),
                 $this->BBDD->scapeCharts($_POST['stock_status_id']),
                 //$this->BBDD->scapeCharts($_POST['image']),
                 $this->BBDD->scapeCharts($_POST['manufacturer_id']),
                 $this->BBDD->scapeCharts($_POST['shipping']),
                 $this->BBDD->scapeCharts($_POST['price']),
                 $this->BBDD->scapeCharts($_POST['points']),
                 $this->BBDD->scapeCharts($_POST['tax_class_id']),
                 $this->BBDD->scapeCharts($_POST['date_available']),
                 $this->BBDD->scapeCharts($_POST['weight']),
                 $this->BBDD->scapeCharts($_POST['weight_class_id']),
                 $this->BBDD->scapeCharts($_POST['length']),
                 $this->BBDD->scapeCharts($_POST['width']),
                 $this->BBDD->scapeCharts($_POST['height']),
                 $this->BBDD->scapeCharts($_POST['length_class_id']),
                 $this->BBDD->scapeCharts($_POST['subtract']),
                 $this->BBDD->scapeCharts($_POST['minimum']),
                 $this->BBDD->scapeCharts($_POST['sort_order']),
                 $this->BBDD->scapeCharts($_POST['status']),
                 $this->BBDD->scapeCharts($_POST['date_added']),
                 $this->BBDD->scapeCharts($_POST['date_modified']),
                 $this->BBDD->scapeCharts($user_id),
                 $this->BBDD->scapeCharts($product_id)                
             ), $objectProductUpdate);
             return json_encode(print_r($_POST));
        } catch (PDOException $ex) {
            return json_encode('Fallo en la conexión con la base de datos' . $ex->getMessage() . ' ' . $ex->getFile() . ' ' . $ex->getLine());
        }       
    }
    public function updateProductDescription($product_id,$user_id) {
        $fields = 'language_id = ?, name = ?,
                   description = ?, tag = ?,
                   meta_title = ?, meta_description=?,
                   meta_keyword=?
                   ';
        $objectProductDescription = $this->BBDD->updateDriver('user_id = ? && product_id = ?',PREFIX.'product_description', $this->driver, $fields);
        $this->BBDD->runDriver(array(
            $this->BBDD->scapeCharts($_POST['language_id']),
            $this->BBDD->scapeCharts($_POST['name']),
            $this->BBDD->scapeCharts($_POST['description']),
            $this->BBDD->scapeCharts($_POST['tag']),
            $this->BBDD->scapeCharts($_POST['meta_title']),
            $this->BBDD->scapeCharts($_POST['meta_description']),
            $this->BBDD->scapeCharts($_POST['meta_keyword']),
            $this->BBDD->scapeCharts($user_id),
            $this->BBDD->scapeCharts($product_id)
        ), $objectProductDescription);
        return json_encode(print_r($_POST));
    }
    public function updateDiscount($product_id) {
        $fields = 'customer_group_id = ?,quantity = ?,priority = ?,
            price = ?,date_start = ?,date_end = ?';
    $objectProductDiscount = $this->BBDD->updateDriver('product_discount_id = ?',PREFIX.'product_discount', $this->driver, $fields);
    $this->BBDD->runDriver(array(
        $this->BBDD->scapeCharts($_POST['customer_group_id']),
        $this->BBDD->scapeCharts($_POST['quantity']),
        $this->BBDD->scapeCharts($_POST['priority']),
        $this->BBDD->scapeCharts($_POST['price']),
        $this->BBDD->scapeCharts($_POST['date_start']),
        $this->BBDD->scapeCharts($_POST['date_end']),
        $this->BBDD->scapeCharts($_POST['product_discount_id'])
    ), $objectProductDiscount);
        return json_encode(print_r($_POST));
    }
    public function updateFilter() {
        $fields = 'filter_id = ?';
        $objectProductFilter = $this->BBDD->updateDriver('product_id = ?',PREFIX.'product_filter', $this->driver, $fields);
        $this->BBDD->runDriver(array(
            $this->BBDD->scapeCharts($_POST['filter_id']),
            $this->BBDD->scapeCharts($_POST['product_id'])
        ), $objectProductFilter);
        return json_encode(print_r($_POST));
    }
    public function updateImages() {
        $fileImage = new validacionImagen();
        if($fileImage->_getValidate_($_FILES['image'])) {
            // Movemos la imagen a la carpeta de forma temporal
            $file = $this->loadImage($_FILES['image']);
            if ($file != null) {
                // Borramos la imagen anterior por rendimiento
                $old_path = $this->getPathOld($_POST['product_image_id'], 'product_image_id = ?', 'product_image');
                if($old_path != null) {
                     $delete = unlink("../../image/{$old_path}"); // Borramos la antigua imagen
                        if($delete) {
                            // Actualizamos por la nueva
                          $fields = 'image = ?';
                          $objectProductImages = $this->BBDD->updateDriver('product_image_id = ?',PREFIX.'product_image', $this->driver, $fields);
                          $this->BBDD->runDriver(array(
                              $this->BBDD->scapeCharts(str_replace('../../image/', '', $file)),
                              $this->BBDD->scapeCharts($_POST['product_image_id']),
                          ), $objectProductImages);
                          return json_encode('Imagen actualizada con éxito');
                     }
                } else {
                    return json_encode('No se ha encontrado la imagen');
                }
            } else {
                return json_encode('Formato o tamaño inválido');
            }
        } else {
            return json_encode('Debes cargar una imagen');
        }
    }
        public function updateImage() {
        $fileImage = new validacionImagen();
        if($fileImage->_getValidate_($_FILES['image'])) {
            // Movemos la imagen a la carpeta de forma temporal
            $file = $this->loadImage($_FILES['image']);
            if ($file != null) {
                /*
                 * Verifica si tiene imagen, si tiene pasa al siguiente bloque
                 * sino, actualiza una nueva
                 */
                $productPresentation = $this->getImageData($_POST['product_id']);
                if(!$productPresentation || empty($productPresentation)) {
                          $fields = 'image = ?';
                          
                          $objectProductImages = $this->BBDD->updateDriver('product_id = ?',PREFIX.'product', $this->driver, $fields);
                          $this->BBDD->runDriver(array(
                              $this->BBDD->scapeCharts(str_replace('../../image/', '', $file)),
                              $this->BBDD->scapeCharts($_POST['product_id']),
                          ), $objectProductImages);
                          return json_encode('Imagen actualizada con éxitoo');                  
                } else {
                // Borramos la imagen anterior por rendimiento
                $old_path = $this->getPathOld($_POST['product_id'], 'product_id = ?', 'product');
                if($old_path != null || (!empty($old_path))) {
                   $delete = unlink("../../image/{$old_path}"); // Borramos la antigua imagen
                        if($delete) {
                            // Actualizamos por la nueva
                          $fields = 'image = ?';
                          $objectProductImages = $this->BBDD->updateDriver('product_id = ?',PREFIX.'product_image', $this->driver, $fields);
                          $this->BBDD->runDriver(array(
                              $this->BBDD->scapeCharts(str_replace('../../image/', '', $file)),
                              $this->BBDD->scapeCharts($_POST['product_image_id']),
                          ), $objectProductImages);
                          return json_encode('Imagen actualizada con éxito');
                     }
                } else {
                    return json_encode('No se ha encontrado la imagen');
                }
              }
            } else {
                return json_encode('Formato o tamaño inválido');
            }
        } else {
            return json_encode('Debes cargar una imagen');
        }
    }
    public function updateSpecial() {
        try {
            $fields = 'customer_group_id = ?,priority=?,
                price = ?, date_start = ?, date_end = ?';
            $obbjectProductSpecial = $this->BBDD->updateDriver('product_special_id = ?',PREFIX.'product_special', $this->driver, $fields);
            $this->BBDD->runDriver(array(
                $this->BBDD->scapeCharts($_POST['customer_group_id']),
                $this->BBDD->scapeCharts($_POST['priority']),
                $this->BBDD->scapeCharts($_POST['price']),
                $this->BBDD->scapeCharts($_POST['date_start']),
                $this->BBDD->scapeCharts($_POST['date_end']),
                $this->BBDD->scapeCharts($_POST['product_special_id'])
            ), $obbjectProductSpecial);
            return json_encode('Descuento actualizado');
        } catch (PDOException $ex) {
            return json_encode('Fallo en la conexión con la base de datos' . $ex->getMessage() . ' ' . $ex->getFile() . ' ' . $ex->getLine());
        }
    }
    public function updateProductCategory() {
        $fields = 'category_id = ?';
        $objectProductCategory = $this->BBDD->updateDriver('product_id = ?', PREFIX.'product_to_category', $this->driver, $fields);
        $this->BBDD->runDriver(array(
            $this->BBDD->scapeCharts($_POST['category_id']),
            $this->BBDD->scapeCharts($_POST['product_id'])
        ), $objectProductCategory);
        return json_encode('categoria actualizada');
    }
    /**************************************************************
     * END  UPDATE MODE
     *************************************************************/
     /**************************************************************
     * DELETE MODE
     *************************************************************/
    public function deleteProduct() {
        /*
         * Cómo es un producto general (Universal) Borramos toda la data que trae con el
         */
         $description = $this->deleteDescription($_POST['product_id']);
         $images = $this->deleteImages($_POST['product_id']);
         $filters = $this->deleteProductFilters($_POST['product_id']);
         $discounts = $this->deleteProductDiscounts($_POST['product_id']);
         $specials = $this->deleteProductoSpecial($_POST['product_id']);
         if($description && $images && $filters && $discounts && $specials) {
             // Entonces borramos el articulo
             try {
                 $objectProductDelete = $this->BBDD->deleteDriver('product_id = ?', PREFIX.'product', $this->driver);
                 $this->BBDD->runDriver(array(
                     $this->BBDD->scapeCharts($_POST['product_id'])
                 ), $objectProductDelete);
                 $response = array();
                 $response['status'] = true;
                 $response['message'] = 'Eliminado con éxito';
                 return json_encode($response);
             } catch (PDOException $ex) {
                 return json_encode('Fallo en la conexión con la base de datos' . $ex->getMessage() . ' ' . $ex->getFile() . ' ' . $ex->getLine());
             }
         }
    }
    public function deleteDescription($product_id) {
     try {
         $objectProductDelete = $this->BBDD->deleteDriver('product_id = ?', PREFIX.'product_description', $this->driver);
         $this->BBDD->runDriver(array(
             $this->BBDD->scapeCharts($product_id)
         ), $objectProductDelete);
         return true;
     } catch (PDOException $ex) {
         return json_encode('Fallo en la conexión con la base de datos' . $ex->getMessage() . ' ' . $ex->getFile() . ' ' . $ex->getLine());
     }        
    }
    public function deleteImages($product_id) {
             try {
         $objectProductDelete = $this->BBDD->deleteDriver('product_id = ?', PREFIX.'product_images', $this->driver);
         $this->BBDD->runDriver(array(
             $this->BBDD->scapeCharts($product_id)
         ), $objectProductDelete);
         return true;
     } catch (PDOException $ex) {
         return json_encode('Fallo en la conexión con la base de datos' . $ex->getMessage() . ' ' . $ex->getFile() . ' ' . $ex->getLine());
     }  
    }
    public function deleteProductFilters($product_id) {
      try {
         $objectProductDelete = $this->BBDD->deleteDriver('product_id = ?', PREFIX.'product_filter', $this->driver);
         $this->BBDD->runDriver(array(
             $this->BBDD->scapeCharts($product_id)
         ), $objectProductDelete);
         return true;
     } catch (PDOException $ex) {
         return json_encode('Fallo en la conexión con la base de datos' . $ex->getMessage() . ' ' . $ex->getFile() . ' ' . $ex->getLine());
     }         
    }
    public function deleteProductDiscounts($product_id) {
     try {
         $objectProductDelete = $this->BBDD->deleteDriver('product_id = ?', PREFIX.'product_discount', $this->driver);
         $this->BBDD->runDriver(array(
             $this->BBDD->scapeCharts($product_id)
         ), $objectProductDelete);
         return true;
     } catch (PDOException $ex) {
         return json_encode('Fallo en la conexión con la base de datos' . $ex->getMessage() . ' ' . $ex->getFile() . ' ' . $ex->getLine());
     }          
    }
    public function deleteProductoSpecial($product_id) {
     try {
         $objectProductDelete = $this->BBDD->deleteDriver('product_id = ?', PREFIX.'product_special', $this->driver);
         $this->BBDD->runDriver(array(
             $this->BBDD->scapeCharts($product_id)
         ), $objectProductDelete);
         return true;
     } catch (PDOException $ex) {
         return json_encode('Fallo en la conexión con la base de datos' . $ex->getMessage() . ' ' . $ex->getFile() . ' ' . $ex->getLine());
     }          
    }
    public function deleteSpecial() {
             try {
         $objectProductDelete = $this->BBDD->deleteDriver('product_special_id = ?', PREFIX.'product_special', $this->driver);
         $this->BBDD->runDriver(array(
             $this->BBDD->scapeCharts($_POST['product_special_id'])
         ), $objectProductDelete);
         return true;
     } catch (PDOException $ex) {
         return json_encode('Fallo en la conexión con la base de datos' . $ex->getMessage() . ' ' . $ex->getFile() . ' ' . $ex->getLine());
     } 
    }
    public function deleteDiscount() {
        try {
         $objectProductDelete = $this->BBDD->deleteDriver('product_discount_id = ?', PREFIX.'product_discount', $this->driver);
         $this->BBDD->runDriver(array(
             $this->BBDD->scapeCharts($_POST['product_discount_id'])
         ), $objectProductDelete);
         return true;
     } catch (PDOException $ex) {
         return json_encode('Fallo en la conexión con la base de datos' . $ex->getMessage() . ' ' . $ex->getFile() . ' ' . $ex->getLine());
     } 
    }
     /**************************************************************
     * END DELETE MODE
     *************************************************************/
    public function getProductById($product_id,$user_id) {
        try {
            
        } catch (PDOException $ex) {
            return json_encode('Fallo en la conexión con la base de datos' . $ex->getMessage() . ' ' . $ex->getFile() . ' ' . $ex->getLine());
        }        
    }
    // Image function
        private function loadImage($file){
        $IMG = new validacionImagen();
        $IMG->_setImageName_($file["name"]);
        $IMG->_setImageExtension_($file["type"]);
        $IMG->_setImageSize_($file["size"]);
        $IMG->_setImageTMP_($file["tmp_name"]);
        //MODULE 1
        if(strcmp($IMG->_getImageEXT_(), "image/png")==0
            || (strcmp($IMG->_getImageEXT_(), "image/jpg")==0)
            || (strcmp($IMG->_getImageEXT_(), "image/jpeg")==0)
            || (strcmp($IMG->_getImageEXT_(), "image/gif")==0)){
            //MODULE2
            if($IMG->_getImageSize_()<=(1024*1024)){
                $FOLDER = "../../image/catalog/ropa/"; // path
                $src = $FOLDER.$IMG->_getImageName_();
                move_uploaded_file($IMG->_getImageTMP_(), $src);
                return $src;
            }else{return null;}    
        }else{return null;}
    }
    /*
     * Función que elimina el path de la imagen viejo, para evitar sobrecarga de contenido
     */
    private function getPathOld($image_id, $DB, $DB_ROOT) {
        try {
            $collection = $this->BBDD->selectDriver($DB,PREFIX.$DB_ROOT, $this->driver);
            $this->BBDD->runDriver(array(
                $this->BBDD->scapeCharts($image_id)
            ), $collection);
            if ($this->BBDD->verifyDriver($collection)) {
                foreach($this->BBDD->fetchDriver($collection) as $src) {
                    return $src->image;
                }
            } else {
                return null;
            }
        } catch (PDOException $ex) {
            return json_encode('Fallo en la conexión con la base de datos' . $ex->getMessage() . ' ' . $ex->getFile() . ' ' . $ex->getLine());
        }
    }
    private function getImageData($product_id) {
        try {
            $productObjectImage = $this->BBDD->selectDriver('product_id = ?',PREFIX.'product', $this->driver);
            $this->BBDD->runDriver(array(
                $this->BBDD->scapeCharts($product_id)
            ), $productObjectImage);
            if($this->BBDD->verifyDriver($productObjectImage)) {
                foreach($this->BBDD->fetchDriver($productObjectImage) as $src) {
                    return $src->image;
                }
            } else {
                return false;
            }
          } catch (Exception $ex) {
            return json_encode('Fallo en la conexión con la base de datos' . $ex->getMessage() . ' ' . $ex->getFile() . ' ' . $ex->getLine());
        }
    }
    protected $BBDD;
    protected $driver;
}

