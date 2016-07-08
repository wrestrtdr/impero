<?php namespace Pckg\Furs\Console;

use Derive\Orders\Entity\Orders;
use Derive\Platform\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class FursConfirmation extends Command
{

    protected function configure()
    {
        parent::configure();

        $this->setName('furs:confirm')
             ->setDescription('Confirm bill on FURS')
             ->addOptions(
                 [
                     'orders' => 'Order IDs',
                 ],
                 InputOption::VALUE_REQUIRED
             );
    }

    public function handle(Orders $orders)
    {
        $arrOrders = $orders->where('id', $this->option('orders'))->all();

        foreach ($arrOrders as $i => $order) {
            $this->output('Confirming bill for order #' . $order->id);
            $order->confirmBillFurs();
            $this->output('Bill for order #' . $order->id . ' confirmed');
        }
    }

}