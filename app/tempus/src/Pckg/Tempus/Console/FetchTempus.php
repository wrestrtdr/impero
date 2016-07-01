<?php namespace Pckg\Tempus\Console;

use Pckg\Framework\Console\Command;
use Pckg\Tempus\Record\Item;

class FetchTempus extends Command
{

    protected function configure() {
        parent::configure();

        $this->setName('tempus:fetch')
             ->setDescription('Fetch currently active window info');
    }

    public function handle() {
        $interval = 2;
        while(true) {
            $this->output('Fetching window info: ' . date('Y-m-d H:i:s'));

            /**
             * Execute some commands.
             */
            $outputs = $this->exec(
                [
                    'xprop -id $(xprop -root 32x \'\t$0\' _NET_ACTIVE_WINDOW | cut -f 2)',
                    'xprintidle',
                ],
                false
            );

            /**
             * Search for program, name and role.
             */
            $program = null;
            $name = null;
            $role = null;
            foreach ($outputs[0] as $line) {
                if (strpos($line, 'WM_CLASS(STRING)') === 0) {
                    $program = $line;
                } elseif (strpos($line, 'WM_NAME(STRING)') === 0) {
                    $name = $line;
                } elseif (strpos($line, 'WM_WINDOW_ROLE(STRING)') === 0) {
                    $role = $line;
                }
            }

            /**
             * Fetch idle time.
             */
            $idle = $outputs[1][0];

            /**
             * Create new item.
             */
            (
            new Item(
                [
                    'program'    => $program,
                    'name'       => $name,
                    'role'       => $role,
                    'idle'       => $idle,
                    'created_at' => date('Y-m-d H:i:s'),
                ]
            )
            )->save();

            $this->output('Window info fetched.');
            sleep($interval);
        }
    }

}