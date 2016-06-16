<?php namespace Gnp\Orders\Console;

use Gnp\Orders\Entity\Orders;
use Pckg\Framework\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class GenerateVoucher extends Command
{

    protected function configure() {
        $this->setName('voucher:generate')
             ->setDescription('Generates voucher')
             ->addOptions(
                 [
                     'orders' => 'Order IDs',
                 ],
                 InputOption::VALUE_REQUIRED
             )->addOptions(
                [
                    'send' => 'Should we send an email?',
                ],
                InputOption::VALUE_OPTIONAL
            );
    }

    public function handle(Orders $orders) {
        $arrOrders = $orders->where('id', $this->option('orders'))->all();

        foreach ($arrOrders as $i => $order) {
            $this->output('Generating voucher for order #' . $order->id);
            $order->generateVoucher();
            $this->output('Voucher for order #' . $order->id . ' generated');

            if ($this->option('send')) {
                $this->output('Sending voucher email for order #' . $order->id);
                $order->sendVoucher();
                $this->output('Voucher for email #' . $order->id . ' sent');
            }
        }
    }

}