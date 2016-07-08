<?php namespace Pckg\Furs\Service;

require_once path('root') . 'vendor/robrichards/xmlseclibs/xmlseclibs.php';
// require_once __CORE_OPENPROF_ROOT__ . 'include/library/phpqrcode/phpqrcode.php';

use DOMDocument;
use DOMXPath;
use Pckg\Furs\Service\Furs\Business;
use Pckg\Furs\Service\Furs\Config;
use Pckg\Furs\Service\Furs\Invoice;
use PHPQRCode\QRcode;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;

// official documentation at
// http://www.datoteke.fu.gov.si/dpr/index.html
class Furs
{

    public $data;

    private $xmlMessage = '';

    private $urlPostHeader = [];

    private $fursResponse;

    private $content2SignIdentifier;

    private $msgIdentifier;

    private $zoi;

    private $eor;

    private $qrDirPath;

    private $xmlsPath;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Business
     */
    protected $business;

    /**
     * @var Invoice
     */
    protected $invoice;

    public function __construct(Config $config, Business $business, Invoice $invoice) {
        $this->config = $config;
        $this->business = $business;
        $this->invoice = $invoice;
        $this->xmlsPath = path('storage') . 'derive' . path('ds') . 'furs' . path('ds') . 'certs' . path('ds');
    }

    public function setTestMode() {
        $this->xmlsPath = path('storage') . 'derive' . path('ds') . 'furs' . path('ds') . 'dev' . path(
                'ds'
            ) . 'xmls' . path('ds');
        $this->qrDirPath = path('storage') . 'derive' . path('ds') . 'furs' . path('ds') . 'dev' . path(
                'ds'
            ) . 'qrcodes' . path('ds');
    }

    public function createEchoMsg() {
        $this->content2SignIdentifier = '';

        $this->urlPostHeader = [
            'Content-Type: text/xml; charset=utf-8',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'SOAPAction: /echo',
        ];
        $dataArray = [
            'name'       => 'soapenv:Envelope',
            'attributes' => [
                'xmlns:soapenv' => 'http://schemas.xmlsoap.org/soap/envelope/',
                'xmlns:fu'      => 'http://www.fu.gov.si/',
            ],
            'childs'     => [
                0 => [
                    'name' => 'soapenv:Header',
                ],
                1 => [
                    'name'   => 'soapenv:Body',
                    'childs' => [
                        0 => [
                            'name'  => 'fu:EchoRequest',
                            'value' => 'vrni x',
                        ],
                    ],
                ],
            ],
        ];

        $this->createXMLMessage($dataArray);
    }

