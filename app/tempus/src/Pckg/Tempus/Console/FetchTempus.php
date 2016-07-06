<?php namespace Pckg\Tempus\Console;

use Pckg\Framework\Console\Command;
use Pckg\Tempus\Entity\Items;
use Pckg\Tempus\Record\Item;

class FetchTempus extends Command
{

    protected function configure() {
        parent::configure();

        $this->setName('tempus:fetch')
             ->setDescription('Fetch currently active window info');
    }

    public function handle() {
        $interval = 1 * 1000 * 1000;
        $prev = (new Items())->orderBy('created_at DESC')->one();

        while (true) {
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
                    $program = substr($line, strlen('WM_CLASS(STRING) = '));

                } elseif (strpos($line, 'WM_NAME(STRING)') === 0) {
                    $name = substr($line, strlen('WM_NAME(STRING) = '));

                } elseif (strpos($line, 'WM_NAME(UTF8_STRING)') === 0) {
                    $name = substr($line, strlen('WM_NAME(UTF8_STRING) = '));

                } elseif (strpos($line, 'WM_WINDOW_ROLE(STRING)') === 0) {
                    $role = substr($line, strlen('WM_WINDOW_ROLE(STRING) = '));

                }
            }

            /**
             * Fetch idle time.
             */
            $idle = $outputs[1][0];
            $date = date('Y-m-d H:i:s');

            if ($idle > 2 * 60 * 1000) {
                if ($prev) {
                    /**
                     * Update old item.
                     */
                    $prev->finished_at = date('Y-m-d H:i:s');
                    $prev->duration = strtotime($date) - strtotime($prev->created_at);
                    $prev->idle = $idle;
                    $prev->save();
                }

            } elseif ($program != $prev->program || $name != $prev->name) {
                /**
                 * Create new item.
                 */
                $new = (
                new Item(
                    [
                        'program'    => $program,
                        'name'       => $name,
                        'role'       => $role,
                        'idle'       => $idle,
                        'created_at' => $date,
                    ]
                )
                );
                $new->save();

                if ($prev && $prev->idle <= 2 * 60 * 1000) {
                    /**
                     * Update old item.
                     */
                    $prev->finished_at = date('Y-m-d H:i:s');
                    $prev->duration = strtotime($date) - strtotime($prev->created_at);
                    $prev->save();
                }

                /**
                 * replace
                 */
                $prev = $new;
            }

            $this->output('Window info fetched.');
            usleep($interval);
        }
    }

}