<?php namespace Gnp\Orders\Record;

use Gnp\Orders\Entity\Orders;
use JonnyW\PhantomJs\Client;
use Pckg\Collection;
use Pckg\Concept\Reflect;
use Pckg\Database\Record;
use Pckg\Mail\Service\Mail;

class Order extends Record
{

    protected $entity = Orders::class;

    protected $toArray = ['user', 'packetsSummary'];

    public function getEditUrl() {
        return '#';
    }

    public function getDeleteUrl() {
        return '#';
    }

    public function getDownloadVoucherUrl() {
        return url(
            'derive.orders.voucher.download',
            [
                'order' => $this,
            ]
        );
    }

    public function getPreviewVoucherUrl() {
        return url(
            'derive.orders.voucher.preview',
            [
                'order' => $this,
            ]
        );
    }

    public function getRebuyUrl() {
        return $this->dt_confirmed != '0000-00-00 00:00:00' || $this->dt_rejected != '0000-00-00 00:00:00' || $this->dt_payed != '0000-00-00 00:00:00' || $this->dt_canceled != '0000-00-00 00:00:00'
            ? null
            : (config("defaults.protocol") . '://' . (config(
                                                          "defaults.domain"
                                                      ) ?? $_SERVER['HTTP_HOST']) . '/estimate/' . $this->hash);
    }

    public function setAppartment($appartment) {
        $this->setTag('appartment', $appartment);
    }

    public function setCheckin($checkin) {
        $this->setTag('checkin', $checkin);

    }

    public function setPeople($people) {
        $this->setTag('people', $people);

    }

    protected function setTag($type, $value) {
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

    public function getPacketsSummary() {
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

    public function getAdditionsSummary() {
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

    public function queueSendVoucher() {
        queue()->create('voucher:send --orders ' . $this->id)->makeTimeoutAfterLast('voucher:send', '+5 seconds');
    }

    public function queueGenerateVoucher() {
        queue()->create('voucher:generate --orders ' . $this->id)->makeTimeoutAfterLast(
            'voucher:generate',
            '+2 seconds'
        );
    }

    public function sendVoucher() {
        $mailer = new Mail();

        $template = view('Gnp\Orders:voucherMail', ['order' => $this]);
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
        } catch (\Exception $e) {
            dd(exception($e));
        }
    }

    public function getAbsoluteVoucherUrl() {
        return path('storage') . 'derive' . path('ds') . 'voucher' . path('ds') . $this->voucher_url;
    }

    public function getRelativeVoucherUrl() {
        return str_replace(path('root'), '', path('storage')) . 'derive' . path('ds') . 'voucher' . path(
            'ds'
        ) . $this->voucher_url;
    }

    public function generateVoucher() {
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

    public function getVoucherId() {
        return substr(
            config('security.hash') . sha1(sha1($this->id) . ' ' . sha1($this->user_id) . ' ' . sha1($this->offer_id)),
            15,
            10
        );
    }

    public function confirmBillFurs() {
        die("confirming");
    }

}
