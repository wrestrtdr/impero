<?php namespace Derive\Orders\Record;

use Derive\Basket\Service\Installments;
use Derive\Orders\Entity\Orders;
use Exception;
use JonnyW\PhantomJs\Client;
use Pckg\Collection;
use Pckg\Concept\Reflect;
use Pckg\Database\Record;
use Pckg\Dynamic\Record\Snippet\RecordActions;
use Pckg\Framework\Config;
use Pckg\Furs\Entity\Furs as FursEntity;
use Pckg\Furs\Service\Furs;
use Pckg\Mail\Service\Mail;

class Order extends Record
{

    use RecordActions;

    protected $entity = Orders::class;

    protected $toArray = ['user', 'packetsSummary'];

    public function getDownloadVoucherUrl()
    {
        return url(
            'derive.orders.voucher.download',
            [
                'order' => $this,
            ]
        );
    }

    public function getPreviewVoucherUrl()
    {
        return url(
            'derive.orders.voucher.preview',
            [
                'order' => $this,
            ]
        );
    }

    public function getRebuyUrl()
    {
        return $this->dt_confirmed != '0000-00-00 00:00:00' || $this->dt_rejected != '0000-00-00 00:00:00' || $this->dt_payed != '0000-00-00 00:00:00' || $this->dt_canceled != '0000-00-00 00:00:00'
            ? null
            : (config("defaults.protocol") . '://' . (config(
                                                          "defaults.domain"
                                                      ) ?? $_SERVER['HTTP_HOST']) . '/estimate/' . $this->hash);
    }

    public function setAppartment($appartment)
    {
        $this->setTag('appartment', $appartment);
    }

    public function setCheckin($checkin)
    {
        $this->setTag('checkin', $checkin);

    }

    public function setPeople($people)
    {
        $this->setTag('people', $people);

    }

    protected function setTag($type, $value)
    {
        $attr = (
        new OrdersTag(
            [
                'type'     => $type,
                'order_id' => $this->id,
            ]
        )
        )->refetch();

        $attr->value = $value;
        $attr->save();
    }

    public function getPacketsSummary()
    {
        $packets = new Collection();
        $this->ordersUsers->each(
            function(OrdersUser $orderUser) use ($packets) {
                if ($orderUser->dt_confirmed) {
                    $packets->push($orderUser->packet);
                }
            }
        );

        $packets = $packets->groupBy('id');

        return implode(
            "<br />",
            array_map(
                function($packetGroup) {
                    return count($packetGroup) . 'x ' . $packetGroup[0]->title;
                },
                $packets->all()
            )
        );
    }

    public function getAdditionsSummary()
    {
        $additions = new Collection();
        $this->ordersUsers->each(
            function(OrdersUser $orderUser) use ($additions) {
                $orderUser->additions->each(
                    function($addition) use ($additions) {
                        $additions->push($addition->addition);
                    }
                );
            }
        );

        $additions = $additions->removeEmpty()->groupBy('id');

        return implode(
            "<br />",
            array_map(
                function($additions) {
                    return count($additions) . 'x ' . $additions[0]->title;
                },
                $additions->all()
            )
        );
    }

    public function getTotalBillsSum()
    {
        return $this->ordersBills->sum(
            function(OrdersBill $bill) {
                return $bill->price;
            }
        );
    }

    public function getPayedBillsSum()
    {
        return $this->ordersBills->sum(
            function(OrdersBill $bill) {
                return $bill->payed;
            }
        );
    }

    public function queueSendVoucher()
    {
        queue()
            ->create('voucher:send --orders ' . $this->id . ' --platform ' . $_SESSION['platform_id'])
            ->makeTimeoutAfterLast('voucher:send', '+5 seconds');
    }

    public function queueGenerateVoucher()
    {
        queue()
            ->create('voucher:generate --orders ' . $this->id . ' --platform ' . $_SESSION['platform_id'])
            ->makeTimeoutAfterLast(
                'voucher:generate',
                '+2 seconds'
            );
    }

    public function queueConfirmFurs()
    {
        queue()->create('furs:confirm --orders ' . $this->id . ' --platform ' . $_SESSION['platform_id'])->makeTimeoutAfterLast('furs:confirm', 1);
    }

    public function sendVoucher()
    {
        $mailer = new Mail();

        $template = view('Derive\Orders:voucherMail', ['order' => $this]);
        $body = $template->autoparse();

        try {
            $sent = $mailer->from('info@hardisland.com', 'HardIsland')
                           ->to($this->user->email, $this->user->name . ' ' . $this->user->surname)
                           ->subject('Your VOUCHER for Hard Island ' . $this->offer->title)
                           ->body($body)
                           ->attach($this->getRelativeVoucherUrl(), 'application/pdf', 'Voucher #' . $this->id . '.pdf')
                           ->send();

            if ($sent) {
                $this->voucher_sent_at = date('Y-m-d H:i:s');
                $this->save();
            }
        } catch (Exception $e) {
            dd(exception($e));
        }
    }

