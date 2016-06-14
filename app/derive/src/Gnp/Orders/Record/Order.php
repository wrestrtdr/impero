<?php namespace Gnp\Orders\Record;

use Gnp\Orders\Console\PhantomJsHtml;
use Gnp\Orders\Entity\Orders;
use JonnyW\PhantomJs\Client;
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
                'type'     => $type,
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

    public function sendVoucher() {
        $mailer = new Mail();

        try {
            $mailer->from('Bojan @ Bob @ GNP <bob@schtr4jh.net>')
                   ->to('Bojan Rajh <schtr4jh@schtr4jh.net>')
                   ->subject('Test subject čšžČŠŽ')
                   ->body('<p><b>HTML</b> body čšžČŠŽ</p>')
                   ->plainBody('Plain body')
                   ->attach('storage/impero/virtualhosts.conf', 'application/pdf', 'Voucher #' . $this->id)
                   ->send();
        } catch (\Exception $e) {
            dd(exception($e));
        }
    }

    public function generateVoucher() {
        /**
         * Make a request to frontend.
         */
        $client = Client::getInstance();
        $request = $client->getMessageFactory()->createPdfRequest(
            url('derive.orders.voucher.getHtml', ['order' => $this], true),
            'GET'
        );

        /**
         * Save as ...
         */
        $request->setOutputFile(
            path('storage') . 'derive' . path('ds') . 'voucher' . path('ds') . 'order-' . $this->id . '-' . date(
                'YmdHis'
            ) . '.pdf'

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
    }

    public function getVoucherId() {
        return substr(
            config('security.hash') . sha1(sha1($this->id) . ' ' . sha1($this->user_id) . ' ' . sha1($this->offer_id)),
            15,
            10
        );
    }

}