    public function createBusinessMsg() {
        $this->msgIdentifier = 'data';
        $this->content2SignIdentifier = 'fu:BusinessPremiseRequest';

        $this->urlPostHeader = [
            'Content-Type: text/xml; charset=utf-8',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'SOAPAction: /invoices/register',
        ];

        $headerArray = [
            'name'   => 'fu:Header',
            'childs' => [
                0 => [
                    'name'  => 'fu:MessageID',
                    'value' => $this->returnUUID(),
                ],
                1 => [
                    'name'  => 'fu:DateTime',
                    'value' => str_replace(' ', 'T', date('Y-m-d H:i:s')),
                ],
            ],
        ];

        $businessPremiseArray = [
            'name'   => 'fu:BusinessPremise',
            'childs' => [
                0 => [
                    'name'  => 'fu:TaxNumber',
                    'value' => $this->config->getTaxNumber(),
                ],
                1 => [
                    'name'  => 'fu:BusinessPremiseID',
                    'value' => $this->business->getId(),
                ],
                2 => [
                    'name'   => 'fu:BPIdentifier',
                    'childs' => [
                        0 => [
                            'name'  => 'fu:PremiseType',
                            'value' => 'C',
                        ],
                    ],
                ],
                3 => [
                    'name'  => 'fu:ValidityDate',
                    'value' => $this->business->getValidityDate(),
                ],
                4 => [
                    'name'   => 'fu:SoftwareSupplier',
                    'childs' => [
                        0 => [
                            'name'  => 'fu:TaxNumber',
                            'value' => $this->config->getSoftwareSupplierTaxNumber(),
                        ],
                    ],
                ],
            ],
        ];

        $dataArray = [
            'name'       => 'SOAP-ENV:Envelope',
            'attributes' => [
                'xmlns:SOAP-ENV' => 'http://schemas.xmlsoap.org/soap/envelope/',
                'xmlns:fu'       => 'http://www.fu.gov.si/',
                'xmlns:xd'       => 'http://www.w3.org/2000/09/xmldsig#',
            ],
            'childs'     => [
                0 => [
                    'name'   => 'SOAP-ENV:Body',
                    'childs' => [
                        0 => [
                            'name'       => 'fu:BusinessPremiseRequest',
                            'attributes' => [
                                'Id' => $this->msgIdentifier,
                            ],
                            'childs'     => [
                                0 => $headerArray,
                                1 => $businessPremiseArray,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->createXMLMessage($dataArray);
    }

    public function createInvoiceMsg($subsequent = null) {
        $messageID = $this->returnUUID();
        $dateTime = str_replace(' ', 'T', date('Y-m-d H:i:s'));

        if ($subsequent) {
            $subsequentSubmitArray = [
                'name'  => 'fu:SubsequentSubmit',
                'value' => $subsequent, // 1
            ];
        } else {
            $subsequentSubmitArray = [];
        }

        $this->zoi = $this->generateZOI();

        $this->msgIdentifier = $this->invoice->getInvoiceNumber();
        $this->content2SignIdentifier = 'fu:InvoiceRequest';

        $this->urlPostHeader = [
            'Content-Type: text/xml; charset=utf-8',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'SOAPAction: /invoices',
        ];

        $headerInvoice = [
            'name'   => 'fu:Header',
            'childs' => [
                0 => [
                    'name'  => 'fu:MessageID',
                    'value' => $messageID,
                ],
                1 => [
                    'name'  => 'fu:DateTime',
                    'value' => $dateTime,
                ],
            ],
        ];
        $bodyInvoice = [
            'name'   => 'fu:Invoice',
            'childs' => [
                0 => [
                    'name'  => 'fu:TaxNumber',
                    'value' => $this->business->getTaxNumber(),
                ],
                1 => [
                    'name'  => 'fu:IssueDateTime',
                    'value' => $this->invoice->getIssueDateTime(),
                ],
                2 => [
                    'name'  => 'fu:NumberingStructure',
                    'value' => 'B',
                ],
                3 => [
                    'name'   => 'fu:InvoiceIdentifier',
                    'childs' => [
                        0 => [
                            'name'  => 'fu:BusinessPremiseID',
                            'value' => $this->business->getId(),
                        ],
                        1 => [
                            'name'  => 'fu:ElectronicDeviceID',
                            'value' => 1,
                        ],
                        2 => [
                            'name'  => 'fu:InvoiceNumber',
                            'value' => $this->invoice->getInvoiceNumber(),
                        ],
                    ],
                ],
                4 => [
                    'name'  => 'fu:InvoiceAmount',
                    'value' => $this->invoice->getInvoiceAmount(),
                ],
                5 => [
                    'name'  => 'fu:PaymentAmount',
                    'value' => $this->invoice->getPaymentAmount(),
                ],
                6 => [
                    'name' => 'fu:TaxesPerSeller',
                    /*'childs' => [
                        0 => [
                            'name'   => 'fu:VAT',
                            'childs' => [
                                0 => [
                                    'name'  => 'fu:TaxRate',
                                    'value' => '22.0',
                                ],
                                1 => [
                                    'name'  => 'fu:TaxableAmount',
                                    'value' => '0',
                                ],
                                2 => [
                                    'name'  => 'fu:TaxAmount',
                                    'value' => '0',
                                ],
                            ],
                        ],
                    ],*/
                ],
                7 => [
                    'name'  => 'fu:OperatorTaxNumber',
                    'value' => $this->business->getTaxNumber(),
                ],
                8 => [
                    'name'  => 'fu:ProtectedID',
                    'value' => $this->zoi,
                ],
                9 => $subsequentSubmitArray,
            ],
        ];

        $headerArray = [
            'name' => 'soapenv:Header',
        ];
        $headerBody = [
            'name'   => 'soapenv:Body',
            'childs' => [
                0 => [
                    'name'       => 'fu:InvoiceRequest',
                    'attributes' => [
                        'Id' => $this->msgIdentifier,
                    ],
                    'childs'     => [
                        0 => $headerInvoice,
                        1 => $bodyInvoice,
                    ],
                ],
            ],
        ];

        $dataArray = [
            'name'       => 'soapenv:Envelope',
            'attributes' => [
                'xmlns:soapenv' => 'http://schemas.xmlsoap.org/soap/envelope/',
                'xmlns:fu'      => 'http://www.fu.gov.si/',
                'xmlns:xd'      => 'http://www.w3.org/2000/09/xmldsig#',
                'xmlns:xsi'     => 'http://www.w3.org/2001/XMLSchema-instance',
            ],
            'childs'     => [
                0 => $headerArray,
                1 => $headerBody,
            ],
        ];

        $this->createXMLMessage($dataArray);
    }

    private function createXMLMessage($dataArray) {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $child = $this->generateXMLMessageFromArray($dom, $dataArray);
        if ($child) {
            $dom->appendChild($child);
        }
        $dom->formatOutput = true;
        $this->xmlMessage = $dom->saveXML();
    }

    private function generateXMLMessageFromArray($dom, $dataArray) {
        if (empty($dataArray['name'])) {
            return false;
        }

        $element_value = (!empty($dataArray['value'])) ? $dataArray['value'] : null;
        $element = $dom->createElement($dataArray['name'], $element_value);

        if (!empty($dataArray['attributes']) && is_array($dataArray['attributes'])) {
            foreach ($dataArray['attributes'] as $attribute_key => $attribute_value) {
                $element->setAttribute($attribute_key, $attribute_value);
            }
        }

        if (isset($dataArray['childs'])) {
            foreach ($dataArray['childs'] as $data_key => $child_data) {
                if (!is_numeric($data_key)) {
                    continue;
                }

                $child = $this->generateXMLMessageFromArray($dom, $child_data);
                if ($child) {
                    $element->appendChild($child);
                }
            }
        }

        return $element;
    }

    public function generateZOI() {
        /**
         * IssueDateTime in xml scheme is    YYYY-MM-DDTHH:MM:SS
         * IssueDateTime in zoi is           DD.MM.YYYY HH:MM:SS
         */

        $businessPremiseID = '1';
        $electronicDeviceID = '1';
        $newIssueDateTime = date("d.m.Y H:i:s", strtotime($this->invoice->getIssueDateTime()));
        $signData = $this->config->getTaxNumber() . $newIssueDateTime . $this->invoice->getInvoiceNumber(
            ) . $businessPremiseID . $electronicDeviceID . $this->invoice->getInvoiceAmount();

        $key = openssl_pkey_get_private('file://' . $this->config->getPemCert(), $this->config->getPassword());
        openssl_sign($signData, $signature, $key, OPENSSL_ALGO_SHA256);
        openssl_free_key($key);

        return md5($signature);
    }

    function returnUUID() {
        $data = openssl_random_pseudo_bytes(16);
        // in case of PHP 7 use random_bytes
        $data = random_bytes(16);
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    private function returnUUID2() {
        mt_srand(crc32(serialize(microtime(true))));

        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
// 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),

// 16 bits for "time_mid"
            mt_rand(0, 0xffff),

// 16 bits for "time_hi_and_version",
// four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

// 16 bits, 8 bits for "clk_seq_hi_res",
// 8 bits for "clk_seq_low",
// two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

// 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    public function signDocument() {
        if (strlen($this->content2SignIdentifier) == 0) {
            return;
        }
// get content to sign
// get content to sign

        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->loadXML($this->xmlMessage);
        $xpath = new DOMXPath($doc);
        $nodeset = $xpath->query("//$this->content2SignIdentifier")->item(0);

// sign
// sign
        $objXMLSecDSig = new XMLSecurityDSig('');
        $objXMLSecDSig->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);
        $objXMLSecDSig->addReference(
            $nodeset,
            XMLSecurityDSig::SHA256,
            ['http://www.w3.org/2000/09/xmldsig#enveloped-signature'],
            ['id_name' => 'Id', 'uri' => $this->msgIdentifier, 'overwrite' => false]
        );

        openssl_pkcs12_read(file_get_contents($this->config->getP12Cert()), $raw, $this->config->getPassword());

        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, ['type' => 'private']);
        $objKey->loadKey($raw['pkey']);
        $objKey->passphrase = $this->config->getPassword();
        $objXMLSecDSig->sign($objKey, $nodeset);
        $objXMLSecDSig->add509Cert(
            $raw['cert'],
            true,
            false,
            ['issuerSerial' => true, 'subjectName' => true, 'issuerCertificate' => false]
        );

        $objXMLSecDSig->appendSignature($nodeset);
        $this->saveResponse($doc, 'signed');

        $this->xmlMessage = $doc->saveXML();
    }

    public function postXML2Furs() {
        $this->signDocument();

        $conn = curl_init();
        $settings = [
            CURLOPT_URL               => $this->config->getUrl(),
            CURLOPT_FRESH_CONNECT     => true,
            CURLOPT_CONNECTTIMEOUT_MS => 3000,
            CURLOPT_TIMEOUT_MS        => 3000,
            CURLOPT_RETURNTRANSFER    => true,
            CURLOPT_POST              => 1,
            CURLOPT_HTTPHEADER        => $this->urlPostHeader,
            CURLOPT_POSTFIELDS        => $this->xmlMessage,
            CURLOPT_SSL_VERIFYHOST    => 2,
            CURLOPT_SSL_VERIFYPEER    => true,
            CURLOPT_SSLCERT           => $this->config->getPemCert(),
            CURLOPT_SSLCERTPASSWD     => $this->config->getPassword(),
            CURLOPT_CAINFO            => $this->config->getServerCert(),
            CURLOPT_VERBOSE           => true,//dev() ? true : false,
        ];
        curl_setopt_array($conn, $settings);
        $this->fursResponse = curl_exec($conn);

        if ($this->fursResponse) {
            if (isset($this->invoice)) {
                $doc = new DOMDocument('1.0', 'UTF-8');
                $doc->loadXML($this->fursResponse);
                $this->saveResponse($doc, 'generated');

                $xpath = new DOMXPath($doc);
                $nodeset = $xpath->query("//fu:UniqueInvoiceID")->item(0);
                $this->eor = $nodeset->nodeValue ?? null;
            }
        } else {
            var_dump(curl_error($conn));
        }
        curl_close($conn);
    }

    public function getEcho() {
        if ($this->fursResponse) {
            $doc = new DOMDocument('1.0', 'UTF-8');
            $doc->loadXML($this->fursResponse);
            $this->saveResponse($doc, 'generated');

            $xpath = new DOMXPath($doc);
            $nodeset = $xpath->query("//fu:EchoResponse")->item(0);
            return $nodeset->nodeValue ?? null;
        }
    }

    protected function saveResponse($doc, $type) {
        $doc->save(
            $this->xmlsPath . date('Ymdhis') . '_' . substr(sha1($this->msgIdentifier), 0, 6) . '_' . $type . '.xml'
        );
    }

    private function md52dec($hex) {
        $dec = 0;
        $len = strlen($hex);
        for ($i = 1; $i <= $len; $i++) {
            $dec = bcadd($dec, bcmul(strval(hexdec($hex[$i - 1])), bcpow('16', strval($len - $i))));
        }

        return $dec;
    }

    public function generateQR() {
        if (!isset($this->invoice)) {
            return;
        }

// QR code is made of:
// 39 chars of decimal ZOI code
// 8  chars of company's tax num
// 12 chars of invoice's date & time
// 1  char is a control number
// ZOI decimal number
// ZOI decimal number

        $zoiDecimal = $this->md52dec($this->zoi);

        $zeros2Add = 39 - strlen($zoiDecimal);
        for ($i = 0; $i < $zeros2Add; $i++) {
            $zoiDecimal = '0' . $zoiDecimal;
        }

        $tmpNum = explode('T', $this->invoice->getIssueDateTime());
        $tmpDate = explode('-', $tmpNum[0]);

        $dateTimeNumber = substr($tmpDate[0], 2);
        $dateTimeNumber .= $tmpDate[1];
        $dateTimeNumber .= $tmpDate[2];
        $dateTimeNumber .= $tmpNum[1];
        $dateTimeNumber = str_replace(':', '', $dateTimeNumber);

        $invoice_year = $tmpDate[0];

        $qrCode = $zoiDecimal . $this->config->getTaxNumber() . $dateTimeNumber;
        $controlChar = array_sum(str_split($qrCode)) % 10;

        $qrCode = $qrCode . $controlChar;
        QRcode::png(
            $qrCode,
            $this->qrDirPath . $this->invoice->getInvoiceNumber() . '-' . date('Ymdhis') . '.png'
        );
    }

    public function getZOI() {
        return $this->zoi;
    }

    public function getEOR() {
        return $this->eor;
    }

    public function getFURSResponse() {
        return $this->fursResponse;
    }

    public function echoXML() {
        $this->signDocument();

        header('Content-Type: text/xml; charset=utf-8', true);
        echo $this->xmlMessage;
    }
}