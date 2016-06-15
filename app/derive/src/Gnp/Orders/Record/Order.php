<?php namespace Gnp\Orders\Record;

use Gnp\Orders\Console\PhantomJsHtml;
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
                'type' => $type,
                'order_id' => $this->id,
            ]
        )
        )->refetch();

        $attr->value = $value;
        $attr->save();
    }

    public function getPacketsSummary() {
        $packets = $this->packets->removeEmpty()->groupBy('id');

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

        $additions = $additions->groupBy('id');

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
        queue()->create('voucher:send --orders ' . $this->id);
    }

    public function queueGenerateVoucher() {
        queue()->create('voucher:generate --orders ' . $this->id);
    }

    public function sendVoucher() {
        $mailer = new Mail();

        $template = view('Gnp\Orders:voucherMail', ['order' => $this]);

        try {
            $mailer->from('bob@schtr4jh.net', 'Bojan @ Bob')
                   ->to('schtr4jh@schtr4jh.net', 'Bojan Rajh')
                   ->subject('Your VOUCHER for Hard Island Festival is here!')
                   ->body($template->autoparse())
                   ->attach($this->getRelativeVoucherUrl(), 'application/pdf', 'Voucher #' . $this->id)
                   ->send();
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
        $client->getEngine()->setPath('/usr/local/share/phantomjs-2.1.1-linux-i686/bin/phantomjs');
        $client->getEngine()->debug(true);
        $request = $client->getMessageFactory()->createPdfRequest(
            url('derive.orders.voucher.getHtml', ['order' => $this], true),
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

}