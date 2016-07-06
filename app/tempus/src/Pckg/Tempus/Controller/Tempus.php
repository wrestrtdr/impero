<?php namespace Pckg\Tempus\Controller;

use Pckg\Collection;
use Pckg\Framework\Controller;
use Pckg\Maestro\Helper\Maestro;
use Pckg\Tempus\Entity\Items;
use Pckg\Tempus\Record\Item;

class Tempus extends Controller
{

    use Maestro;

    public function getHomeAction(Items $items) {
        $items = $items->orderBy('created_at DESC')
                       ->where('finished_at')
                       ->all();
        $collection = new Collection();

        $items->each(
            function(Item $item) {
                /**
                 * @T00D00 - This needs to be added as filter on frontend!
                 */
                if ($item->idle > 2 * 60 * 1000) {
                    $item->program = 'IDLE';
                } else {
                    if (strpos($item->program, 'chrome') || strpos($item->program, 'chromium')) {
                        $item->program = 'Google Chrome';
                    } elseif (strpos($item->program, 'terminal')) {
                        $item->program = 'Terminal';
                    } elseif (strpos($item->program, 'skype')) {
                        $item->program = 'Skype';
                    } elseif (strpos($item->program, 'geary')) {
                        $item->program = 'Geary (email)';
                    } elseif (strpos($item->program, 'jetbrains')) {
                        $item->program = 'PHP Storm';
                    } elseif (strpos($item->program, 'libre')) {
                        $item->program = 'Office';
                    } elseif (strpos($item->program, 'gnome') || strpos($item->program, 'navigator') || strpos(
                            $item->program,
                            'Navigator'
                        ) || strpos($item->program, 'nautilus') || strpos(
                                  $item->program,
                                  'unity'
                              ) || strpos($item->program, 'desktop')
                    ) {
                        $item->program = 'System';
                    } elseif (!$item->program) {
                        $item->program = '-- not set --';
                    }
                }
            }
        );

        return view(
            'home',
            [
                'items' => $items,
            ]
        );

        $groups = [];
        $all = $items->active()
                     ->joinPrevItem()
                     ->joinNextItem()
            /*->where(
                (new Raw())->where('items.name', '%gnp%', 'LIKE')
                           ->orWhere('items.name', '%gonparty%', 'LIKE')
                           ->orWhere('items.name', '%gonparty%', 'LIKE')
                           ->orWhere('items.name', '%@bob%', 'LIKE')
                           ->orWhere('items.name', '%hard island%', 'LIKE')
                           ->orWhere('items.name', '%hardisland%', 'LIKE')
                           ->orWhere('items.name', '%bob.pckg%', 'LIKE')
                           ->orWhere('items.name', '%\/www\/%', 'LIKE')
                           ->orWhere('items.name', '%dev.php%', 'LIKE')
            )*/
                     ->where()
            //->where('idle', 2 * 60 * 1000, '<')
                     ->orderBy('items.created_at')
                     ->limit(200)
                     ->all();

        $collection = new Collection();
        $prev = null;
        $all->each(
            function($item) use (&$prev) {
                if ($prev) {
                    $prev->setRelation('next', $item);
                    $item->setRelation('prev', $prev);
                }
                $prev = $item;
            }
        );

        return $this->tabelize($items, [], 'Tempus')
                    ->setEntityActions([])
                    ->setRecordActions([])
                    ->setRecords($all)
                    ->setGroups($groups)
                    ->setFields(
                        [
                            'id',
                            'program',
                            'name',
                            'created_at',
                            'duration' => function($item) use (&$prev) {
                                if ($item->hasRelation('prev') && $item->hasRelation('next')) {
                                    return get_date_diff($item->prev->created_at, $item->next->created_at);
                                }
                            },
                        ]
                    );
    }

}

function get_date_diff($time1, $time2, $precision = 2) {
    // If not numeric then convert timestamps
    if (!is_int($time1)) {
        $time1 = strtotime($time1);
    }
    if (!is_int($time2)) {
        $time2 = strtotime($time2);
    }
    // If time1 > time2 then swap the 2 values
    if ($time1 > $time2) {
        list($time1, $time2) = [$time2, $time1];
    }
    // Set up intervals and diffs arrays
    $intervals = ['year', 'month', 'day', 'hour', 'minute', 'second'];
    $diffs = [];
    foreach ($intervals as $interval) {
        // Create temp time from time1 and interval
        $ttime = strtotime('+1 ' . $interval, $time1);
        // Set initial values
        $add = 1;
        $looped = 0;
        // Loop until temp time is smaller than time2
        while ($time2 >= $ttime) {
            // Create new temp time from time1 and interval
            $add++;
            $ttime = strtotime("+" . $add . " " . $interval, $time1);
            $looped++;
        }
        $time1 = strtotime("+" . $looped . " " . $interval, $time1);
        $diffs[$interval] = $looped;
    }
    $count = 0;
    $times = [];
    foreach ($diffs as $interval => $value) {
        // Break if we have needed precission
        if ($count >= $precision) {
            break;
        }
        // Add value and interval if value is bigger than 0
        if ($value > 0) {
            if ($value != 1) {
                $interval .= "s";
            }
            // Add value and interval to times array
            $times[] = $value . " " . $interval;
            $count++;
        }
    }

    // Return string with times
    return implode(", ", $times);
}