<?php
namespace Kushki;
use kushki\lib\Amount;
use kushki\lib\Kushki;
use kushki\lib\KushkiEnvironment;
use kushki\lib\Transaction;
use kushki\lib\ExtraTaxes;
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type");
header("Access-Control-Allow-Methods", "POST, GET, PUT, DELETE, OPTIONS");
require_once '../../models/config.php';
require_once '../../models/connection.php';
require_once '../kushki/autoload.php';

if ($_POST) {
    if (isset($_POST['kushkiToken']) && (!empty($_POST['kushkiToken']))) {
        if (isset($_POST['totalAmount']) && (!empty($_POST['totalAmount']))) {
            $paymentGateway = new PaymentRequest(); // Creamos un objeto de pago
            $payload = $paymentGateway->createPaymentObject(); // Crearmos la solicitud de pago
            $getBBDDSumCart = $paymentGateway->sumCart($_POST['customer']);
            // Verificamos si el front fue manipulado buscando data de la db
            if ($getBBDDSumCart != 0) {
                if ($getBBDDSumCart == $_POST['totalAmount']) {
                    $payment = $payload->charge($_POST['kushkiToken'], $paymentGateway->chargePaymentGateway());
                    if ($payment->isSuccessful()) {
                         if ($paymentGateway->removeCart($_POST['customer'])) {
                            if ($paymentGateway->generateOrder()) {
                               header("location: http://www.ragazzashop.com/orders.php"); 
                            }   
                        }
                    } else {
                        // echo "Error " . $transaccion->getResponseCode() . ": " . $transaccion->getResponseText();
                        $response = array();
                        $response['status'] = false;
                        $response['statusCode'] = 400;
                        $response['message'] = 'No pudimos procesar tu pago';
                        $response['text'] = $payment->getResponseText();
                        $response['code'] = $payment->getResponseCode();
                        echo json_encode($response);
                    }
                } else {
                    $response = array();
                    $response['status'] = false;
                    $response['statusCode'] = 400;
                    $response['message'] = 'El monto es invalido';
                    echo json_encode($response);
                }
            } else {
                    $response = array();
                    $response['status'] = false;
                    $response['statusCode'] = 400;
                    $response['message'] = 'Fallo en las credenciales';
                    echo json_encode($response);
            }
        }
    }
}
class PaymentRequest {
        public function __construct() {
        $this->BBDD = new \dbDriver;
        $this->driver = $this->BBDD->setPDO();
        $this->BBDD->setPDOConfig($this->driver);
    }
    public function createPaymentObject() {
        $merchantId = "1000000363945473759415366872700";
        $language = \kushki\lib\KushkiLanguage::ES;
        $currency = \kushki\lib\KushkiCurrency::USD;
        $environment = \kushki\lib\KushkiEnvironment::TESTING;
        $kushki = new Kushki($merchantId, $language, $currency, $environment);
        return $kushki;
    }
    public function chargePaymentGateway() {
        $amount = new Amount($_POST['totalAmount'], 0, 0, 0);
        return $amount;
    }
    public function sumCart($customer_id) {
        $sumCart = $this->BBDD->sumDriver('cart_client_id=?',PREFIX."carrito",$this->driver,"cart_price");
        $this->BBDD->runDriver(array(
            $this->BBDD->scapeCharts($customer_id)
        ), $sumCart);
        if($this->BBDD->verifyDriver($sumCart))
        {
            foreach ($this->BBDD->fetchDriver($sumCart) as $tot) {
                return $tot->total;
            }
        }else{
            return 0;
        }
    }
    public function removeCart($customer_id) {
        try {
            $cart = $this->BBDD->deleteDriver('cart_client_id = ?', PREFIX.'carrito', $this->driver);
            $this->BBDD->runDriver(array(
                $this->BBDD->scapeCharts($customer_id)
            ), $cart);
            return true;
        } catch (PDOException $ex) {
            exit('failure to connect with database server');
        }
    }
    public function generateOrder() {
        // Generamos una orden por cada producto, por si son de distintas empresas
        $time = time();
        $date = date("Y-m-d ", $time);
        $itemOrder = json_decode($_POST['custom_field']);
        foreach ($itemOrder as $item) {
                 $fields = 'user_id, invoice_no, invoice_prefix, store_id, store_name
                   store_url, customer_id, customer_group_id, firstname,
                   lastname, email, telephone, fax, custom_field, payment_firstname,
                   payment_lastname, payment_company, payment_address_1, payment_address_2,
                   payment_city, payment_postcode, payment_country, payment_country_id, 
                   payment_zone, payment_zone_id, payment_address_format, payment_custom_field,
                   payment_method, payment_code, shipping_firstname, shipping_lastname, shipping_address_1,
                   shipping_address_2, shipping_city, shipping_postcode, shipping_country, shipping_country_id,
                   shipping_zone, shipping_zone_id, shipping_method, total, currency_id, date_added
                   '; // 43
        $sql = '?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?'; // 43   
      try {
            $invoice = $this->BBDD->insertDriver($sql,PREFIX.'order',$this->driver, $fields);
            $this->BBDD->runDriver(array(
                $this->BBDD->scapeCharts($_POST['customer_id']),
                $this->BBDD->scapeCharts(rand(5, 15)),
                $this->BBDD->scapeCharts('-'),
                $this->BBDD->scapeCharts($item->car_product->user_id),
                $this->BBDD->scapeCharts($item->store->shop_name),
                $this->BBDD->scapeCharts('http://ragazzashop.com/empresas/#/login'),
                $this->BBDD->scapeCharts($_POST['customer_id']),
                $this->BBDD->scapeCharts(3),
                $this->BBDD->scapeCharts($_POST['firstname']),
                $this->BBDD->scapeCharts($_POST['lastname']),
                $this->BBDD->scapeCharts(''),
                $this->BBDD->scapeCharts($_POST['telephone']),
                $this->BBDD->scapeCharts($_POST['telephone']),
                $this->BBDD->scapeCharts(json_encode($item->cart_product)),
                $this->BBDD->scapeCharts($_POST['payment_firstname']),
                $this->BBDD->scapeCharts($_POST['payment_lastname']),
                $this->BBDD->scapeCharts($_POST['payment_company']),
                $this->BBDD->scapeCharts($_POST['payment_address_1']),
                $this->BBDD->scapeCharts($_POST['payment_address_2']),
                $this->BBDD->scapeCharts($_POST['payment_city']),
                $this->BBDD->scapeCharts($_POST['payment_postcode']),
                $this->BBDD->scapeCharts($_POST['payment_country']),
                $this->BBDD->scapeCharts($_POST['payment_country_id']),
                $this->BBDD->scapeCharts($_POST['payment_zone']),
                $this->BBDD->scapeCharts($_POST['payment_zone_id']),
                $this->BBDD->scapeCharts($_POST['payment_address_format']),
                $this->BBDD->scapeCharts($_POST['payment_custom_field']),
                $this->BBDD->scapeCharts($_POST['payment_method']),
                $this->BBDD->scapeCharts($_POST['payment_code']),
                $this->BBDD->scapeCharts($_POST['shipping_firstname']),
                $this->BBDD->scapeCharts($_POST['shipping_lastname']),
                $this->BBDD->scapeCharts($_POST['shipping_address_1']),
                $this->BBDD->scapeCharts($_POST['shipping_address_2']),
                $this->BBDD->scapeCharts($_POST['shipping_city']),
                $this->BBDD->scapeCharts($_POST['shipping_postcode']),
                $this->BBDD->scapeCharts($_POST['shipping_country']),
                $this->BBDD->scapeCharts($_POST['shipping_country_id']),
                $this->BBDD->scapeCharts($_POST['shipping_zone']),
                $this->BBDD->scapeCharts($_POST['shipping_zone_id']),
                $this->BBDD->scapeCharts($_POST['shipping_method']),
                $this->BBDD->scapeCharts($_POST['total']),
                $this->BBDD->scapeCharts($_POST['currency_id']),
                $this->BBDD->scapeCharts($date),
            ), $invoice);
        } catch (PDOException $ex) {
             exit('failure to connect with database server'); 
        }
       }
       return true;
    }
    protected $BBDD;
    protected $driver;
}
        /*
         * /*
 * {
cart_id: "59",
cart_code: "bd5d4e031162f21d4b198de065865181",
cart_product: {
user_id: "34",
product_id: "98",
model: "CAR002",
sku: "",
upc: "",
ean: "",
jan: "",
isbn: "",
mpn: "",
location: "",
quantity: "2",
stock_status_id: "7",
image: "catalog/ropa/20180526_092206.jpg",
manufacturer_id: "0",
shipping: "1",
price: "120.0000",
points: "0",
tax_class_id: "0",
date_available: "2018-09-05",
weight: "0.00000000",
weight_class_id: "1",
length: "0.00000000",
width: "0.00000000",
height: "0.00000000",
length_class_id: "1",
subtract: "1",
minimum: "1",
sort_order: "1",
status: "1",
viewed: "0",
date_added: "2018-09-04 18:51:25",
date_modified: "2018-09-29 08:25:53"
},
cart_price: "120",
cart_client_id: "23",
quantity: "1",
date: "2018-09-29 ",
option: {
user_id: "0",
product_id: "98",
language_id: "1",
name: "CARDIGAN CREMA",
description: "<p>CARDIGAN COLOR CREMA CON FILO MOSTAZA Y CORREA PARA FORMAR UNA CINTURA ENVIDIABLE</p>",
tag: "",
meta_title: "CARDIGAN CREMA",
meta_description: "",
meta_keyword: ""
},
store: {
user_id: "34",
customer_group_id: "2",
shop_name: "test",
shop_address: "altragracia",
shop_email: "test@hotmail.com",
shop_phone: "1215",
shop_password: "$2y$12$9AQleba6bXcaYr5W8APpw./d4yVJ9nLuhD7tDq8LR7jttr34yiNjS",
shop_image: "catalog/profile/4c8fc94fe7cb75668c5e938cf1522906.jpg",
status: "0",
shop_date_added: "2018-06-23 "
}
},
         */