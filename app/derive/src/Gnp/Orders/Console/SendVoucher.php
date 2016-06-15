<?php namespace Gnp\Orders\Console;

use Gnp\Orders\Entity\Orders;
use Pckg\Framework\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class SendVoucher extends Command
{

    protected function configure() {
        $this->setName('voucher:send')
             ->setDescription('Send voucher')
             ->addOptions(
                 [
                     'orders' => 'Order IDs',
                 ],
                 InputOption::VALUE_REQUIRED
             );
    }

    public function handle(Orders $orders) {
        $arrOrders = $orders->where('id', $this->option('orders'))->all();

        foreach ($arrOrders as $i => $order) {
            $this->output('Sending voucher email for order #' . $order->id);
            $order->sendVoucher();
            $this->output('Voucher for email #' . $order->id . ' sent');
        }
    }

}