    public function getAbsoluteVoucherUrl()
    {
        return path('storage') . 'derive' . path('ds') . 'voucher' . path('ds') . $this->voucher_url;
    }

    public function getRelativeVoucherUrl()
    {
        return str_replace(path('root'), '', path('storage')) . 'derive' . path('ds') . 'voucher' . path(
            'ds'
        ) . $this->voucher_url;
    }

    public function generateVoucher()
    {
        /**
         * Make a request to frontend.
         */
        $client = Client::getInstance();
        $client->getEngine()->setPath('/usr/local/bin/phantomjs');
        $client->getEngine()->debug(true);
        $request = $client->getMessageFactory()->createPdfRequest(
            url('derive.orders.voucher.preview', ['order' => $this], true),
            'GET'
        );

        /**
         * Save as ...
         */
        $filename = 'order-' . $this->id . '-' . date('YmdHis') . '.pdf';
        $request->setOutputFile(
            path('storage') . 'derive' . path('ds') . 'voucher' . path('ds') . $filename

        );

        /**
         * Set some settings.
         */
        $request->setFormat('A4');
        $request->setOrientation('portrait');
        $request->setMargin(null);

        /**
         * Create response.
         **/
        $response = $client->getMessageFactory()->createResponse();

        /**
         * Run everything.
         */
        $client->send($request, $response);

        $this->voucher_url = $filename;
        $this->save();
    }

    public function getVoucherId()
    {
        // 'SUBSTR(SHA1(CONCAT(SHA1(id), ' ', SHA1(user_id), ' ', SHA1(offer_id))), 16, 10)'
        return substr(
            sha1(sha1($this->id) . ' ' . sha1($this->user_id) . ' ' . sha1($this->offer_id)),
            15,
            10
        );
    }

    public function confirmBillFurs()
    {
        $defaults = context()->get(Config::class)->get('furs');

        /**
         * Create business.
         */
        $business = new Furs\Business(
            $defaults['businessId'],
            $defaults['businessTaxNumber'],
            $defaults['businessValidityDate'],
            $defaults['electronicDeviceId']
        );

        /**
         * Get or create FURS invoice number.
         */
        $fursRecord = (new FursEntity())->getOrCreateFromOrder($this, $business);

        /**
         * Create invoice.
         */
        $invoice = new Furs\Invoice(
            $fursRecord->furs_id,
            number_format($this->getTotalBillsSum(), 2),
            number_format($this->getPayedBillsSum(), 2),
            date('Y-m-d') . 'T' . date('H:i:s')
        );

        /**
         * Configuration
         */
        $certsPath = path('storage') . 'derive' . path('ds') . 'furs' . path('ds') . $defaults['env'] . path('ds');
        $config = new Furs\Config(
            $defaults['taxNumber'],
            $certsPath . $defaults['pemCert'],
            $certsPath . $defaults['p12Cert'],
            $defaults['password'],
            $certsPath . $defaults['serverCert'],
            $defaults['url'],
            $defaults['softwareSupplierTaxNumber']
        );

        /**
         * Create furs object.
         */
        $furs = new Furs($config, $business, $invoice);
        $furs->setTestMode();

        /**
         * Create echo message and throw exception if something is not ok.
         */
        $furs->createEchoMsg();
        $furs->postXML2Furs();

        if ($furs->getEcho() != 'vrni x') {
            throw new Exception('System is misconfigured or FURS not available!');
        }

        /**
         * Create business request.
         */
        $furs->createBusinessMsg();
        $furs->postXML2Furs();

        /**
         * Create invoice request.
         */
        $furs->createInvoiceMsg();
        $furs->postXML2Furs();

        /**
         * Generate QR code.
         */
        // $furs->generateQR();

        /**
         * Set EOR code, which is always the same for same bill.
         */
        if ($eor = $furs->getEOR()) {
            $this->furs_eor = $furs->getEOR();
        }

        /**
         * ZOU changes based on date of confirmation and other properties.
         */
        if ($zoi = $furs->getZOI()) {
            $this->furs_zoi = $furs->getZOI();
            $this->furs_confirmed_at = date('Y-m-d H:i:s');
            $this->furs_num = $defaults['businessId'] . '-' . $defaults['electronicDeviceId'] . '-' . date('Y') . str_pad(
                    $fursRecord->furs_id,
                    4 > strlen($fursRecord->furs_id) ? 4 : strlen($fursRecord->furs_id),
                    '0',
                    STR_PAD_LEFT
                );
        }

        $this->save();
    }

    public function takeVoucher()
    {
        $this->taken_at = date('Y-m-d H:i:s');
        $this->take_comment .= request()->post('comment') . "\n";
        $this->save();
    }

    public function retakeVoucher()
    {
        $this->taken_at = null;
        $this->take_comment .= request()->post('comment') . "\n";
        $this->save();
    }

    /**
     * @return $this
     * @T00D00
     */
    public function setInstallments($number)
    {
        $installments = new Installments();
        $installments->setOrder($this)->redefineTo($number);

        return $this;
    }

}
